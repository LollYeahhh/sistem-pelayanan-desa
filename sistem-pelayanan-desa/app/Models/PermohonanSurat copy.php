<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanSurat extends Model
{
    protected $table = "permohonan_surat";

    protected $fillable = [
        "user_id",
        "layanan_id",
        "no_registrasi",
        "status",
        "catatan_pemohon",
        "catatan_admin",
        "reviewed_by",
        "reviewed_at",
    ];

    protected function casts(): array
    {
        return [
            "reviewed_at" => "datetime",
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, "reviewed_by");
    }

    public function data()
    {
        return $this->hasMany(PermohonanData::class, "permohonan_id");
    }

    public function lampiran()
    {
        return $this->hasMany(PermohonanLampiran::class, "permohonan_id");
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            "menunggu" => [
                "label" => "MENUNGGU",
                "color" => "bg-yellow-100 text-yellow-700",
            ],
            "diproses" => [
                "label" => "DIPROSES",
                "color" => "bg-blue-100 text-blue-700",
            ],
            "disetujui" => [
                "label" => "DISETUJUI",
                "color" => "bg-green-100 text-green-700",
            ],
            "ditolak" => [
                "label" => "DITOLAK",
                "color" => "bg-red-100 text-red-700",
            ],
            "selesai" => [
                "label" => "SELESAI",
                "color" => "bg-emerald-100 text-emerald-700",
            ],
            default => [
                "label" => strtoupper($this->status),
                "color" => "bg-gray-100 text-gray-700",
            ],
        };
    }
}
