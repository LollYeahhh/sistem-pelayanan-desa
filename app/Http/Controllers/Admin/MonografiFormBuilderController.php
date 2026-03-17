<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonografiCategory;
use App\Models\MonografiField;
use Illuminate\Http\Request;

class MonografiFormBuilderController extends Controller
{
    public function show()
    {
        $categories = MonografiCategory::with("fields")
            ->orderBy("urutan")
            ->get();

        return view("admin.monografi.form-builder", compact("categories"));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            "kode" => "required|string|max:5",
            "nama_kategori" => "required|string|max:255",
            "icon" => "nullable|string|max:255",
        ]);

        $lastUrutan = MonografiCategory::max("urutan") ?? 0;

        MonografiCategory::create([
            "kode" => strtoupper($validated["kode"]),
            "nama_kategori" => $validated["nama_kategori"],
            "icon" => $validated["icon"] ?? null,
            "urutan" => $lastUrutan + 1,
        ]);

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Kategori monografi berhasil ditambahkan.");
    }

    public function updateCategory(
        Request $request,
        MonografiCategory $category,
    ) {
        $validated = $request->validate([
            "kode" => "required|string|max:5",
            "nama_kategori" => "required|string|max:255",
            "icon" => "nullable|string|max:255",
            "urutan" => "nullable|integer|min:1",
        ]);

        $category->update([
            "kode" => strtoupper($validated["kode"]),
            "nama_kategori" => $validated["nama_kategori"],
            "icon" => $validated["icon"] ?? null,
            "urutan" => $validated["urutan"] ?? $category->urutan,
        ]);

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Kategori monografi berhasil diperbarui.");
    }

    public function destroyCategory(MonografiCategory $category)
    {
        $category->delete();

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Kategori monografi berhasil dihapus.");
    }

    public function storeField(
        Request $request,
        MonografiCategory $category,
    ) {
        $validated = $request->validate([
            "label" => "required|string|max:255",
            "tipe_input" => "required|in:text,number,date,textarea",
            "urutan" => "nullable|integer|min:1",
        ]);

        $lastUrutan = $category->fields()->max("urutan") ?? 0;

        $category->fields()->create([
            "label" => $validated["label"],
            "tipe_input" => $validated["tipe_input"],
            "urutan" => $validated["urutan"] ?? $lastUrutan + 1,
        ]);

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Field monografi berhasil ditambahkan.");
    }

    public function updateField(Request $request, MonografiField $field)
    {
        $validated = $request->validate([
            "label" => "required|string|max:255",
            "tipe_input" => "required|in:text,number,date,textarea",
            "urutan" => "nullable|integer|min:1",
        ]);

        $field->update([
            "label" => $validated["label"],
            "tipe_input" => $validated["tipe_input"],
            "urutan" => $validated["urutan"] ?? $field->urutan,
        ]);

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Field monografi berhasil diperbarui.");
    }

    public function destroyField(MonografiField $field)
    {
        $field->delete();

        return redirect()
            ->route("admin.monografi.form-builder")
            ->with("success", "Field monografi berhasil dihapus.");
    }
}
