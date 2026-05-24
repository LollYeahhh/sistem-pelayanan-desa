<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const JENIS_KELAMIN_OPTIONS = [
        "laki-laki" => "Laki-laki",
        "perempuan" => "Perempuan",
    ];

    public const STATUS_PERKAWINAN_OPTIONS = [
        "belum_menikah" => "Belum Menikah",
        "menikah" => "Menikah",
        "cerai_hidup" => "Cerai Hidup",
        "cerai_mati" => "Cerai Mati",
    ];

    protected $fillable = [
        "nama_lengkap",
        "nik",
        "email",
        "email_verified_at",
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
            "email_verified_at" => "datetime",
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

    public function registrationOtps(): HasMany
    {
        return $this->hasMany(RegistrationOtp::class);
    }

    public function monografiEntries()
    {
        return $this->hasMany(MonografiData::class, "rt_user_id");
    }

    public function monografiSubmissions()
    {
        return $this->hasMany(MonografiSubmission::class, "rt_user_id");
    }

    public static function getJenisKelaminOptions(): array
    {
        return self::JENIS_KELAMIN_OPTIONS;
    }

    public static function getStatusPerkawinanOptions(): array
    {
        return self::STATUS_PERKAWINAN_OPTIONS;
    }

    public static function normalizeJenisKelaminValue(?string $value): ?string
    {
        return self::normalizeOptionValue($value, self::JENIS_KELAMIN_OPTIONS);
    }

    public static function normalizeStatusPerkawinanValue(?string $value): ?string
    {
        return self::normalizeOptionValue($value, self::STATUS_PERKAWINAN_OPTIONS);
    }

    public static function getJenisKelaminLabel(?string $value, string $fallback = "-"): string
    {
        return self::resolveOptionLabel($value, self::JENIS_KELAMIN_OPTIONS, $fallback);
    }

    public static function getStatusPerkawinanLabel(?string $value, string $fallback = "-"): string
    {
        return self::resolveOptionLabel($value, self::STATUS_PERKAWINAN_OPTIONS, $fallback);
    }

    protected function namaLengkap(): Attribute
    {
        return $this->normalizedTitleCaseAttribute();
    }

    protected function jenisKelamin(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => self::normalizeJenisKelaminValue($value),
            set: fn(?string $value) => self::normalizeJenisKelaminValue($value),
        );
    }

    protected function statusPerkawinan(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => self::normalizeStatusPerkawinanValue($value),
            set: fn(?string $value) => self::normalizeStatusPerkawinanValue($value),
        );
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

    public function getJenisKelaminLabelAttribute(): string
    {
        return self::getJenisKelaminLabel($this->getRawOriginal("jenis_kelamin"));
    }

    public function getStatusPerkawinanLabelAttribute(): string
    {
        return self::getStatusPerkawinanLabel($this->getRawOriginal("status_perkawinan"));
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

    private static function normalizeOptionValue(?string $value, array $options): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === "") {
            return null;
        }

        if (array_key_exists($value, $options)) {
            return $value;
        }

        $matchedKey = array_search($value, $options, true);

        if ($matchedKey !== false) {
            return $matchedKey;
        }

        return $value;
    }

    private static function resolveOptionLabel(?string $value, array $options, string $fallback): string
    {
        $normalizedValue = self::normalizeOptionValue($value, $options);

        if ($normalizedValue === null) {
            return $fallback;
        }

        return $options[$normalizedValue] ?? $normalizedValue;
    }
}
