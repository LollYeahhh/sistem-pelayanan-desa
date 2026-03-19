<?php

namespace App\Support;

use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananFieldSection;
use App\Models\LayananFormVersion;
use Illuminate\Support\Facades\DB;

class LayananFormVersionManager
{
    public static function getPublishedVersion(Layanan $layanan): ?LayananFormVersion
    {
        return $layanan->formVersions()
            ->where("status", "published")
            ->orderByDesc("version_number")
            ->first();
    }

    public static function getDraftVersion(Layanan $layanan): ?LayananFormVersion
    {
        return $layanan->formVersions()
            ->where("status", "draft")
            ->orderByDesc("version_number")
            ->first();
    }

    public static function ensurePublishedVersion(Layanan $layanan): LayananFormVersion
    {
        $published = self::getPublishedVersion($layanan);

        if ($published) {
            return $published->load("sections.fields");
        }

        return DB::transaction(function () use ($layanan) {
            $version = $layanan->formVersions()->create([
                "version_number" => 1,
                "status" => "published",
                "published_at" => now(),
            ]);

            LayananFieldSection::query()
                ->where("layanan_id", $layanan->id)
                ->whereNull("form_version_id")
                ->update(["form_version_id" => $version->id]);

            return $version->load("sections.fields");
        });
    }

    public static function ensureDraftVersion(Layanan $layanan): LayananFormVersion
    {
        $draft = self::getDraftVersion($layanan);

        if ($draft) {
            return $draft->load("sections.fields");
        }

        $published = self::ensurePublishedVersion($layanan);

        return DB::transaction(function () use ($layanan, $published) {
            $nextVersion = ((int) $layanan->formVersions()->max("version_number")) + 1;

            $draft = $layanan->formVersions()->create([
                "version_number" => $nextVersion,
                "status" => "draft",
                "published_at" => null,
            ]);

            $published->loadMissing("sections.fields");

            foreach ($published->sections as $section) {
                $newSection = LayananFieldSection::create([
                    "layanan_id" => $layanan->id,
                    "form_version_id" => $draft->id,
                    "nama_section" => $section->nama_section,
                    "urutan" => $section->urutan,
                ]);

                foreach ($section->fields as $field) {
                    LayananField::create([
                        "section_id" => $newSection->id,
                        "label" => $field->label,
                        "tipe_input" => $field->tipe_input,
                        "placeholder" => $field->placeholder,
                        "is_required" => $field->is_required,
                        "urutan" => $field->urutan,
                        "opsi_select" => $field->opsi_select,
                    ]);
                }
            }

            return $draft->load("sections.fields");
        });
    }

    public static function publishDraft(Layanan $layanan): LayananFormVersion
    {
        return DB::transaction(function () use ($layanan) {
            $draft = self::ensureDraftVersion($layanan);

            $layanan->formVersions()
                ->where("status", "published")
                ->update(["status" => "archived"]);

            $layanan->formVersions()
                ->where("status", "draft")
                ->where("id", "!=", $draft->id)
                ->update(["status" => "archived"]);

            $draft->update([
                "status" => "published",
                "published_at" => now(),
            ]);

            return $draft->fresh()->load("sections.fields");
        });
    }
}
