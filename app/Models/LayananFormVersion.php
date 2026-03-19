<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananFormVersion extends Model
{
    protected $table = "layanan_form_versions";

    protected $fillable = [
        "layanan_id",
        "version_number",
        "status",
        "published_at",
    ];

    protected function casts(): array
    {
        return [
            "published_at" => "datetime",
        ];
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, "layanan_id");
    }

    public function sections()
    {
        return $this->hasMany(LayananFieldSection::class, "form_version_id")->orderBy("urutan");
    }
}
