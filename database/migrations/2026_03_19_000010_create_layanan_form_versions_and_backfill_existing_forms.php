<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("layanan_form_versions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("layanan_id")->constrained("layanan")->cascadeOnDelete();
            $table->unsignedInteger("version_number");
            $table->enum("status", ["draft", "published", "archived"])->default("draft");
            $table->timestamp("published_at")->nullable();
            $table->timestamps();

            $table->unique(["layanan_id", "version_number"]);
        });

        Schema::table("layanan_field_sections", function (Blueprint $table) {
            $table->foreignId("form_version_id")
                ->nullable()
                ->after("layanan_id")
                ->constrained("layanan_form_versions")
                ->nullOnDelete();
        });

        Schema::table("permohonan_surat", function (Blueprint $table) {
            $table->foreignId("form_version_id")
                ->nullable()
                ->after("layanan_id")
                ->constrained("layanan_form_versions")
                ->nullOnDelete();
        });

        $now = now();
        $layananItems = DB::table("layanan")->get(["id"]);

        foreach ($layananItems as $layanan) {
            $versionId = DB::table("layanan_form_versions")->insertGetId([
                "layanan_id" => $layanan->id,
                "version_number" => 1,
                "status" => "published",
                "published_at" => $now,
                "created_at" => $now,
                "updated_at" => $now,
            ]);

            DB::table("layanan_field_sections")
                ->where("layanan_id", $layanan->id)
                ->update(["form_version_id" => $versionId]);

            DB::table("permohonan_surat")
                ->where("layanan_id", $layanan->id)
                ->update(["form_version_id" => $versionId]);
        }
    }

    public function down(): void
    {
        Schema::table("permohonan_surat", function (Blueprint $table) {
            $table->dropConstrainedForeignId("form_version_id");
        });

        Schema::table("layanan_field_sections", function (Blueprint $table) {
            $table->dropConstrainedForeignId("form_version_id");
        });

        Schema::dropIfExists("layanan_form_versions");
    }
};
