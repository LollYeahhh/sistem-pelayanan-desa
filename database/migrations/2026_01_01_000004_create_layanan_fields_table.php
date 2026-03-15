<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('layanan_field_sections')->cascadeOnDelete();
            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->enum('tipe_input', ['text', 'number', 'date', 'select', 'file', 'textarea'])->default('text');
            $table->text('opsi_select')->nullable()->comment('JSON array untuk opsi dropdown');
            $table->boolean('is_required')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_fields');
    }
};
