<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AduanMasyarakat extends Model
{
    use HasFactory;

    protected $table = "aduan_masyarakat";

    protected $fillable = [
        "nama_depan",
        "nama_belakang",
        "email",
        "isi_aduan",
        "lampiran_path",
        "status",
    ];

    protected function namaDepan(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function namaBelakang(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function normalizedTitleCaseAttribute(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->normalizeTitleCase($value),
            set: fn (?string $value) => $this->normalizeTitleCase($value),
        );
    }

    protected function normalizeTitleCase(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace("/\s+/u", " ", trim($value));

        if ($value === "") {
            return null;
        }

        return mb_convert_case(
            mb_strtolower($value, "UTF-8"),
            MB_CASE_TITLE,
            "UTF-8",
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(
                implode(" ", array_filter([$this->nama_depan, $this->nama_belakang])),
            ),
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => match ($attributes["status"] ?? null) {
                "baru" => "Baru",
                "diproses" => "Diproses",
                "selesai" => "Selesai",
                default => "-",
            },
        );
    }

    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => match ($attributes["status"] ?? null) {
                "baru" => "bg-amber-50 text-amber-600 border-amber-200",
                "diproses" => "bg-blue-50 text-blue-600 border-blue-200",
                "selesai" => "bg-green-50 text-green-600 border-green-200",
                default => "bg-gray-50 text-gray-500 border-gray-200",
            },
        );
    }
}
