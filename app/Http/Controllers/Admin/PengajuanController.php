<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSurat;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSurat::with(["user", "layanan"]);

        if ($request->filled("search")) {
            $s = $request->search;
            $query->whereHas("user", function ($q) use ($s) {
                $q->where("nama_lengkap", "like", "%{$s}%")->orWhere(
                    "nik",
                    "like",
                    "%{$s}%",
                );
            });
        }

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $pengajuan = $query->orderBy("created_at", "desc")->paginate(10);
        $pengajuan->appends($request->query());

        return view("admin.pengajuan.index", compact("pengajuan"));
    }

    public function show(PermohonanSurat $permohonan)
    {
        $permohonan->load(["user", "layanan"]);
        return view("admin.pengajuan.show", compact("permohonan"));
    }

    public function approve(PermohonanSurat $permohonan)
    {
        $permohonan->update([
            "status" => "disetujui",
            "reviewed_by" => auth()->id(),
            "reviewed_at" => now(),
        ]);

        return redirect()
            ->route("admin.pengajuan.show", $permohonan)
            ->with("success", "Pengajuan berhasil disetujui.");
    }

    public function reject(Request $request, PermohonanSurat $permohonan)
    {
        $request->validate(["alasan_tolak" => "required|string|max:500"]);

        $permohonan->update([
            "status" => "ditolak",
            "catatan" => $request->alasan_tolak,
            "reviewed_by" => auth()->id(),
            "reviewed_at" => now(),
        ]);

        return redirect()
            ->route("admin.pengajuan.show", $permohonan)
            ->with("success", "Pengajuan telah ditolak.");
    }
}
