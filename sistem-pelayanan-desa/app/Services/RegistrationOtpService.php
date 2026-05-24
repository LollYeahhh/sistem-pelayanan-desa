<?php

namespace App\Services;

use App\Models\RegistrationOtp;
use App\Notifications\RegistrationOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class RegistrationOtpService
{
    public const EXPIRES_IN_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_COOLDOWN_SECONDS = 120;

    public function issue(string $email, string $name): RegistrationOtp
    {
        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::EXPIRES_IN_MINUTES);
        $lastSentAt = now();

        Notification::route("mail", $email)
            ->notify(new RegistrationOtpNotification($code, $name, self::EXPIRES_IN_MINUTES));

        RegistrationOtp::where("email", $email)
            ->whereNull("verified_at")
            ->delete();

        return RegistrationOtp::create([
            "user_id" => null,
            "email" => $email,
            "code_hash" => Hash::make($code),
            "attempts" => 0,
            "expires_at" => $expiresAt,
            "last_sent_at" => $lastSentAt,
        ]);
    }

    public function latestPendingFor(string $email): ?RegistrationOtp
    {
        return RegistrationOtp::where("email", $email)
            ->whereNull("verified_at")
            ->latest()
            ->first();
    }

    public function resend(string $email, string $name): array
    {
        $secondsRemaining = $this->secondsUntilResend($email);

        if ($secondsRemaining > 0) {
            return [
                "sent" => false,
                "seconds_remaining" => $secondsRemaining,
            ];
        }

        $this->issue($email, $name);

        return [
            "sent" => true,
            "seconds_remaining" => 0,
        ];
    }

    public function secondsUntilResend(string $email): int
    {
        $otp = $this->latestPendingFor($email);
        $availableAt = $otp?->last_sent_at?->copy()->addSeconds(self::RESEND_COOLDOWN_SECONDS);

        if (! $availableAt?->isFuture()) {
            return 0;
        }

        return max(1, (int) now()->diffInSeconds($availableAt));
    }

    public function verify(string $email, string $code): string
    {
        $otp = $this->latestPendingFor($email);

        if (! $otp) {
            return "missing";
        }

        if ($otp->expires_at->isPast()) {
            return "expired";
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return "locked";
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment("attempts");

            return $otp->fresh()->attempts >= self::MAX_ATTEMPTS
                ? "locked"
                : "invalid";
        }

        $otp->forceFill([
            "verified_at" => now(),
        ])->save();

        return "verified";
    }
}
