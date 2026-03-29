<?php

namespace App\Http\Controllers\RT;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\MonografiCategory;
use App\Models\PermohonanData;
use App\Models\PermohonanLampiran;
use App\Models\PermohonanSurat;
use App\Notifications\AdminNewPengajuanNotification;
use App\Support\LayananFormVersionManager;
use App\Support\PengajuanIdentityMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RTController extends Controller
{
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

        return view("rt.dashboard", compact("user", "layananPreview", "permohonan"));
    }

    public function ajukanIndex()
    {
        $layanan = Layanan::where("is_active", true)->get();

        return view("rt.ajukan.index", compact("layanan"));
    }

    public function ajukanForm(Layanan $layanan)
    {
        $publishedVersion = LayananFormVersionManager::ensurePublishedVersion($layanan);
        $publishedVersion->load("sections.fields");
        $layanan->setRelation("sections", $publishedVersion->sections);

        $fieldStates = PengajuanIdentityMapper::buildFieldStates(
            $layanan->sections->flatMap(fn($section) => $section->fields),
            Auth::user(),
        );

        return view("rt.ajukan.form", compact("layanan", "fieldStates"));
    }

    public function ajukanStore(Request $request, Layanan $layanan)
    {
        $publishedVersion = LayananFormVersionManager::ensurePublishedVersion($layanan);
        $publishedVersion->load("sections.fields");
        $layanan->setRelation("sections", $publishedVersion->sections);
        $allFields = $layanan->sections->flatMap(fn($section) => $section->fields);

        $rules = [];
        $labels = [];

        foreach ($allFields as $field) {
            $key = "field_" . $field->id;

            if ($field->tipe_input === "file") {
                $fieldRules = ["nullable", "file", "max:2048"];

                if ($field->is_required) {
                    $fieldRules[0] = "required";
                }
            } else {
                $fieldRules = $field->is_required ? ["required", "string"] : ["nullable", "string"];
            }

            $rules[$key] = $fieldRules;
            $labels[$key] = $field->label;
        }

        $payload = PengajuanIdentityMapper::mergeProfileValuesIntoPayload(
            $allFields,
            Auth::user(),
            $request->all(),
        );

        $validated = Validator::make($payload, $rules, [], $labels)->validate();

        $permohonan = DB::transaction(function () use ($request, $layanan, $validated, $publishedVersion) {
            $permohonan = PermohonanSurat::create([
                "user_id" => Auth::id(),
                "layanan_id" => $layanan->id,
                "form_version_id" => $publishedVersion->id,
                "no_registrasi" => "REG-" . strtoupper(Str::random(8)),
                "status" => "menunggu",
            ]);

            foreach ($layanan->sections as $section) {
                foreach ($section->fields as $field) {
                    $key = "field_" . $field->id;

                    if ($field->tipe_input === "file") {
                        if ($request->hasFile($key)) {
                            $file = $request->file($key);
                            $path = $file->store("lampiran/" . $permohonan->id, "public");

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

            return $permohonan->load(["user", "layanan"]);
        });

        $this->dispatchAdminNotification(
            new AdminNewPengajuanNotification($permohonan),
        );

        return redirect()
            ->route("rt.riwayat.index")
            ->with("success", "Permohonan surat RT berhasil diajukan!");
    }

    public function riwayat(Request $request)
    {
        $query = PermohonanSurat::where("user_id", Auth::id())
            ->with("layanan")
            ->latest();

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $permohonan = $query->paginate(10);

        return view("rt.riwayat.index", compact("permohonan"));
    }

    public function riwayatShow(PermohonanSurat $permohonan)
    {
        if ($permohonan->user_id !== Auth::id()) {
            abort(403);
        }

        $permohonan->load(["layanan", "formVersion.sections.fields", "data.field", "lampiran", "reviewer"]);

        return view("rt.riwayat.show", compact("permohonan"));
    }

    public function profil()
    {
        $user = Auth::user();

        return view("rt.profil", compact("user"));
    }

    public function search(Request $request)
    {
        $q = trim($request->query("q", ""));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $results = collect();

        Layanan::where("is_active", true)
            ->where("nama_layanan", "like", "%{$q}%")
            ->take(5)
            ->get()
            ->each(function ($layanan) use ($results) {
                $results->push([
                    "type" => "Layanan",
                    "title" => $layanan->nama_layanan,
                    "sub" => "Ajukan layanan ini",
                    "url" => route("rt.ajukan.form", $layanan->id),
                ]);
            });

        PermohonanSurat::with("layanan")
            ->where("user_id", Auth::id())
            ->where(function ($query) use ($q) {
                $query
                    ->where("no_registrasi", "like", "%{$q}%")
                    ->orWhereHas("layanan", fn($lq) => $lq->where("nama_layanan", "like", "%{$q}%"));
            })
            ->take(5)
            ->get()
            ->each(function ($p) use ($results) {
                $results->push([
                    "type" => "Riwayat",
                    "title" => $p->no_registrasi ?: "Permohonan #" . $p->id,
                    "sub" => ($p->layanan->nama_layanan ?? "-") . " — " . ucfirst($p->status),
                    "url" => route("rt.riwayat.show", $p->id),
                ]);
            });

        return response()->json($results->take(10)->values());
    }

    public function profilUpdate(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            "nama_lengkap" => "required|string|max:255",
            "nik" => [
                "nullable",
                "digits:16",
                Rule::unique("users", "nik")->ignore($user->id),
            ],
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

        if (filled($user->nik)) {
            unset($validated["nik"]);
        }

        $user->update($validated);

        return back()->with("success", "Profil RT berhasil diperbarui!");
    }
}
