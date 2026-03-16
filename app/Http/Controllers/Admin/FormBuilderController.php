<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\LayananFieldSection;
use App\Models\LayananField;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    public function show(Layanan $layanan)
    {
        $layanan->load("sections.fields");
        return view("admin.layanan.form-builder", compact("layanan"));
    }

    // ========================================
    // SECTION
    // ========================================

    public function storeSection(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            "nama_section" => "required|string|max:255",
        ]);

        $lastUrutan = $layanan->sections()->max("urutan") ?? 0;

        $layanan->sections()->create([
            "nama_section" => $validated["nama_section"],
            "urutan" => $lastUrutan + 1,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Section berhasil ditambahkan.");
    }

    public function destroySection(
        Layanan $layanan,
        LayananFieldSection $section,
    ) {
        $section->fields()->delete();
        $section->delete();

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Section berhasil dihapus.");
    }

    // ========================================
    // FIELD
    // ========================================

    public function storeField(
        Request $request,
        Layanan $layanan,
        LayananFieldSection $section,
    ) {
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
                ? json_encode(
                    array_map("trim", explode(",", $validated["opsi_select"])),
                )
                : null,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field berhasil ditambahkan.");
    }

    public function updateField(
        Request $request,
        Layanan $layanan,
        LayananField $field,
    ) {
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
                ? json_encode(
                    array_map("trim", explode(",", $validated["opsi_select"])),
                )
                : null,
        ]);

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field berhasil diperbarui.");
    }

    public function destroyField(Layanan $layanan, LayananField $field)
    {
        $field->delete();

        return redirect()
            ->route("admin.layanan.form-builder", $layanan)
            ->with("success", "Field berhasil dihapus.");
    }
}
