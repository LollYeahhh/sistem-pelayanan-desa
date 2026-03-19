<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = "layanan";

    protected $fillable = ["nama_layanan", "slug", "icon_path", "is_active"];

    protected function casts(): array
    {
        return [
            "is_active" => "boolean",
        ];
    }

    public function formVersions()
    {
        return $this->hasMany(LayananFormVersion::class, "layanan_id")->orderByDesc("version_number");
    }

    public function publishedFormVersion()
    {
        return $this->hasOne(LayananFormVersion::class, "layanan_id")
            ->ofMany(["version_number" => "max"], function ($query) {
                $query->where("status", "published");
            });
    }

    public function draftFormVersion()
    {
        return $this->hasOne(LayananFormVersion::class, "layanan_id")
            ->ofMany(["version_number" => "max"], function ($query) {
                $query->where("status", "draft");
            });
    }

    /**
     * Default sections relation points to the currently published form.
     */
    public function sections()
    {
        return $this->hasMany(LayananFieldSection::class, "layanan_id")
            ->whereHas("formVersion", function ($query) {
                $query->where("status", "published");
            })
            ->orderBy("urutan");
    }

    public function fields()
    {
        return $this->hasManyThrough(
            LayananField::class,
            LayananFieldSection::class,
            "layanan_id",
            "section_id",
            "id",
            "id",
        );
    }

    public function getTotalFieldsAttribute(): int
    {
        return $this->sections->sum(fn($s) => $s->fields->count());
    }
}
