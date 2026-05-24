<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminNewUserActivationNotification;
use App\Services\RegistrationOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{
    private const PENDING_REGISTRATION_DATA = "pending_registration_data";

    public function __construct(
        private readonly RegistrationOtpService $registrationOtpService,
    ) {
    }

    public function showLogin()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (Auth::attempt($credentials, $request->boolean("remember"))) {
            $request->session()->regenerate();
            $user = $request->user();

            return match ($user->role) {
                "admin" => redirect()->route("admin.dashboard"),
                "rt" => redirect()->route("rt.dashboard"),
                "warga" => redirect()->route("warga.dashboard"),
            };
        }

        return back()
            ->withErrors([
                "email" => "Email atau password salah.",
            ])
            ->onlyInput("email");
    }

    public function showRegister()
    {
        return view("auth.register");
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            "nama_lengkap" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "no_hp" => "required|string|max:20",
            "password" => "required|string|min:8|confirmed",
        ]);

        $flashKey = "success";
        $flashMessage = "Registrasi berhasil. Kode OTP telah dikirim ke email Anda.";

        try {
            $this->registrationOtpService->issue($validated["email"], $validated["nama_lengkap"]);
        } catch (Throwable $exception) {
            report($exception);
            $flashKey = "error";
            $flashMessage = $this->otpDeliveryErrorMessage();
        }

        $this->storePendingRegistration($request, $validated);

        return redirect()
            ->route("register.otp")
            ->with($flashKey, $flashMessage);
    }

    public function showRegistrationOtp(Request $request)
    {
        $pending = $this->pendingRegistrationData($request);

        if (! $pending) {
            return redirect()
                ->route("register")
                ->with("error", "Sesi verifikasi OTP tidak ditemukan. Silakan daftar atau login ulang.");
        }

        return view("auth.verify-otp", [
            "maskedEmail" => $this->maskEmail($pending["email"]),
            "resendSecondsRemaining" => $this->registrationOtpService->secondsUntilResend($pending["email"]),
        ]);
    }

    public function verifyRegistrationOtp(Request $request)
    {
        $pending = $this->pendingRegistrationData($request);

        if (! $pending) {
            return redirect()
                ->route("register")
                ->with("error", "Sesi verifikasi OTP tidak ditemukan. Silakan daftar atau login ulang.");
        }

        $validated = $request->validate([
            "otp" => "required|digits:6",
        ], [
            "otp.required" => "Kode OTP wajib diisi.",
            "otp.digits" => "Kode OTP harus terdiri dari 6 digit.",
        ]);

        $result = $this->registrationOtpService->verify($pending["email"], $validated["otp"]);

        if ($result !== "verified") {
            return back()
                ->withErrors([
                    "otp" => $this->otpErrorMessage($result),
                ])
                ->onlyInput("otp");
        }

        $user = User::create([
            "nama_lengkap" => $pending["nama_lengkap"],
            "email" => $pending["email"],
            "no_hp" => $pending["no_hp"],
            "password" => $pending["password"],
            "role" => "warga",
            "nik" => null,
            "is_verified" => false,
            "status_akun" => "nonaktif",
            "email_verified_at" => now(),
        ]);

        $request->session()->forget(self::PENDING_REGISTRATION_DATA);

        $this->dispatchAdminNotification(
            new AdminNewUserActivationNotification($user),
        );

        return redirect()
            ->route("login")
            ->with(
                "success",
                "Email berhasil diverifikasi. Akun Anda menunggu aktivasi admin sebelum dapat mengajukan layanan.",
            );
    }

    public function resendRegistrationOtp(Request $request)
    {
        $pending = $this->pendingRegistrationData($request);

        if (! $pending) {
            return redirect()
                ->route("register")
                ->with("error", "Sesi verifikasi OTP tidak ditemukan. Silakan daftar atau login ulang.");
        }

        try {
            $result = $this->registrationOtpService->resend($pending["email"], $pending["nama_lengkap"]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                "resend" => $this->otpDeliveryErrorMessage(),
            ]);
        }

        if (! $result["sent"]) {
            return back()->withErrors([
                "resend" => "Tunggu {$result["seconds_remaining"]} detik sebelum mengirim ulang OTP.",
            ]);
        }

        return back()->with("success", "Kode OTP baru telah dikirim ke email Anda.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }

    private function storePendingRegistration(Request $request, array $data): void
    {
        $request->session()->put(self::PENDING_REGISTRATION_DATA, [
            "nama_lengkap" => $data["nama_lengkap"],
            "email" => $data["email"],
            "no_hp" => $data["no_hp"],
            "password" => $data["password"],
        ]);
    }

    private function pendingRegistrationData(Request $request): ?array
    {
        return $request->session()->get(self::PENDING_REGISTRATION_DATA);
    }

    private function otpErrorMessage(string $result): string
    {
        return match ($result) {
            "expired" => "Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode OTP.",
            "locked" => "Terlalu banyak percobaan salah. Silakan kirim ulang kode OTP.",
            "missing" => "Kode OTP tidak ditemukan. Silakan kirim ulang kode OTP.",
            default => "Kode OTP salah.",
        };
    }

    private function otpDeliveryErrorMessage(): string
    {
        return "Gmail menolak kredensial SMTP. Gunakan App Password Gmail yang valid, lalu coba kirim ulang OTP.";
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode("@", $email, 2);
        $visible = mb_substr($name, 0, 2);

        return $visible . str_repeat("*", max(3, mb_strlen($name) - 2)) . "@" . $domain;
    }
}
