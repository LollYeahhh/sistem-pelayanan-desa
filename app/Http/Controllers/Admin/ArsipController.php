<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSurat;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSurat::with(["user", "layanan"])->whereIn("status", [
            "disetujui",
            "ditolak",
        ]);

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

        if ($request->filled("tanggal")) {
            $query->whereDate("reviewed_at", $request->tanggal);
        }

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $arsip = $query->orderBy("reviewed_at", "desc")->paginate(10);
        $arsip->appends($request->query());

        return view("admin.arsip.index", compact("arsip"));
    }
}
