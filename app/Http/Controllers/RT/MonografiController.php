<?php

namespace App\Http\Controllers\RT;

use App\Http\Controllers\Controller;
use App\Models\MonografiCategory;
use App\Models\MonografiData;
use App\Models\User;
use Illuminate\Http\Request;

class MonografiController extends Controller
{
    public function index(Request $request)
    {
        $category = MonografiCategory::orderBy("urutan")->first();

        if (!$category) {
            [$rtOptions, $selectedRt, $categories] = $this->resolveContext(
                $request,
            );

            return view("rt.monografi.show", [
                "categories" => $categories,
                "currentCategory" => null,
                "fields" => collect(),
                "values" => collect(),
                "rtOptions" => $rtOptions,
                "selectedRt" => $selectedRt,
                "previousCategory" => null,
                "nextCategory" => null,
                "tahun" => (int) now("Asia/Jakarta")->format("Y"),
            ]);
        }

        return $this->renderCategory($request, $category);
    }

    public function show(Request $request, MonografiCategory $category)
    {
        return $this->renderCategory($request, $category);
    }

    public function update(Request $request, MonografiCategory $category)
    {
        [$rtOptions, $selectedRt, $categories] = $this->resolveContext(
            $request,
        );
        $fields = $category->fields()->orderBy("urutan")->get();
        $tahun = (int) now("Asia/Jakarta")->format("Y");

        $rules = [];

        foreach ($fields as $field) {
            $key = "field_" . $field->id;

            $rules[$key] = match ($field->tipe_input) {
                "number" => ["nullable", "numeric"],
                "date" => ["nullable", "date"],
                default => ["nullable", "string"],
            };
        }

        $validated = $request->validate($rules);

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

        $targetCategory = match ($request->input("action")) {
            "previous" => $previousCategory,
            "next" => $nextCategory,
            default => $category,
        };

        $message =
            $request->input("action") === "next" && !$nextCategory
                ? "Data monografi berhasil disimpan."
                : "Data monografi kategori berhasil disimpan.";

        return redirect()
            ->route("rt.monografi.show", [
                "category" => $targetCategory ?? $category,
                "rt" => $selectedRt->id,
            ])
            ->with("success", $message);
    }

    protected function renderCategory(
        Request $request,
        MonografiCategory $category,
    ) {
        [$rtOptions, $selectedRt, $categories] = $this->resolveContext(
            $request,
        );

        $currentCategory =
            $categories->firstWhere("id", $category->id) ??
            $category->load("fields");
        $fields = $currentCategory->fields()->orderBy("urutan")->get();
        $tahun = (int) now("Asia/Jakarta")->format("Y");

        $values = MonografiData::whereIn("field_id", $fields->pluck("id"))
            ->where("rt_user_id", $selectedRt->id)
            ->where("tahun", $tahun)
            ->pluck("value", "field_id");

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
            ),
        );
    }

    protected function resolveContext(Request $request): array
    {
        $rtOptions = User::where("role", "rt")
            ->orderBy("id")
            ->get()
            ->values()
            ->map(function ($user, $index) {
                $user->rt_label = "RT " . ($index + 1);

                return $user;
            });
        $selectedRt =
            $rtOptions->firstWhere("id", (int) $request->query("rt")) ??
            ($rtOptions->firstWhere("id", auth()->id()) ?? $rtOptions->first());

        if (!$selectedRt) {
            abort(404, "Data RT tidak tersedia.");
        }

        $categories = MonografiCategory::with("fields")
            ->orderBy("urutan")
            ->get();

        return [$rtOptions, $selectedRt, $categories];
    }
}

