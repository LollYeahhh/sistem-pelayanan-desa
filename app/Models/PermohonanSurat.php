<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class PermohonanSurat extends Model
{
    public const ARSIP_STATUSES = ["disetujui", "ditolak", "selesai"];
    public const ADMIN_INBOX_STATUSES = ["menunggu", "diproses", "disetujui"];

    protected $table = 'permohonan_surat';

    protected $fillable = [
        'user_id', 'layanan_id', 'form_version_id', 'no_registrasi', 'status',
        'catatan_pemohon', 'catatan_admin', 'alasan_penolakan', 'reviewed_by',
        'reviewed_at', 'deadline', 'file_dokumen_path', 'file_dokumen_nama',
        'file_dokumen_mime', 'dokumen_selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'deadline' => 'date',
            'dokumen_selesai_at' => 'datetime',
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

    public function formVersion()
    {
        return $this->belongsTo(LayananFormVersion::class, 'form_version_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function data()
    {
        return $this->hasMany(PermohonanData::class, 'permohonan_id');
    }

    public function lampiran()
    {
        return $this->hasMany(PermohonanLampiran::class, 'permohonan_id');
    }

    public function scopeForArsip($query)
    {
        return $query
            ->whereIn("status", self::ARSIP_STATUSES)
            ->whereNotNull("reviewed_at");
    }

    public function scopeForAdminInbox($query)
    {
        return $query->whereIn("status", self::ADMIN_INBOX_STATUSES);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'menunggu'  => ['label' => 'MENUNGGU', 'color' => 'bg-yellow-100 text-yellow-700'],
            'diproses'  => ['label' => 'DIPROSES', 'color' => 'bg-blue-100 text-blue-700'],
            'disetujui' => ['label' => 'DISETUJUI', 'color' => 'bg-green-100 text-green-700'],
            'ditolak'   => ['label' => 'DITOLAK', 'color' => 'bg-red-100 text-red-700'],
            'selesai'   => ['label' => 'SELESAI', 'color' => 'bg-emerald-100 text-emerald-700'],
            default     => ['label' => strtoupper($this->status), 'color' => 'bg-gray-100 text-gray-700'],
        };
    }

    public function getRejectionReasonAttribute(): ?string
    {
        return $this->getAttribute('alasan_penolakan')
            ?? $this->getAttribute('catatan_admin')
            ?? $this->getAttribute('catatan');
    }

    public function getMasaBerlakuSuratAttribute(): ?Carbon
    {
        if ($this->deadline) {
            return $this->deadline->copy();
        }

        if ($this->reviewed_at) {
            return $this->reviewed_at->copy()->addDays(7);
        }

        return null;
    }
}
