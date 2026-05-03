<?php

namespace App\Support;

use App\Models\LayananField;
use App\Models\User;
use Illuminate\Support\Str;

class PengajuanIdentityMapper
{
    protected const FIELD_ATTRIBUTE_ALIASES = [
        "nama lengkap" => "nama_lengkap",
        "nama lengkap pemohon" => "nama_lengkap",
        "nama pemohon" => "nama_lengkap",
        "nik" => "nik",
        "nomor nik" => "nik",
        "no nik" => "nik",
        "no ktp" => "nik",
        "nomor ktp" => "nik",
        "tempat lahir" => "tempat_lahir",
        "tanggal lahir" => "tanggal_lahir",
        "jenis kelamin" => "jenis_kelamin",
        "status perkawinan" => "status_perkawinan",
        "status pernikahan" => "status_perkawinan",
        "agama" => "agama",
        "pekerjaan" => "pekerjaan",
        "alamat" => "alamat",
        "alamat lengkap" => "alamat",
        "rt rw" => "rt_rw",
        "rt / rw" => "rt_rw",
        "rt rw domisili" => "rt_rw",
        "desa" => "desa",
        "desa kelurahan" => "desa",
        "desa / kelurahan" => "desa",
        "kelurahan" => "desa",
        "kecamatan" => "kecamatan",
        "no hp" => "no_hp",
        "nomor hp" => "no_hp",
        "no hp whatsapp" => "no_hp",
        "no hp / whatsapp" => "no_hp",
        "nomor hp whatsapp" => "no_hp",
        "nomor hp / whatsapp" => "no_hp",
        "no whatsapp" => "no_hp",
        "nomor whatsapp" => "no_hp",
        "whatsapp" => "no_hp",
        "no wa" => "no_hp",
        "nomor wa" => "no_hp",
        "nomor telepon" => "no_hp",
        "no telepon" => "no_hp",
    ];

    protected const IDENTITY_SUMMARY_LABELS = [
        "Nama Lengkap" => "nama_lengkap",
        "NIK" => "nik",
        "Tempat Lahir" => "tempat_lahir",
        "Tanggal Lahir" => "tanggal_lahir",
        "Jenis Kelamin" => "jenis_kelamin",
        "Status Perkawinan" => "status_perkawinan",
        "Agama" => "agama",
        "Pekerjaan" => "pekerjaan",
        "Alamat Lengkap" => "alamat",
        "RT / RW" => "rt_rw",
        "Desa / Kelurahan" => "desa",
        "Kecamatan" => "kecamatan",
        "No. HP / WhatsApp" => "no_hp",
    ];

    public static function buildFieldStates(iterable $fields, User $user): array
    {
        $states = [];

        foreach ($fields as $field) {
            $states[$field->id] = self::resolveFieldState($field, $user);
        }

        return $states;
    }

    public static function mergeProfileValuesIntoPayload(iterable $fields, User $user, array $payload): array
    {
        foreach ($fields as $field) {
            if ($field->tipe_input === "file") {
                continue;
            }

            $fieldState = self::resolveFieldState($field, $user);

            if ($fieldState["is_locked"]) {
                $payload["field_" . $field->id] = $fieldState["value"];
            }
        }

        return $payload;
    }

    public static function buildIdentitySummary(User $user): array
    {
        $summary = [];

        foreach (self::IDENTITY_SUMMARY_LABELS as $label => $attribute) {
            $summary[$label] = self::formatDisplayValue($attribute, $user->{$attribute} ?? null);
        }

        return $summary;
    }

    public static function resolveFieldState(LayananField $field, User $user): array
    {
        $attribute = self::resolveAttributeFromLabel($field->label);
        $value = $attribute
            ? self::formatFieldValue($attribute, $user->{$attribute} ?? null, $field)
            : null;

        return [
            "attribute" => $attribute,
            "is_identity" => $attribute !== null,
            "value" => $value,
            "is_locked" => filled($value),
        ];
    }

    protected static function resolveAttributeFromLabel(?string $label): ?string
    {
        $normalized = self::normalizeLabel($label);

        return self::FIELD_ATTRIBUTE_ALIASES[$normalized] ?? null;
    }

    protected static function formatFieldValue(string $attribute, mixed $value, LayananField $field): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($attribute === "tanggal_lahir") {
            return optional($value)->format("Y-m-d") ?: null;
        }

        $formatted = trim((string) $value);

        if ($formatted === "") {
            return null;
        }

        if ($field->tipe_input === "select" && is_array($field->opsi_select)) {
            foreach ($field->opsi_select as $option) {
                if (self::normalizeLabel($option) === self::normalizeLabel($formatted)) {
                    return $option;
                }
            }
        }

        return $formatted;
    }

    protected static function formatDisplayValue(string $attribute, mixed $value): string
    {
        if ($value === null || $value === "") {
            return "-";
        }

        if ($attribute === "tanggal_lahir") {
            return optional($value)->format("d/m/Y") ?: "-";
        }

        return trim((string) $value) !== "" ? (string) $value : "-";
    }

    protected static function normalizeLabel(?string $value): string
    {
        return (string) Str::of($value ?? "")
            ->lower()
            ->ascii()
            ->replaceMatches("/[^a-z0-9]+/", " ")
            ->squish();
    }
}
