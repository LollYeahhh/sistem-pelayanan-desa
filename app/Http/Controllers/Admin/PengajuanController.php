<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananField;
use App\Models\PermohonanSurat;
use App\Support\LayananFormVersionManager;
use App\Support\PengajuanIdentityMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $permohonan->load([
            "user",
            "layanan",
            "formVersion.sections.fields",
            "data.field",
            "lampiran",
        ]);

        $formVersion = $permohonan->formVersion ?? LayananFormVersionManager::ensurePublishedVersion($permohonan->layanan);
        $formVersion->loadMissing("sections.fields");
        $permohonan->setRelation("formVersion", $formVersion);
        $permohonan->layanan->setRelation("sections", $formVersion->sections);

        $fieldStates = PengajuanIdentityMapper::buildFieldStates(
            $formVersion->sections->flatMap(fn($section) => $section->fields),
            $permohonan->user,
        );
        $identitySummary = PengajuanIdentityMapper::buildIdentitySummary($permohonan->user);

        return view("admin.pengajuan.show", compact("permohonan", "fieldStates", "identitySummary"));
    }

    public function previewLampiran(PermohonanSurat $permohonan, LayananField $field): BinaryFileResponse
    {
        [$path, $mimeType] = $this->resolveLampiranFile($permohonan, $field);

        return response()->file(Storage::disk("public")->path($path), [
            "Content-Type" => $mimeType,
            "Content-Disposition" => "inline; filename=\"" . basename($path) . "\"",
        ]);
    }

    public function downloadLampiran(PermohonanSurat $permohonan, LayananField $field): BinaryFileResponse
    {
        [$path, $mimeType] = $this->resolveLampiranFile($permohonan, $field);

        return response()->download(
            Storage::disk("public")->path($path),
            basename($path),
            ["Content-Type" => $mimeType],
        );
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

    protected function resolveLampiranFile(PermohonanSurat $permohonan, LayananField $field): array
    {
        $permohonan->loadMissing([
            "layanan",
            "formVersion.sections.fields",
            "lampiran",
            "data.field",
        ]);
        $field->loadMissing("section");

        $formVersion = $permohonan->formVersion ?? LayananFormVersionManager::ensurePublishedVersion($permohonan->layanan);

        if ((int) $field->section?->form_version_id !== (int) $formVersion->id) {
            abort(404);
        }

        $lampiran = $permohonan->lampiran->firstWhere("nama_dokumen", $field->label);
        $datum = $permohonan->data->firstWhere("field_id", $field->id);
        $path = $lampiran?->file_path ?? ($datum->value ?? null);
        $mimeType = $lampiran?->file_type;

        if (!$path || !Storage::disk("public")->exists($path)) {
            abort(404);
        }

        if (!$mimeType) {
            $mimeType = Storage::disk("public")->mimeType($path) ?: "application/octet-stream";
        }

        return [$path, $mimeType];
    }
}

