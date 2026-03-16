<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananField extends Model
{
    protected $table = "layanan_fields";

    protected $fillable = [
        "section_id",
        "label",
        "tipe_input",
        "placeholder",
        "is_required",
        "urutan",
        "opsi_select",
    ];

    protected function casts(): array
    {
        return [
            "is_required" => "boolean",
            "opsi_select" => "array",
        ];
    }

    public function section()
    {
        return $this->belongsTo(LayananFieldSection::class, "section_id");
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_input) {
            "text" => "TEXT",
            "number" => "NUMBER",
            "date" => "DATE",
            "select" => "SELECT",
            "file" => "FILE UPLOAD",
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
            "file" => "bg-emerald-50 text-emerald-600",
            "textarea" => "bg-rose-50 text-rose-600",
            default => "bg-gray-50 text-gray-600",
        };
    }
}
