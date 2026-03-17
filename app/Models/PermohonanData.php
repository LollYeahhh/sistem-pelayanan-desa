<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanData extends Model
{
    protected $table = "permohonan_data";

    public $timestamps = false;

    protected $fillable = ["permohonan_id", "field_id", "value"];

    public function permohonan()
    {
        return $this->belongsTo(PermohonanSurat::class, "permohonan_id");
    }

    public function field()
    {
        return $this->belongsTo(LayananField::class, "field_id");
    }
}
