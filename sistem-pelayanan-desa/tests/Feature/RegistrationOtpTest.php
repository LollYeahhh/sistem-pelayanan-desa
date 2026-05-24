<?php

namespace Tests\Feature;

use App\Models\RegistrationOtp;
use App\Models\User;
use App\Notifications\AdminNewUserActivationNotification;
use App\Notifications\RegistrationOtpNotification;
use App\Services\RegistrationOtpService;
use Exception;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_email_otp_and_keeps_account_pending(): void
    {
        Notification::fake();
        $admin = $this->createUser([
            "email" => "admin@example.test",
            "role" => "admin",
        ]);

        $response = $this->post(route("register"), $this->registrationPayload());

        $response->assertRedirect(route("register.otp"));

        $user = User::where("email", "warga@example.test")->firstOrFail();

        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->is_verified);
        $this->assertSame("nonaktif", $user->status_akun);
        $this->assertDatabaseHas("registration_otps", [
            "user_id" => $user->id,
            "email" => $user->email,
            "attempts" => 0,
            "verified_at" => null,
        ]);

        Notification::assertSentTo($user, RegistrationOtpNotification::class);
        Notification::assertNotSentTo($admin, AdminNewUserActivationNotification::class);
    }

    public function test_correct_otp_verifies_email_and_notifies_admin(): void
    {
        Notification::fake();
        $admin = $this->createUser([
            "email" => "admin@example.test",
            "role" => "admin",
        ]);

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();
        $otp = $this->captureOtpCode($user);

        $response = $this->post(route("register.otp.verify"), [
            "otp" => $otp,
        ]);

        $response->assertRedirect(route("login"));

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse($user->is_verified);
        $this->assertSame("nonaktif", $user->status_akun);
        $this->assertNotNull(
            RegistrationOtp::where("user_id", $user->id)->firstOrFail()->verified_at,
        );

        Notification::assertSentTo($admin, AdminNewUserActivationNotification::class);
    }

    public function test_wrong_otp_increments_attempts(): void
    {
        Notification::fake();

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();

        $response = $this->post(route("register.otp.verify"), [
            "otp" => "000000",
        ]);

        $response->assertSessionHasErrors("otp");

        $this->assertDatabaseHas("registration_otps", [
            "user_id" => $user->id,
            "attempts" => 1,
            "verified_at" => null,
        ]);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_expired_otp_is_rejected(): void
    {
        Notification::fake();

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();
        $otp = $this->captureOtpCode($user);

        RegistrationOtp::where("user_id", $user->id)->update([
            "expires_at" => now()->subMinute(),
        ]);

        $response = $this->post(route("register.otp.verify"), [
            "otp" => $otp,
        ]);

        $response->assertSessionHasErrors("otp");
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_otp_is_locked_after_too_many_failed_attempts(): void
    {
        Notification::fake();

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();
        $otp = $this->captureOtpCode($user);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route("register.otp.verify"), [
                "otp" => "000000",
            ])->assertSessionHasErrors("otp");
        }

        $response = $this->post(route("register.otp.verify"), [
            "otp" => $otp,
        ]);

        $response->assertSessionHasErrors("otp");
        $this->assertDatabaseHas("registration_otps", [
            "user_id" => $user->id,
            "attempts" => 5,
            "verified_at" => null,
        ]);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_otp_respects_cooldown_and_creates_new_code(): void
    {
        Notification::fake();

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();
        $oldOtp = RegistrationOtp::where("user_id", $user->id)->firstOrFail();

        $this->post(route("register.otp.resend"))
            ->assertSessionHasErrors("resend");

        $oldOtp->update([
            "last_sent_at" => now()->subSeconds(RegistrationOtpService::RESEND_COOLDOWN_SECONDS + 1),
        ]);

        $this->post(route("register.otp.resend"))
            ->assertSessionHas("success");

        $this->assertDatabaseMissing("registration_otps", [
            "id" => $oldOtp->id,
        ]);
        $this->assertDatabaseHas("registration_otps", [
            "user_id" => $user->id,
            "email" => $user->email,
            "attempts" => 0,
            "verified_at" => null,
        ]);
    }

    public function test_resend_otp_mail_failure_returns_validation_error_and_preserves_existing_code(): void
    {
        Notification::fake();

        $this->post(route("register"), $this->registrationPayload());
        $user = User::where("email", "warga@example.test")->firstOrFail();
        $oldOtp = RegistrationOtp::where("user_id", $user->id)->firstOrFail();

        $oldOtp->update([
            "last_sent_at" => now()->subSeconds(RegistrationOtpService::RESEND_COOLDOWN_SECONDS + 1),
        ]);

        $this->app->instance(
            NotificationDispatcher::class,
            new class implements NotificationDispatcher {
                public function send($notifiables, $notification): void
                {
                    throw new Exception("SMTP authentication failed.");
                }

                public function sendNow($notifiables, $notification, ?array $channels = null): void
                {
                    throw new Exception("SMTP authentication failed.");
                }
            },
        );

        $this->post(route("register.otp.resend"))
            ->assertSessionHasErrors("resend");

        $this->assertDatabaseHas("registration_otps", [
            "id" => $oldOtp->id,
            "user_id" => $user->id,
            "verified_at" => null,
        ]);
    }

    public function test_login_redirects_unverified_email_user_to_otp_page(): void
    {
        Notification::fake();
        $user = $this->createUser([
            "email" => "pending@example.test",
            "email_verified_at" => null,
            "is_verified" => false,
            "status_akun" => "nonaktif",
        ]);

        $response = $this->post(route("login"), [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertRedirect(route("register.otp"));
        $this->assertGuest();
        Notification::assertSentTo($user, RegistrationOtpNotification::class);
    }

    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            "nama_lengkap" => "Warga Baru",
            "email" => "warga@example.test",
            "no_hp" => "081234567890",
            "password" => "password123",
            "password_confirmation" => "password123",
        ], $overrides);
    }

    private function createUser(array $overrides = []): User
    {
        static $sequence = 1;

        $user = User::create(array_merge([
            "nama_lengkap" => "User {$sequence}",
            "nik" => str_pad((string) $sequence, 16, "0", STR_PAD_LEFT),
            "email" => "user{$sequence}@example.test",
            "email_verified_at" => now(),
            "password" => "password",
            "role" => "warga",
            "status_akun" => "aktif",
            "is_verified" => true,
        ], $overrides));

        $sequence++;

        return $user;
    }

    private function captureOtpCode(User $user): string
    {
        $code = null;

        Notification::assertSentTo(
            $user,
            RegistrationOtpNotification::class,
            function (RegistrationOtpNotification $notification) use (&$code) {
                $code = $notification->code;

                return true;
            },
        );

        $this->assertNotNull($code);

        return $code;
    }
}
