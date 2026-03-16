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

    /**
     * Sections form builder
     */
    public function sections()
    {
        return $this->hasMany(LayananFieldSection::class)->orderBy("urutan");
    }

    /**
     * Hitung total field dari semua section
     */
    public function getTotalFieldsAttribute(): int
    {
        return $this->sections->sum(fn($s) => $s->fields->count());
    }
}
