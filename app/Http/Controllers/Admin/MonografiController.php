<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSurat;
use Illuminate\Http\Request;

class MonografiController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSurat::with(["user", "layanan"])->where(
            "status",
            "disetujui",
        );

        if ($request->filled("search")) {
            $s = $request->search;
            $query->whereHas("user", function ($q) use ($s) {
                $q->where("nama_lengkap", "like", "%{$s}%");
            });
        }

        $data = $query->orderBy("reviewed_at", "desc")->paginate(10);
        $data->appends($request->query());

        return view("admin.monografi.index", compact("data"));
    }
}
