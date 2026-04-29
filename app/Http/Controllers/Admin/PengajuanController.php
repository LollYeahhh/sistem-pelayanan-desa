<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananField;
use App\Models\PermohonanSurat;
use App\Support\LayananFormVersionManager;
use App\Support\PengajuanIdentityMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSurat::with(["user", "layanan"])->forAdminInbox();

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
        $displaySections = $this->buildDisplaySections(
            $permohonan,
            $fieldStates,
        );

        return view("admin.pengajuan.show", compact(
            "permohonan",
            "displaySections",
        ));
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
        $permohonan->loadMissing(["user", "layanan"]);

        if (!in_array($permohonan->status, ["menunggu", "diproses"], true)) {
            return redirect()
                ->route("admin.pengajuan.show", $permohonan)
                ->with("error", "Pengajuan ini tidak dapat disetujui pada status saat ini.");
        }

        $permohonan->update([
            "status" => "disetujui",
            "alasan_penolakan" => null,
            "reviewed_by" => auth()->id(),
            "reviewed_at" => now(),
        ]);

        $this->dispatchDatabaseNotification(
            $permohonan->user,
            [
                "kind" => "status_disetujui",
                "title" => "Pengajuan disetujui",
                "message" => sprintf(
                    "%s untuk %s telah disetujui.",
                    $permohonan->no_registrasi ?: "Pengajuan #" . $permohonan->id,
                    $permohonan->layanan->nama_layanan ?? "layanan",
                ),
                "url" => $permohonan->user->role === "rt"
                    ? route("rt.riwayat.show", $permohonan)
                    : route("warga.riwayat.show", $permohonan),
            ],
            "App\\Notifications\\PengajuanStatusUpdatedNotification",
        );

        return redirect()
            ->route("admin.pengajuan.show", $permohonan)
            ->with("success", "Pengajuan berhasil disetujui.");
    }

    public function complete(Request $request, PermohonanSurat $permohonan)
    {
        $permohonan->loadMissing(["user", "layanan"]);

        if ($permohonan->status !== "disetujui") {
            return redirect()
                ->route("admin.pengajuan.show", $permohonan)
                ->with("error", "Dokumen hasil hanya dapat diunggah setelah pengajuan disetujui.");
        }

        $validated = $request->validate([
            "file_dokumen" => "required|file|mimes:pdf,doc,docx|max:5120",
            "catatan_admin" => "nullable|string|max:1000",
        ]);

        $file = $request->file("file_dokumen");
        $path = $file->store("dokumen-hasil/" . $permohonan->id, "local");
        $completedAt = now();

        $permohonan->update([
            "status" => "selesai",
            "file_dokumen_path" => $path,
            "file_dokumen_nama" => $file->getClientOriginalName(),
            "file_dokumen_mime" => $file->getClientMimeType(),
            "catatan_admin" => $validated["catatan_admin"] ?? null,
            "alasan_penolakan" => null,
            "reviewed_by" => auth()->id(),
            "reviewed_at" => $completedAt,
            "deadline" => $completedAt->copy()->addDays(7),
            "dokumen_selesai_at" => $completedAt,
        ]);

        $this->dispatchDatabaseNotification(
            $permohonan->user,
            [
                "kind" => "status_selesai",
                "title" => "Dokumen selesai diproses",
                "message" => sprintf(
                    "%s untuk %s telah selesai dan dokumen hasil dapat diunduh.",
                    $permohonan->no_registrasi ?: "Pengajuan #" . $permohonan->id,
                    $permohonan->layanan->nama_layanan ?? "layanan",
                ),
                "url" => $this->resolveHistoryRoute($permohonan),
            ],
            "App\\Notifications\\PengajuanStatusUpdatedNotification",
        );

        return redirect()
            ->route("admin.pengajuan.show", $permohonan)
            ->with("success", "Dokumen hasil berhasil diunggah dan pengajuan ditandai selesai.");
    }

    public function reject(Request $request, PermohonanSurat $permohonan)
    {
        $request->validate(["alasan_tolak" => "required|string|max:500"]);
        $permohonan->loadMissing(["user", "layanan"]);

        if (!in_array($permohonan->status, ["menunggu", "diproses", "disetujui"], true)) {
            return redirect()
                ->route("admin.pengajuan.show", $permohonan)
                ->with("error", "Pengajuan ini tidak dapat ditolak pada status saat ini.");
        }

        $permohonan->update([
            "status" => "ditolak",
            "alasan_penolakan" => $request->alasan_tolak,
            "catatan_admin" => null,
            "file_dokumen_path" => null,
            "file_dokumen_nama" => null,
            "file_dokumen_mime" => null,
            "dokumen_selesai_at" => null,
            "reviewed_by" => auth()->id(),
            "reviewed_at" => now(),
        ]);

        $message = sprintf(
            "%s untuk %s ditolak. Alasan: %s",
            $permohonan->no_registrasi ?: "Pengajuan #" . $permohonan->id,
            $permohonan->layanan->nama_layanan ?? "layanan",
            Str::limit($request->alasan_tolak, 120),
        );

        $this->dispatchDatabaseNotification(
            $permohonan->user,
            [
                "kind" => "status_ditolak",
                "title" => "Pengajuan ditolak",
                "message" => $message,
                "url" => $this->resolveHistoryRoute($permohonan),
            ],
            "App\\Notifications\\PengajuanStatusUpdatedNotification",
        );

        return redirect()
            ->route("admin.pengajuan.show", $permohonan)
            ->with("success", "Pengajuan telah ditolak.");
    }

    public function downloadDokumen(PermohonanSurat $permohonan)
    {
        $user = auth()->user();

        if (!$user || ($user->id !== $permohonan->user_id && !$user->isAdmin())) {
            abort(403);
        }

        if (
            !$permohonan->file_dokumen_path ||
            !Storage::disk("local")->exists($permohonan->file_dokumen_path)
        ) {
            abort(404, "Dokumen tidak ditemukan.");
        }

        $downloadName = $permohonan->file_dokumen_nama;

        if (!$downloadName) {
            $extension = pathinfo($permohonan->file_dokumen_path, PATHINFO_EXTENSION);
            $downloadName = ($permohonan->no_registrasi ?: "permohonan-" . $permohonan->id)
                . ($extension ? "." . $extension : "");
        }

        return Storage::disk("local")->download(
            $permohonan->file_dokumen_path,
            $downloadName,
            [
                "Content-Type" => $permohonan->file_dokumen_mime ?: "application/octet-stream",
            ],
        );
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

    protected function buildDisplaySections(PermohonanSurat $permohonan, array $fieldStates)
    {
        $dataLookup = $permohonan->data->keyBy("field_id");
        $displaySections = collect();

        foreach ($permohonan->layanan->sections as $section) {
            $sectionItems = collect();

            foreach ($section->fields as $field) {
                $fieldState = $fieldStates[$field->id] ?? [
                    "is_identity" => false,
                    "is_locked" => false,
                    "value" => null,
                ];
                $datum = $dataLookup->get($field->id);
                $submittedValue = trim((string) ($datum->value ?? ""));

                if ($field->tipe_input === "file") {
                    $lampiran = $permohonan->lampiran->firstWhere("nama_dokumen", $field->label);
                    $filePath = $lampiran?->file_path ?? ($datum->value ?? null);

                    if (!$filePath || !Storage::disk("public")->exists($filePath)) {
                        continue;
                    }

                    $mimeType = $lampiran?->file_type ?: Storage::disk("public")->mimeType($filePath);
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $isImage = $mimeType
                        ? str_starts_with($mimeType, "image/")
                        : in_array($extension, ["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"]);
                    $isPdf = $mimeType === "application/pdf" || $extension === "pdf";

                    $sectionItems->push([
                        "type" => "file",
                        "label" => $field->label,
                        "filename" => basename($filePath),
                        "status" => "Telah Diunggah",
                        "is_image" => $isImage,
                        "is_pdf" => $isPdf,
                        "preview_type" => $isImage ? "image" : ($isPdf ? "pdf" : "file"),
                        "preview_url" => route("admin.pengajuan.lampiran.preview", [$permohonan, $field]),
                        "download_url" => route("admin.pengajuan.lampiran.download", [$permohonan, $field]),
                    ]);

                    continue;
                }

                if ($fieldState["is_identity"]) {
                    $displayValue = $fieldState["is_locked"]
                        ? trim((string) ($fieldState["value"] ?? ""))
                        : $submittedValue;

                    if ($displayValue !== "") {
                        $sectionItems->push([
                            "type" => "text",
                            "label" => $field->label,
                            "value" => $displayValue,
                            "is_textarea" => false,
                        ]);
                    }

                    continue;
                }

                if ($submittedValue !== "") {
                    $sectionItems->push([
                        "type" => "text",
                        "label" => $field->label,
                        "value" => $submittedValue,
                        "is_textarea" => $field->tipe_input === "textarea",
                    ]);
                }
            }

            if ($sectionItems->isNotEmpty()) {
                $displaySections->push([
                    "title" => $section->nama_section,
                    "items" => $sectionItems,
                ]);
            }
        }

        return $displaySections;
    }

    protected function resolveHistoryRoute(PermohonanSurat $permohonan): string
    {
        return $permohonan->user->role === "rt"
            ? route("rt.riwayat.show", $permohonan)
            : route("warga.riwayat.show", $permohonan);
    }
}
