<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("aduan_masyarakat", function (Blueprint $table) {
            $table->id();
            $table->string("nama_depan", 100);
            $table->string("nama_belakang", 100);
            $table->string("email");
            $table->text("isi_aduan");
            $table->string("lampiran_path")->nullable();
            $table->enum("status", ["baru", "diproses", "selesai"])->default("baru");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("aduan_masyarakat");
    }
};
