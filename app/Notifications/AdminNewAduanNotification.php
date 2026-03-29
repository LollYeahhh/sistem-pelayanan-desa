<?php

namespace App\Notifications;

use App\Models\AduanMasyarakat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewAduanNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected AduanMasyarakat $aduan,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ["database"];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            "kind" => "aduan",
            "title" => "Aduan baru masuk",
            "message" => "{$this->aduan->full_name} mengirim aduan baru.",
            "url" => route("admin.aduan.show", $this->aduan),
            "resource_id" => $this->aduan->id,
        ];
    }
}
