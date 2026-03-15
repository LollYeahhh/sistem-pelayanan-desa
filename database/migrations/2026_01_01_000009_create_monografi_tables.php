<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori monografi (A. Potensi SDM, B. Perumahan, C. Listrik, dst.)
        Schema::create('monografi_categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 5)->comment('A, B, C, dst.');
            $table->string('nama_kategori');
            $table->string('icon')->nullable();
            $table->integer('urutan')->default(0);
        });

        // Field-field dalam setiap kategori monografi
        Schema::create('monografi_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('monografi_categories')->cascadeOnDelete();
            $table->string('label');
            $table->enum('tipe_input', ['text', 'number', 'date', 'select', 'textarea'])->default('number');
            $table->integer('urutan')->default(0);
        });

        // Data monografi yang diisi oleh RT
        Schema::create('monografi_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('monografi_fields')->cascadeOnDelete();
            $table->unsignedBigInteger('rt_user_id');
            $table->text('value')->nullable();
            $table->year('tahun');
            $table->timestamps();

            $table->foreign('rt_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['field_id', 'rt_user_id', 'tahun'], 'monografi_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monografi_data');
        Schema::dropIfExists('monografi_fields');
        Schema::dropIfExists('monografi_categories');
    }
};
