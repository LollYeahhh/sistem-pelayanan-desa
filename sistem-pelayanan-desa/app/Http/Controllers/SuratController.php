<?php

namespace App\Http\Controllers;

use App\Models\PermohonanSurat;
use Illuminate\Http\JsonResponse;

class SuratController extends Controller
{
    public function cek(string $id): JsonResponse
    {
        $noRegistrasi = strtoupper(trim($id));

        $surat = PermohonanSurat::with([
            'user:id,nama_lengkap',
            'layanan:id,nama_layanan',
        ])
            ->where('no_registrasi', $noRegistrasi)
            ->first();

        if (!$surat) {
            return response()->json([
                'status' => 'tidak ditemukan',
            ]);
        }

        return response()->json([
            'status' => $surat->status,
            'nama' => $surat->user?->nama_lengkap,
            'jenis_surat' => $surat->layanan?->nama_layanan,
        ]);
    }
}
