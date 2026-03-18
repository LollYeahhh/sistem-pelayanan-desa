<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "nama_lengkap",
        "nik",
        "email",
        "password",
        "role",
        "tempat_lahir",
        "tanggal_lahir",
        "jenis_kelamin",
        "status_perkawinan",
        "agama",
        "pekerjaan",
        "alamat",
        "rt_rw",
        "desa",
        "kecamatan",
        "no_hp",
        "foto",
        "status_akun",
        "is_verified",
    ];

    protected $hidden = ["password", "remember_token"];

    protected function casts(): array
    {
        return [
            "tanggal_lahir" => "date",
            "is_verified" => "boolean",
            "password" => "hashed",
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }
    public function isRT(): bool
    {
        return $this->role === "rt";
    }
    public function isWarga(): bool
    {
        return $this->role === "warga";
    }

    public function permohonan()
    {
        return $this->hasMany(PermohonanSurat::class);
    }

    public function monografiEntries()
    {
        return $this->hasMany(MonografiData::class, "rt_user_id");
    }

    protected function namaLengkap(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function tempatLahir(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function desa(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function kecamatan(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function normalizedTitleCaseAttribute(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->normalizeTitleCase($value),
            set: fn(?string $value) => $this->normalizeTitleCase($value),
        );
    }

    protected function normalizeTitleCase(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value));

        if ($value === '') {
            return null;
        }

        $uppercaseTokens = ["RT", "RW"];

        $parts = preg_split('/(\s+)/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

        $normalized = array_map(function ($part) use ($uppercaseTokens) {
            if (trim($part) === '') {
                return $part;
            }

            $upperPart = mb_strtoupper($part, 'UTF-8');

            if (in_array($upperPart, $uppercaseTokens, true) || preg_match('/^\d+$/', $part)) {
                return $upperPart;
            }

            return mb_convert_case(mb_strtolower($part, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }, $parts);

        return implode('', $normalized);
    }
}
