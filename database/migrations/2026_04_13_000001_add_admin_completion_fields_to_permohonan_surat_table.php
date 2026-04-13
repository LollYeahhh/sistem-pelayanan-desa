<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToAdd = [];

        if (! Schema::hasColumn('permohonan_surat', 'catatan_admin')) {
            $columnsToAdd[] = 'catatan_admin';
        }

        if (! Schema::hasColumn('permohonan_surat', 'file_dokumen_path')) {
            $columnsToAdd[] = 'file_dokumen_path';
        }

        if (! Schema::hasColumn('permohonan_surat', 'file_dokumen_nama')) {
            $columnsToAdd[] = 'file_dokumen_nama';
        }

        if (! Schema::hasColumn('permohonan_surat', 'file_dokumen_mime')) {
            $columnsToAdd[] = 'file_dokumen_mime';
        }

        if (! Schema::hasColumn('permohonan_surat', 'dokumen_selesai_at')) {
            $columnsToAdd[] = 'dokumen_selesai_at';
        }

        if ($columnsToAdd === []) {
            return;
        }

        Schema::table('permohonan_surat', function (Blueprint $table) use ($columnsToAdd) {
            if (in_array('catatan_admin', $columnsToAdd, true)) {
                $table->text('catatan_admin')->nullable();
            }

            if (in_array('file_dokumen_path', $columnsToAdd, true)) {
                $table->string('file_dokumen_path')->nullable();
            }

            if (in_array('file_dokumen_nama', $columnsToAdd, true)) {
                $table->string('file_dokumen_nama')->nullable();
            }

            if (in_array('file_dokumen_mime', $columnsToAdd, true)) {
                $table->string('file_dokumen_mime')->nullable();
            }

            if (in_array('dokumen_selesai_at', $columnsToAdd, true)) {
                $table->timestamp('dokumen_selesai_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columnsToDrop = [];

        foreach ([
            'catatan_admin',
            'file_dokumen_path',
            'file_dokumen_nama',
            'file_dokumen_mime',
            'dokumen_selesai_at',
        ] as $column) {
            if (Schema::hasColumn('permohonan_surat', $column)) {
                $columnsToDrop[] = $column;
            }
        }

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('permohonan_surat', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }
};
