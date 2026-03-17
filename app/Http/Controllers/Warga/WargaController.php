<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\PermohonanSurat;
use App\Models\PermohonanData;
use App\Models\PermohonanLampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WargaController extends Controller
{
    /**
     * Dashboard warga
     */
    public function dashboard()
    {
        $user = Auth::user();

        $layananPreview = Layanan::where("is_active", true)
            ->orderBy("id")
            ->take(6)
            ->get();

        $permohonan = PermohonanSurat::where("user_id", $user->id)
            ->with("layanan")
            ->latest()
            ->take(2)
            ->get();

        return view(
            "warga.dashboard",
            compact("user", "layananPreview", "permohonan"),
        );
    }

    /**
     * Step 1 — Pilih Layanan
     */
    public function ajukanIndex()
    {
        $layanan = Layanan::where("is_active", true)->get();
        return view("warga.ajukan.index", compact("layanan"));
    }

    /**
     * Step 2 — Form Pengisian
     */
    public function ajukanForm(Layanan $layanan)
    {
        $layanan->load(["sections.fields"]);
        return view("warga.ajukan.form", compact("layanan"));
    }

    /**
     * Store permohonan baru
     */
    public function ajukanStore(Request $request, Layanan $layanan)
    {
        $layanan->load(["sections.fields"]);

        // Build validation rules dynamically
        $rules = [];
        $labels = [];

        foreach ($layanan->sections as $section) {
            foreach ($section->fields as $field) {
                $key = "field_" . $field->id;

                if ($field->tipe_input === "file") {
                    $fieldRules = ["nullable", "file", "max:2048"];
                    if ($field->is_required) {
                        $fieldRules[0] = "required";
                    }
                } else {
                    $fieldRules = $field->is_required
                        ? ["required", "string"]
                        : ["nullable", "string"];
                }

                $rules[$key] = $fieldRules;
                $labels[$key] = $field->label;
            }
        }

        $validated = $request->validate($rules, [], $labels);

        DB::transaction(function () use ($request, $layanan, $validated) {
            // Create permohonan
            $noReg = "REG-" . strtoupper(Str::random(8));
            $permohonan = PermohonanSurat::create([
                "user_id" => Auth::id(),
                "layanan_id" => $layanan->id,
                "no_registrasi" => $noReg,
                "status" => "menunggu",
            ]);

            // Save field data
            foreach ($layanan->sections as $section) {
                foreach ($section->fields as $field) {
                    $key = "field_" . $field->id;

                    if ($field->tipe_input === "file") {
                        if ($request->hasFile($key)) {
                            $file = $request->file($key);
                            $path = $file->store(
                                "lampiran/" . $permohonan->id,
                                "public",
                            );

                            PermohonanLampiran::create([
                                "permohonan_id" => $permohonan->id,
                                "nama_dokumen" => $field->label,
                                "file_path" => $path,
                                "file_type" => $file->getClientMimeType(),
                            ]);

                            PermohonanData::create([
                                "permohonan_id" => $permohonan->id,
                                "field_id" => $field->id,
                                "value" => $path,
                            ]);
                        }
                    } else {
                        PermohonanData::create([
                            "permohonan_id" => $permohonan->id,
                            "field_id" => $field->id,
                            "value" => $validated[$key] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route("warga.riwayat.index")
            ->with("success", "Permohonan surat berhasil diajukan!");
    }

    /**
     * Riwayat & Status
     */
    public function riwayat(Request $request)
    {
        $query = PermohonanSurat::where("user_id", Auth::id())
            ->with("layanan")
            ->latest();

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $permohonan = $query->paginate(10);

        return view("warga.riwayat.index", compact("permohonan"));
    }

    /**
     * Detail permohonan
     */
    public function riwayatShow(PermohonanSurat $permohonan)
    {
        // Ensure user can only see their own
        if ($permohonan->user_id !== Auth::id()) {
            abort(403);
        }

        $permohonan->load(["layanan", "data.field", "lampiran", "reviewer"]);

        return view("warga.riwayat.show", compact("permohonan"));
    }

    /**
     * Profil
     */
    public function profil()
    {
        $user = Auth::user();
        return view("warga.profil", compact("user"));
    }

    /**
     * Update profil
     */
    public function profilUpdate(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            "nama_lengkap" => "required|string|max:255",
            "tempat_lahir" => "nullable|string|max:100",
            "tanggal_lahir" => "nullable|date",
            "jenis_kelamin" => "nullable|in:Laki-laki,Perempuan",
            "agama" => "nullable|string|max:50",
            "status_perkawinan" => "nullable|string|max:50",
            "pekerjaan" => "nullable|string|max:100",
            "alamat" => "nullable|string|max:500",
            "rt_rw" => "nullable|string|max:20",
            "desa" => "nullable|string|max:100",
            "kecamatan" => "nullable|string|max:100",
            "no_hp" => "nullable|string|max:20",
        ]);

        $user->update($validated);

        return back()->with("success", "Profil berhasil diperbarui!");
    }
}
