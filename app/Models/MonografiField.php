<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonografiField extends Model
{
    protected $table = "monografi_fields";

    public $timestamps = false;

    protected $fillable = ["category_id", "label", "tipe_input", "urutan"];

    public function category()
    {
        return $this->belongsTo(MonografiCategory::class, "category_id");
    }

    public function values()
    {
        return $this->hasMany(MonografiData::class, "field_id");
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_input) {
            "text" => "TEXT",
            "number" => "NUMBER",
            "date" => "DATE",
            "select" => "SELECT",
            "textarea" => "TEXTAREA",
            default => strtoupper($this->tipe_input),
        };
    }

    public function getTipeBadgeColorAttribute(): string
    {
        return match ($this->tipe_input) {
            "text" => "bg-blue-50 text-blue-600",
            "number" => "bg-purple-50 text-purple-600",
            "date" => "bg-amber-50 text-amber-600",
            "select" => "bg-cyan-50 text-cyan-600",
            "textarea" => "bg-rose-50 text-rose-600",
            default => "bg-gray-50 text-gray-600",
        };
    }
}
