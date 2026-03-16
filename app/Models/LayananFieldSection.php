<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananFieldSection extends Model
{
    protected $table = 'layanan_field_sections';

    protected $fillable = [
        'layanan_id',
        'nama_section',
        'urutan',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function fields()
    {
        return $this->hasMany(LayananField::class, 'section_id')->orderBy('urutan');
    }
}