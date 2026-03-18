<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\User;
use App\Models\PermohonanSurat;
use App\Models\AduanMasyarakat;
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
                    $query
                        ->where("status_akun", "aktif")
                        ->orWhereNull("status_akun");
                })
                ->count(),
            "arsip_dokumen" => DokumenArsip::count(),
        ];

        $availableYears = PermohonanSurat::selectRaw(
            "DISTINCT YEAR(created_at) as year",
        )
            ->whereNotNull("created_at")
            ->orderByDesc("year")
            ->pluck("year")
            ->map(fn($year) => (int) $year)
            ->values();

        if (!$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
        }

        $availableYears = $availableYears->unique()->sortDesc()->values();

        $requestedYear = (int) $request->query("year");
        $selectedYear = $availableYears->contains($requestedYear)
            ? $requestedYear
            : $availableYears->first() ?? $currentYear;

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

    /**
     * Global search for admin navbar.
     */
    public function search(Request $request)
    {
        $q = trim($request->query("q", ""));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $results = collect();

        // Search Warga (nama_lengkap, nik)
        User::whereIn("role", ["warga", "rt"])
            ->where(function ($query) use ($q) {
                $query
                    ->where("nama_lengkap", "like", "%{$q}%")
                    ->orWhere("nik", "like", "%{$q}%");
            })
            ->take(5)
            ->get()
            ->each(function ($user) use ($results) {
                $results->push([
                    "type" => "Warga",
                    "title" => $user->nama_lengkap,
                    "sub" => $user->nik ? "NIK: " . $user->nik : $user->email,
                    "url" => route("admin.warga.show", $user->id),
                ]);
            });

        // Search Layanan (nama_layanan)
        Layanan::where("nama_layanan", "like", "%{$q}%")
            ->take(5)
            ->get()
            ->each(function ($layanan) use ($results) {
                $results->push([
                    "type" => "Layanan",
                    "title" => $layanan->nama_layanan,
                    "sub" => $layanan->is_active ? "Aktif" : "Nonaktif",
                    "url" => route("admin.layanan.index"),
                ]);
            });

        // Search Pengajuan (no_registrasi, or layanan name)
        PermohonanSurat::with(["user", "layanan"])
            ->where(function ($query) use ($q) {
                $query
                    ->where("no_registrasi", "like", "%{$q}%")
                    ->orWhereHas(
                        "layanan",
                        fn($lq) => $lq->where("nama_layanan", "like", "%{$q}%"),
                    )
                    ->orWhereHas(
                        "user",
                        fn($uq) => $uq->where("nama_lengkap", "like", "%{$q}%"),
                    );
            })
            ->take(5)
            ->get()
            ->each(function ($p) use ($results) {
                $results->push([
                    "type" => "Pengajuan",
                    "title" => $p->no_registrasi ?: "Pengajuan #" . $p->id,
                    "sub" =>
                        ($p->layanan->nama_layanan ?? "-") .
                        " — " .
                        ($p->user->nama_lengkap ?? "-"),
                    "url" => route("admin.pengajuan.show", $p->id),
                ]);
            });

        // Search Aduan (nama_depan, nama_belakang, email)
        AduanMasyarakat::where(function ($query) use ($q) {
            $query
                ->where("nama_depan", "like", "%{$q}%")
                ->orWhere("nama_belakang", "like", "%{$q}%")
                ->orWhere("email", "like", "%{$q}%");
        })
            ->take(5)
            ->get()
            ->each(function ($aduan) use ($results) {
                $results->push([
                    "type" => "Aduan",
                    "title" => $aduan->full_name,
                    "sub" =>
                        $aduan->status_label .
                        " — " .
                        $aduan->created_at->format("d M Y"),
                    "url" => route("admin.aduan.show", $aduan->id),
                ]);
            });

        return response()->json($results->take(15)->values());
    }
}
