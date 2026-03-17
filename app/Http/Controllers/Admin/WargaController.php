<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn("role", ["warga", "rt"]);

        if ($request->filled("search")) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where("nama_lengkap", "like", "%{$s}%")
                    ->orWhere("email", "like", "%{$s}%")
                    ->orWhere("nik", "like", "%{$s}%");
            });
        }

        $warga = $query->orderBy("created_at", "desc")->paginate(10);
        $warga->appends($request->query());

        return view("admin.warga.index", compact("warga"));
    }

    public function show(User $user)
    {
        return view("admin.warga.show", compact("user"));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            "nama_lengkap" => "required|string|max:255",
            "nik" => "required|string|max:20",
            "tempat_lahir" => "nullable|string|max:100",
            "tanggal_lahir" => "nullable|date",
            "jenis_kelamin" => "nullable|in:Laki-laki,Perempuan",
            "status_perkawinan" => "nullable|string|max:50",
            "agama" => "nullable|string|max:50",
            "pekerjaan" => "nullable|string|max:100",
        ]);

        $user->update($validated);

        return redirect()
            ->route("admin.warga.show", $user)
            ->with("success", "Data warga berhasil diperbarui.");
    }

    public function updateRole(Request $request, User $user)
    {
        if (! in_array($user->role, ["warga", "rt"], true)) {
            return redirect()
                ->route("admin.warga.index")
                ->with("error", "Role pengguna ini tidak bisa diubah dari Database Warga.");
        }

        $validated = $request->validate([
            "role" => "required|in:warga,rt",
        ]);

        if ($user->role === $validated["role"]) {
            return back()->with("success", "Role pengguna tidak berubah.");
        }

        $user->update([
            "role" => $validated["role"],
        ]);

        return back()->with(
            "success",
            "Role pengguna berhasil diubah menjadi " .
                ($validated["role"] === "rt" ? "RT" : "Warga") .
                ".",
        );
    }

    public function toggleStatus(User $user)
    {
        $user->status_akun =
            $user->status_akun === "aktif" ? "nonaktif" : "aktif";
        $user->save();

        return redirect()
            ->route("admin.warga.show", $user)
            ->with("success", "Status akun berhasil diubah.");
    }
}
