<?php

namespace App\Http\Controllers\RT;

use App\Http\Controllers\Controller;
use App\Models\MonografiCategory;
use App\Models\MonografiData;
use App\Models\MonografiField;
use App\Models\MonografiSubmission;
use App\Models\User;
use App\Support\MonografiWilayahResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MonografiController extends Controller
{
    public function index(
        Request $request,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        $category = MonografiCategory::orderBy("urutan")->first();

        if (!$category) {
            [$rtOptions, $selectedRt, $categories, $wilayahLabel] = $this->resolveContext(
                $request,
                $wilayahResolver,
            );
            $tahun = $this->currentYear();

            return view("rt.monografi.show", [
                "categories" => $categories,
                "currentCategory" => null,
                "fields" => collect(),
                "values" => collect(),
                "rtOptions" => $rtOptions,
                "selectedRt" => $selectedRt,
                "previousCategory" => null,
                "nextCategory" => null,
                "tahun" => $tahun,
                "submission" => $this->resolveSubmission(
                    $selectedRt,
                    $tahun,
                    $wilayahLabel,
                ),
                "wilayahLabel" => $wilayahLabel,
                "isReadOnly" => false,
            ]);
        }

        return $this->renderCategory($request, $category, $wilayahResolver);
    }

    public function show(
        Request $request,
        MonografiCategory $category,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        return $this->renderCategory($request, $category, $wilayahResolver);
    }

    public function update(
        Request $request,
        MonografiCategory $category,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        [$rtOptions, $selectedRt, $categories, $wilayahLabel] = $this->resolveContext(
            $request,
            $wilayahResolver,
        );
        $tahun = $this->currentYear();
        $submission = MonografiSubmission::query()
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->first();

        if ($submission?->status === "submitted") {
            return redirect()
                ->route("rt.monografi.show", [
                    "category" => $category,
                    "rt" => $selectedRt->id,
                ])
                ->withErrors([
                    "submit" => "Data monografi sudah dikirim. Hubungi admin untuk membuka kembali draft.",
                ]);
        }

        $fields = $category->fields()->orderBy("urutan")->orderBy("id")->get();
        $validated = $this->validateFields($request, $fields);

        foreach ($fields as $field) {
            MonografiData::updateOrCreate(
                [
                    "field_id" => $field->id,
                    "rt_user_id" => $selectedRt->id,
                    "tahun" => $tahun,
                ],
                [
                    "value" => $validated["field_" . $field->id] ?? null,
                ],
            );
        }

        $orderedCategories = $categories->values();
        $currentIndex = $orderedCategories->search(
            fn($item) => (int) $item->id === (int) $category->id,
        );
        $previousCategory =
            $currentIndex !== false && $currentIndex > 0
                ? $orderedCategories[$currentIndex - 1]
                : null;
        $nextCategory =
            $currentIndex !== false &&
            $currentIndex < $orderedCategories->count() - 1
                ? $orderedCategories[$currentIndex + 1]
                : null;

        $action = $request->input("action", "save");

        if ($action === "submit") {
            $this->ensureAllFieldsFilled($selectedRt, $tahun);
            $this->storeSubmission(
                $selectedRt,
                $tahun,
                $wilayahLabel,
                "submitted",
            );

            return redirect()
                ->route("rt.monografi.show", [
                    "category" => $category,
                    "rt" => $selectedRt->id,
                ])
                ->with("success", "Data monografi berhasil dikirim.");
        }

        $this->storeSubmission($selectedRt, $tahun, $wilayahLabel, "draft");

        $targetCategory = $action === "save_and_next" && $nextCategory
            ? $nextCategory
            : $category;
        $message = $action === "save_and_next"
            ? "Draft kategori berhasil disimpan."
            : "Draft monografi berhasil disimpan.";

        return redirect()
            ->route("rt.monografi.show", [
                "category" => $targetCategory,
                "rt" => $selectedRt->id,
            ])
            ->with("success", $message);
    }

    protected function renderCategory(
        Request $request,
        MonografiCategory $category,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        [$rtOptions, $selectedRt, $categories, $wilayahLabel] = $this->resolveContext(
            $request,
            $wilayahResolver,
        );

        $currentCategory =
            $categories->firstWhere("id", $category->id) ??
            $category->load("fields");
        $fields = $currentCategory->fields()->orderBy("urutan")->orderBy("id")->get();
        $tahun = $this->currentYear();

        $values = MonografiData::whereIn("field_id", $fields->pluck("id"))
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->pluck("value", "field_id");

        $submission = $this->resolveSubmission(
            $selectedRt,
            $tahun,
            $wilayahLabel,
        );

        $orderedCategories = $categories->values();
        $currentIndex = $orderedCategories->search(
            fn($item) => (int) $item->id === (int) $currentCategory->id,
        );
        $previousCategory =
            $currentIndex !== false && $currentIndex > 0
                ? $orderedCategories[$currentIndex - 1]
                : null;
        $nextCategory =
            $currentIndex !== false &&
            $currentIndex < $orderedCategories->count() - 1
                ? $orderedCategories[$currentIndex + 1]
                : null;

        $isReadOnly = $submission->status === "submitted";

        return view(
            "rt.monografi.show",
            compact(
                "categories",
                "currentCategory",
                "fields",
                "values",
                "rtOptions",
                "selectedRt",
                "previousCategory",
                "nextCategory",
                "tahun",
                "submission",
                "wilayahLabel",
                "isReadOnly",
            ),
        );
    }

    protected function resolveContext(
        Request $request,
        MonografiWilayahResolver $wilayahResolver,
    ): array {
        $rtOptions = $wilayahResolver->decorateRtUsers(
            User::where("role", "rt")->orderBy("id")->get(),
        );
        $selectedRt =
            $rtOptions->firstWhere("id", (int) $request->query("rt")) ??
            ($rtOptions->firstWhere("id", auth()->id()) ?? $rtOptions->first());

        if (!$selectedRt) {
            abort(404, "Data RT tidak tersedia.");
        }

        $categories = MonografiCategory::with("fields")
            ->orderBy("urutan")
            ->orderBy("id")
            ->get();

        return [$rtOptions, $selectedRt, $categories, $selectedRt->wilayah_label];
    }

    protected function validateFields(Request $request, $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = "field_" . $field->id;

            $rules[$key] = match ($field->tipe_input) {
                "number" => ["nullable", "numeric"],
                "date" => ["nullable", "date"],
                default => ["nullable", "string"],
            };
        }

        return $request->validate($rules);
    }

    protected function ensureAllFieldsFilled(User $selectedRt, int $tahun): void
    {
        $allFields = MonografiField::query()
            ->orderBy("urutan")
            ->orderBy("id")
            ->get(["id", "label"]);

        if ($allFields->isEmpty()) {
            throw ValidationException::withMessages([
                "submit" => "Admin belum menyiapkan field monografi untuk dikirim.",
            ]);
        }

        $values = MonografiData::query()
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->pluck("value", "field_id");

        $missingFields = $allFields
            ->filter(fn($field) => !filled($values->get($field->id)))
            ->pluck("label");

        if ($missingFields->isNotEmpty()) {
            $sample = $missingFields->take(3)->implode(", ");
            $suffix = $missingFields->count() > 3 ? " dan field lainnya." : ".";

            throw ValidationException::withMessages([
                "submit" => "Lengkapi semua field monografi sebelum mengirim data. Contoh yang masih kosong: {$sample}{$suffix}",
            ]);
        }
    }

    protected function storeSubmission(
        User $selectedRt,
        int $tahun,
        string $wilayahLabel,
        string $status,
    ): MonografiSubmission {
        $submission = MonografiSubmission::firstOrNew([
            "rt_user_id" => $selectedRt->id,
            "tahun" => $tahun,
        ]);

        $submission->status = $status;
        $submission->wilayah_label = $wilayahLabel;
        $submission->last_saved_at = now();
        $submission->submitted_at = $status === "submitted" ? now() : null;
        $submission->save();

        return $submission;
    }

    protected function resolveSubmission(
        User $selectedRt,
        int $tahun,
        string $wilayahLabel,
    ): MonografiSubmission {
        $submission = MonografiSubmission::query()
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->first();

        if ($submission) {
            return $submission;
        }

        $hasData = MonografiData::query()
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->exists();

        return new MonografiSubmission([
            "rt_user_id" => $selectedRt->id,
            "tahun" => $tahun,
            "status" => $hasData ? "draft" : "draft",
            "wilayah_label" => $wilayahLabel,
        ]);
    }

    protected function currentYear(): int
    {
        return (int) now("Asia/Jakarta")->format("Y");
    }
}
