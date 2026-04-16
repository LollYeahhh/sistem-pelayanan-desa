<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSurat;
use App\Support\ArsipExcelExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $arsip = $this->buildArsipQuery($request)
            ->orderBy("reviewed_at", "desc")
            ->paginate(10);

        $arsip->appends($request->query());

        $exportYear = $this->resolveExportYear($request);
        $selectedMonths = $this->resolveExportMonths($request);

        return view("admin.arsip.index", compact(
            "arsip",
            "exportYear",
            "selectedMonths",
        ));
    }

    public function export(
        Request $request,
        ArsipExcelExporter $exporter,
    ): BinaryFileResponse {
        $year = $this->resolveExportYear($request);
        $months = $this->resolveExportMonths($request);

        $arsip = $this->buildArsipQuery($request, true)
            ->orderBy("reviewed_at", "desc")
            ->orderBy("id", "desc")
            ->get();

        $path = $exporter->export($arsip, $year, $months);

        return response()
            ->download(
                $path,
                $this->buildExportFilename($year, $months),
                [
                    "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                ],
            )
            ->deleteFileAfterSend(true);
    }

    protected function buildArsipQuery(Request $request, bool $forExport = false)
    {
        $query = PermohonanSurat::with(["user", "layanan", "reviewer"])
            ->whereIn("status", ["disetujui", "ditolak", "selesai"])
            ->whereNotNull("reviewed_at");

        if ($request->filled("search")) {
            $search = trim((string) $request->input("search"));

            $query->whereHas("user", function ($q) use ($search) {
                $q->where("nama_lengkap", "like", "%{$search}%")->orWhere(
                    "nik",
                    "like",
                    "%{$search}%",
                );
            });
        }

        if ($request->filled("status")) {
            $query->where("status", $request->input("status"));
        }

        if ($forExport) {
            $year = $this->resolveExportYear($request);
            $months = $this->resolveExportMonths($request);

            $query->whereYear("reviewed_at", $year)->where(function ($monthQuery) use (
                $months
            ) {
                foreach ($months as $month) {
                    $monthQuery->orWhereMonth("reviewed_at", $month);
                }
            });

            return $query;
        }

        if ($request->filled("tanggal")) {
            $query->whereDate("reviewed_at", $request->input("tanggal"));
        }

        return $query;
    }

    protected function resolveExportYear(Request $request): int
    {
        $validated = $request->validate([
            "tahun" => ["nullable", "integer", "between:2000,2100"],
            "bulan" => ["nullable", "array"],
            "bulan.*" => ["integer", "between:1,12"],
        ]);

        return (int) ($validated["tahun"] ?? now("Asia/Jakarta")->format("Y"));
    }

    protected function resolveExportMonths(Request $request): array
    {
        $validated = $request->validate([
            "tahun" => ["nullable", "integer", "between:2000,2100"],
            "bulan" => ["nullable", "array"],
            "bulan.*" => ["integer", "between:1,12"],
        ]);

        $months = collect($validated["bulan"] ?? [now("Asia/Jakarta")->month])
            ->map(fn($month) => (int) $month)
            ->filter(fn($month) => $month >= 1 && $month <= 12)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $months !== [] ? $months : [now("Asia/Jakarta")->month];
    }

    protected function buildExportFilename(int $year, array $months): string
    {
        $monthToken = collect($months)
            ->map(fn($month) => str_pad((string) $month, 2, "0", STR_PAD_LEFT))
            ->implode("_");

        return sprintf("arsip-dokumen-%d-%s.xlsx", $year, $monthToken);
    }
}
