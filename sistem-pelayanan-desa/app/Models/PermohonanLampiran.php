<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanLampiran extends Model
{
    protected $table = "permohonan_lampiran";

    protected $fillable = [
        "permohonan_id",
        "nama_dokumen",
        "file_path",
        "file_type",
    ];

    public function permohonan()
    {
        return $this->belongsTo(PermohonanSurat::class, "permohonan_id");
    }
}
