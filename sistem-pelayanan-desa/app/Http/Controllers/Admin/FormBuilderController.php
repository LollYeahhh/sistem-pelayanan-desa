<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananFieldSection;
use App\Support\LayananFormVersionManager;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    public function show(Layanan $layanan)
    {
        $publishedVersion = LayananFormVersionManager::ensurePublishedVersion($layanan);
        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);

        $layanan->setRelation("sections", $draftVersion->sections);

        return view("admin.layanan.form-builder", compact("layanan", "draftVersion", "publishedVersion"));
    }

    public function publish(Layanan $layanan)
    {
        $publishedVersion = LayananFormVersionManager::publishDraft($layanan);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Draft berhasil dipublikasikan sebagai versi {$publishedVersion->version_number}.");
    }

    public function storeSection(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            "nama_section" => "required|string|max:255",
        ]);

        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);
        $lastUrutan = $draftVersion->sections()->max("urutan") ?? 0;

        $draftVersion->sections()->create([
            "layanan_id" => $layanan->id,
            "nama_section" => $validated["nama_section"],
            "urutan" => $lastUrutan + 1,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Section berhasil ditambahkan ke draft.");
    }

    public function destroySection(Layanan $layanan, LayananFieldSection $section)
    {
        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);
        $this->guardDraftSection($draftVersion->id, $section);

        $section->fields()->delete();
        $section->delete();

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Section draft berhasil dihapus.");
    }

    public function storeField(Request $request, Layanan $layanan, LayananFieldSection $section)
    {
        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);
        $this->guardDraftSection($draftVersion->id, $section);

        $validated = $request->validate([
            "label" => "required|string|max:255",
            "tipe_input" => "required|in:text,number,date,select,file,textarea",
            "placeholder" => "nullable|string|max:255",
            "is_required" => "nullable",
            "urutan" => "nullable|integer|min:1",
            "opsi_select" => "nullable|string",
        ]);

        $lastUrutan = $section->fields()->max("urutan") ?? 0;

        $section->fields()->create([
            "label" => $validated["label"],
            "tipe_input" => $validated["tipe_input"],
            "placeholder" => $validated["placeholder"] ?? null,
            "is_required" => $request->has("is_required"),
            "urutan" => $validated["urutan"] ?? $lastUrutan + 1,
            "opsi_select" => !empty($validated["opsi_select"])
                ? json_encode(array_map("trim", explode(",", $validated["opsi_select"])))
                : null,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field draft berhasil ditambahkan.");
    }

    public function updateField(Request $request, Layanan $layanan, LayananField $field)
    {
        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);
        $field->loadMissing("section");
        $this->guardDraftField($draftVersion->id, $field);

        $validated = $request->validate([
            "label" => "required|string|max:255",
            "tipe_input" => "required|in:text,number,date,select,file,textarea",
            "placeholder" => "nullable|string|max:255",
            "is_required" => "nullable",
            "urutan" => "nullable|integer|min:1",
            "opsi_select" => "nullable|string",
        ]);

        $field->update([
            "label" => $validated["label"],
            "tipe_input" => $validated["tipe_input"],
            "placeholder" => $validated["placeholder"] ?? null,
            "is_required" => $request->has("is_required"),
            "urutan" => $validated["urutan"] ?? $field->urutan,
            "opsi_select" => !empty($validated["opsi_select"])
                ? json_encode(array_map("trim", explode(",", $validated["opsi_select"])))
                : null,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field draft berhasil diperbarui.");
    }

    public function destroyField(Layanan $layanan, LayananField $field)
    {
        $draftVersion = LayananFormVersionManager::ensureDraftVersion($layanan);
        $field->loadMissing("section");
        $this->guardDraftField($draftVersion->id, $field);

        $field->delete();

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field draft berhasil dihapus.");
    }

    protected function guardDraftSection(int $draftVersionId, LayananFieldSection $section): void
    {
        if ((int) $section->form_version_id !== $draftVersionId) {
            abort(404);
        }
    }

    protected function guardDraftField(int $draftVersionId, LayananField $field): void
    {
        if ((int) $field->section?->form_version_id !== $draftVersionId) {
            abort(404);
        }
    }
}
