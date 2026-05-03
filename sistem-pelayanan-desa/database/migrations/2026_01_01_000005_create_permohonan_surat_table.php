<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_surat', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->enum('status', ['menunggu', 'diproses', 'disetujui', 'ditolak', 'selesai'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->datetime('reviewed_at')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_surat');
    }
};
