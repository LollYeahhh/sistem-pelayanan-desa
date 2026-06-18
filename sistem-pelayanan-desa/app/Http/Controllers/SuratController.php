<?php

namespace App\Http\Controllers;

use App\Models\PermohonanSurat;
use Illuminate\Http\JsonResponse;

class SuratController extends Controller
{
    private const FORMAT_GUIDE_REPLY = "*FORMAT CEK STATUS SURAT*\n\nUntuk mengecek status pengajuan, silakan kirim nomor registrasi yang didapat setelah mengajukan surat.\n\nContoh:\nCek status surat REG-ABC12345\n\nNomor registrasi bisa dilihat pada halaman riwayat pengajuan di portal layanan:\nhttps://pelayanandesadukuhtengah.my.id/";

    public function cek(string $id): JsonResponse
    {
        $noRegistrasi = strtoupper(trim($id));

        if ($this->isMissingOrInvalidRegistrationNumber($noRegistrasi)) {
            return response()->json([
                'status' => 'format_tidak_lengkap',
                'reply' => self::FORMAT_GUIDE_REPLY,
            ]);
        }

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
            'status_label' => $this->resolveStatusLabel($surat->status),
            'nama' => $surat->user?->nama_lengkap,
            'jenis_surat' => $surat->layanan?->nama_layanan,
        ]);
    }

    private function isMissingOrInvalidRegistrationNumber(string $noRegistrasi): bool
    {
        if ($noRegistrasi === '' || $noRegistrasi === 'FORMAT-TIDAK-LENGKAP') {
            return true;
        }

        return preg_match('/^REG-[A-Z0-9]{8}$/', $noRegistrasi) !== 1;
    }

    private function resolveStatusLabel(string $status): string
    {
        return match ($status) {
            'menunggu' => 'Menunggu verifikasi',
            'diproses' => 'Sedang diproses',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
            default => strtoupper($status),
        };
    }
}
