<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenArsip extends Model
{
    protected $table = 'dokumen_arsip';

    protected $fillable = [
        'permohonan_id', 'nomor_dokumen', 'file_path',
        'tanggal_terbit', 'diterbitkan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
        ];
    }

    public function permohonan()
    {
        return $this->belongsTo(PermohonanSurat::class, 'permohonan_id');
    }
}