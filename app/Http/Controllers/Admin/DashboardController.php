<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\User;
use App\Models\PermohonanSurat;
use App\Models\DokumenArsip;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik cards
        $stats = [
            "layanan_aktif" => Layanan::where("is_active", true)->count(),
            "pengajuan_baru" => PermohonanSurat::whereDate(
                "created_at",
                today(),
            )->count(),
            "warga_terdaftar" => User::where("role", "warga")->count(),
            "arsip_dokumen" => DokumenArsip::count(),
        ];

        // 5 Pengajuan terbaru
        $pengajuanTerbaru = PermohonanSurat::with(["user", "layanan"])
            ->latest()
            ->take(5)
            ->get();

        // Data chart bulanan (pengajuan per bulan tahun ini)
        $chartData = PermohonanSurat::selectRaw(
            "MONTH(created_at) as bulan, COUNT(*) as total",
        )
            ->whereYear("created_at", date("Y"))
            ->groupByRaw("MONTH(created_at)")
            ->orderByRaw("MONTH(created_at)")
            ->pluck("total", "bulan")
            ->toArray();

        $bulanLabels = [
            "JAN",
            "FEB",
            "MAR",
            "APR",
            "MEI",
            "JUN",
            "JUL",
            "AGS",
            "SEP",
            "OKT",
            "NOV",
            "DES",
        ];
        $chartValues = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartValues[] = $chartData[$i] ?? 0;
        }

        return view(
            "admin.dashboard",
            compact("stats", "pengajuanTerbaru", "bulanLabels", "chartValues"),
        );
    }
}
