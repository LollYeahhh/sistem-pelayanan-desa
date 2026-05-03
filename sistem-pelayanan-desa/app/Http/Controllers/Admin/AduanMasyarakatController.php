<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AduanMasyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AduanMasyarakatController extends Controller
{
    public function index(Request $request)
    {
        $query = AduanMasyarakat::query();

        if ($request->filled("search")) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where("nama_depan", "like", "%{$search}%")
                    ->orWhere("nama_belakang", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhere("isi_aduan", "like", "%{$search}%");
            });
        }

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $aduan = $query->latest()->paginate(10);
        $aduan->appends($request->query());

        return view("admin.aduan.index", compact("aduan"));
    }

    public function show(AduanMasyarakat $aduan)
    {
        return view("admin.aduan.show", compact("aduan"));
    }

    public function updateStatus(Request $request, AduanMasyarakat $aduan)
    {
        $validated = $request->validate([
            "status" => "required|in:baru,diproses,selesai",
        ]);

        $aduan->update($validated);

        return redirect()
            ->route("admin.aduan.show", $aduan)
            ->with("success", "Status aduan berhasil diperbarui.");
    }

    public function downloadLampiran(AduanMasyarakat $aduan)
    {
        abort_unless($aduan->lampiran_path, 404);
        abort_unless(Storage::disk("public")->exists($aduan->lampiran_path), 404);

        return Storage::disk("public")->download(
            $aduan->lampiran_path,
            basename($aduan->lampiran_path),
        );
    }
}
