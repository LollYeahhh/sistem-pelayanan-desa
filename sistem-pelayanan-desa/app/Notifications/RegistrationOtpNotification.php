<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $code,
        public readonly string $recipientName,
        public readonly int $expiresInMinutes,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("[NO-REPLY] Kode OTP Registrasi Akun")
            ->view("emails.registration-otp", [
                "code" => $this->code,
                "expiresInMinutes" => $this->expiresInMinutes,
                "name" => $this->recipientName,
            ]);
    }
}
