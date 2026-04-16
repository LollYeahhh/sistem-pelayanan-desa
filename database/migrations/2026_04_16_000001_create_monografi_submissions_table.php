<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("monografi_submissions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("rt_user_id")->constrained("users")->cascadeOnDelete();
            $table->year("tahun");
            $table->enum("status", ["draft", "submitted"])->default("draft");
            $table->string("wilayah_label");
            $table->timestamp("last_saved_at")->nullable();
            $table->timestamp("submitted_at")->nullable();
            $table->timestamps();

            $table->unique(["rt_user_id", "tahun"], "monografi_submission_unique");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("monografi_submissions");
    }
};
