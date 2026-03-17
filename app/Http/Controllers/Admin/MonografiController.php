<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonografiField;
use App\Models\User;
use Illuminate\Http\Request;

class MonografiController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) now("Asia/Jakarta")->format("Y");
        $totalFields = MonografiField::count();

        $query = User::query()
            ->where("role", "rt")
            ->withCount([
                "monografiEntries as filled_fields_count" => fn($q) => $q->where(
                    "tahun",
                    $tahun,
                ),
            ])
            ->withMax(
                [
                    "monografiEntries as last_monografi_update" => fn($q) => $q->where(
                        "tahun",
                        $tahun,
                    ),
                ],
                "updated_at",
            );

        if ($request->filled("search")) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where("nama_lengkap", "like", "%{$s}%")
                    ->orWhere("rt_rw", "like", "%{$s}%");
            });
        }

        if ($request->filled("status")) {
            if ($request->status === "selesai" && $totalFields > 0) {
                $query->having("filled_fields_count", ">=", $totalFields);
            } elseif ($request->status === "draft") {
                $query->having("filled_fields_count", ">", 0);

                if ($totalFields > 0) {
                    $query->having("filled_fields_count", "<", $totalFields);
                }
            } elseif ($request->status === "kosong") {
                $query->having("filled_fields_count", "=", 0);
            }
        }

        $data = $query
            ->orderByDesc("filled_fields_count")
            ->orderByDesc("last_monografi_update")
            ->paginate(10);

        $data->appends($request->query());

        return view("admin.monografi.index", compact("data", "tahun", "totalFields"));
    }
}
