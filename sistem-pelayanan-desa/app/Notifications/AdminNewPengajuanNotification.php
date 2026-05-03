<?php

namespace App\Notifications;

use App\Models\PermohonanSurat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewPengajuanNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected PermohonanSurat $permohonan,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ["database"];
    }

    public function toDatabase(object $notifiable): array
    {
        $pemohon = $this->permohonan->user->nama_lengkap ?? "Pemohon";
        $layanan = $this->permohonan->layanan->nama_layanan ?? "Layanan";

        return [
            "kind" => "pengajuan",
            "title" => "Pengajuan baru masuk",
            "message" => "{$pemohon} mengajukan {$layanan}.",
            "url" => route("admin.pengajuan.show", $this->permohonan),
            "resource_id" => $this->permohonan->id,
        ];
    }
}
