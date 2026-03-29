<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewUserActivationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $user,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ["database"];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            "kind" => "aktivasi",
            "title" => "Akun baru menunggu verifikasi",
            "message" => "{$this->user->nama_lengkap} mendaftar dan menunggu aktivasi admin.",
            "url" => route("admin.warga.show", $this->user),
            "resource_id" => $this->user->id,
        ];
    }
}
