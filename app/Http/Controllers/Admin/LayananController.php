<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    /**
     * Tampilkan daftar layanan dengan search & pagination
     */
    public function index(Request $request)
    {
        $query = Layanan::query();

        // Search
        if ($request->filled("search")) {
            $query->where("nama_layanan", "like", "%" . $request->search . "%");
        }

        $layanan = $query->orderBy("created_at", "desc")->paginate(10);
        $layanan->appends($request->query());

        return view("admin.layanan.index", compact("layanan"));
    }

    /**
     * Simpan layanan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "nama_layanan" =>
                "required|string|max:255|unique:layanan,nama_layanan",
            "icon" => "nullable|image|mimes:png,jpg,svg|max:2048",
            "is_active" => "required|in:0,1",
        ]);

        $data = [
            "nama_layanan" => $validated["nama_layanan"],
            "slug" => Str::slug($validated["nama_layanan"]),
            "is_active" => (bool) $validated["is_active"],
        ];

        // Upload icon jika ada
        if ($request->hasFile("icon")) {
            $file = $request->file("icon");
            $filename =
                Str::slug($validated["nama_layanan"]) .
                "." .
                $file->getClientOriginalExtension();
            $file->move(public_path("images/layanan"), $filename);
            $data["icon_path"] = $filename;
        }

        Layanan::create($data);

        return redirect()
            ->route("admin.layanan.index")
            ->with("success", "Layanan berhasil ditambahkan.");
    }

    /**
     * Update layanan
     */
    public function update(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            "nama_layanan" =>
                "required|string|max:255|unique:layanan,nama_layanan," .
                $layanan->id,
            "icon" => "nullable|image|mimes:png,jpg,svg|max:2048",
            "is_active" => "required|in:0,1",
        ]);

        $layanan->nama_layanan = $validated["nama_layanan"];
        $layanan->slug = Str::slug($validated["nama_layanan"]);
        $layanan->is_active = (bool) $validated["is_active"];

        // Upload icon baru jika ada
        if ($request->hasFile("icon")) {
            // Hapus icon lama jika ada
            if (
                $layanan->icon_path &&
                file_exists(
                    public_path("images/layanan/" . $layanan->icon_path),
                )
            ) {
                unlink(public_path("images/layanan/" . $layanan->icon_path));
            }

            $file = $request->file("icon");
            $filename =
                Str::slug($validated["nama_layanan"]) .
                "." .
                $file->getClientOriginalExtension();
            $file->move(public_path("images/layanan"), $filename);
            $layanan->icon_path = $filename;
        }

        $layanan->save();

        return redirect()
            ->route("admin.layanan.index")
            ->with("success", "Layanan berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(Layanan $layanan)
    {
        $layanan->is_active = !$layanan->is_active;
        $layanan->save();

        return redirect()
            ->route("admin.layanan.index")
            ->with("success", "Status layanan berhasil diubah.");
    }

    /**
     * Hapus layanan
     */
    public function destroy(Layanan $layanan)
    {
        // Hapus icon jika ada
        if (
            $layanan->icon_path &&
            file_exists(public_path("images/layanan/" . $layanan->icon_path))
        ) {
            unlink(public_path("images/layanan/" . $layanan->icon_path));
        }

        $layanan->delete();

        return redirect()
            ->route("admin.layanan.index")
            ->with("success", "Layanan berhasil dihapus.");
    }
}
