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
    public function index(Request $request)
    {
        $currentYear = (int) now("Asia/Jakarta")->format("Y");

        // Statistik cards
        $stats = [
            "layanan_aktif" => Layanan::where("is_active", true)->count(),
            "pengajuan_baru" => PermohonanSurat::whereDate(
                "created_at",
                today(),
            )->count(),
            "warga_terdaftar" => User::whereIn("role", ["warga", "rt"])
                ->where(function ($query) {
                    $query->where("status_akun", "aktif")->orWhereNull(
                        "status_akun",
                    );
                })
                ->count(),
            "arsip_dokumen" => DokumenArsip::count(),
        ];

        $availableYears = PermohonanSurat::selectRaw("DISTINCT YEAR(created_at) as year")
            ->whereNotNull("created_at")
            ->orderByDesc("year")
            ->pluck("year")
            ->map(fn($year) => (int) $year)
            ->values();

        if (! $availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
        }

        $availableYears = $availableYears->unique()->sortDesc()->values();

        $requestedYear = (int) $request->query("year");
        $selectedYear = $availableYears->contains($requestedYear)
            ? $requestedYear
            : ($availableYears->first() ?? $currentYear);

        // 5 Pengajuan terbaru
        $pengajuanTerbaru = PermohonanSurat::with(["user", "layanan"])
            ->latest()
            ->take(5)
            ->get();
        $pengajuanTerbaruCount = $pengajuanTerbaru->count();

        $penggunaTerbaru = User::whereIn("role", ["warga", "rt"])
            ->latest()
            ->take(3)
            ->get();

        // Data chart bulanan (pengajuan per bulan tahun terpilih)
        $chartData = PermohonanSurat::selectRaw(
            "MONTH(created_at) as bulan, COUNT(*) as total",
        )
            ->whereYear("created_at", $selectedYear)
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
            compact(
                "stats",
                "penggunaTerbaru",
                "pengajuanTerbaru",
                "pengajuanTerbaruCount",
                "bulanLabels",
                "chartValues",
                "availableYears",
                "selectedYear",
            ),
        );
    }
}
