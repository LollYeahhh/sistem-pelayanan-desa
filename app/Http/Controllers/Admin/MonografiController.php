<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonografiCategory;
use App\Models\MonografiData;
use App\Models\MonografiField;
use App\Models\MonografiSubmission;
use App\Models\User;
use App\Support\MonografiExcelExporter;
use App\Support\MonografiWilayahResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MonografiController extends Controller
{
    public function index(
        Request $request,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        $tahun = $this->resolveYear($request);
        $totalFields = MonografiField::count();
        $labelsByUserId = $wilayahResolver
            ->labelsByUserId($this->loadRtUsers());

        $data = $this->buildReportQuery($request, $tahun)
            ->orderByDesc("filled_fields_count")
            ->orderByDesc("last_monografi_update")
            ->paginate(10);

        $data->appends($request->query());
        $data->getCollection()->transform(function ($item) use (
            $labelsByUserId,
        ) {
            $item->resolved_status = $this->resolveStatus(
                $item->submission_status,
                (int) ($item->filled_fields_count ?? 0),
            );
            $item->resolved_wilayah_label =
                $item->submission_wilayah_label ??
                ($labelsByUserId[$item->id] ?? "-");

            return $item;
        });

        return view("admin.monografi.index", compact("data", "tahun", "totalFields"));
    }

    public function show(
        Request $request,
        User $rtUser,
        MonografiWilayahResolver $wilayahResolver,
    ) {
        abort_unless($rtUser->role === "rt", 404);

        $tahun = $this->resolveYear($request);
        $categories = MonografiCategory::with([
            "fields" => fn($query) => $query->orderBy("urutan")->orderBy("id"),
        ])
            ->orderBy("urutan")
            ->orderBy("id")
            ->get();

        $values = MonografiData::query()
            ->where("rt_user_id", $rtUser->id)
            ->where("tahun", $tahun)
            ->pluck("value", "field_id");

        $submission = MonografiSubmission::query()
            ->where("rt_user_id", $rtUser->id)
            ->where("tahun", $tahun)
            ->first();

        $rtOptions = $wilayahResolver->decorateRtUsers($this->loadRtUsers());
        $wilayahLabel =
            $submission?->wilayah_label ??
            ($rtOptions->firstWhere("id", $rtUser->id)?->wilayah_label ?? "-");
        $filledCount = MonografiData::query()
            ->where("rt_user_id", $rtUser->id)
            ->where("tahun", $tahun)
            ->whereIn("field_id", MonografiField::query()->pluck("id"))
            ->get()
            ->filter(fn($item) => filled($item->value))
            ->count();
        $status = $this->resolveStatus($submission?->status, $filledCount);

        return view("admin.monografi.show", compact(
            "rtUser",
            "tahun",
            "categories",
            "values",
            "submission",
            "wilayahLabel",
            "filledCount",
            "status",
        ));
    }

    public function reopen(Request $request, User $rtUser): RedirectResponse
    {
        abort_unless($rtUser->role === "rt", 404);

        $tahun = $this->resolveYear($request);
        $submission = MonografiSubmission::query()
            ->where("rt_user_id", $rtUser->id)
            ->where("tahun", $tahun)
            ->firstOrFail();

        $submission->update([
            "status" => "draft",
            "submitted_at" => null,
            "last_saved_at" => now(),
        ]);

        return redirect()
            ->route("admin.monografi.show", [
                "rtUser" => $rtUser,
                "tahun" => $tahun,
            ])
            ->with("success", "Status monografi berhasil dikembalikan ke draft.");
    }

    public function export(
        Request $request,
        MonografiExcelExporter $exporter,
    ): BinaryFileResponse {
        $tahun = $this->resolveYear($request);
        $path = $exporter->export($tahun);

        return response()
            ->download(
                $path,
                sprintf("monografi-desa-%d.xlsx", $tahun),
                [
                    "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                ],
            )
            ->deleteFileAfterSend(true);
    }

    protected function buildReportQuery(Request $request, int $tahun)
    {
        $submissionQuery = MonografiSubmission::query()
            ->where("tahun", $tahun)
            ->select([
                "rt_user_id",
                "status",
                "wilayah_label",
                "last_saved_at",
                "submitted_at",
            ]);

        $query = User::query()
            ->where("users.role", "rt")
            ->leftJoinSub(
                $submissionQuery,
                "submission_snapshot",
                fn($join) => $join->on(
                    "users.id",
                    "=",
                    "submission_snapshot.rt_user_id",
                ),
            )
            ->select([
                "users.*",
                "submission_snapshot.status as submission_status",
                "submission_snapshot.wilayah_label as submission_wilayah_label",
                "submission_snapshot.last_saved_at as submission_last_saved_at",
                "submission_snapshot.submitted_at as submission_submitted_at",
            ])
            ->withCount([
                "monografiEntries as filled_fields_count" => fn($q) => $q->where(
                    "tahun",
                    $tahun,
                )
                    ->whereNotNull("value")
                    ->where("value", "!=", ""),
            ])
            ->withMax(
                [
                    "monografiEntries as last_monografi_update" => fn($q) => $q->where(
                        "tahun",
                        $tahun,
                    ),
                ],
                "updated_at",
            );

        if ($request->filled("search")) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where("users.nama_lengkap", "like", "%{$s}%")
                    ->orWhere("users.rt_rw", "like", "%{$s}%")
                    ->orWhere(
                        "submission_snapshot.wilayah_label",
                        "like",
                        "%{$s}%",
                    );
            });
        }

        if ($request->filled("status")) {
            if ($request->status === "submitted") {
                $query->where("submission_snapshot.status", "submitted");
            } elseif ($request->status === "draft") {
                $query->havingRaw(
                    "(submission_status = 'draft' OR (submission_status IS NULL AND filled_fields_count > 0))",
                );
            } elseif ($request->status === "kosong") {
                $query->havingRaw(
                    "(submission_status IS NULL AND filled_fields_count = 0)",
                );
            }
        }

        return $query;
    }

    protected function resolveStatus(
        ?string $submissionStatus,
        int $filledFieldsCount,
    ): string {
        if ($submissionStatus === "submitted") {
            return "submitted";
        }

        if ($submissionStatus === "draft" || $filledFieldsCount > 0) {
            return "draft";
        }

        return "kosong";
    }

    protected function loadRtUsers()
    {
        return User::query()
            ->where("role", "rt")
            ->orderBy("id")
            ->get(["id", "nama_lengkap", "rt_rw"]);
    }

    protected function resolveYear(Request $request): int
    {
        $validated = $request->validate([
            "tahun" => ["nullable", "integer", "between:2000,2100"],
        ]);

        return (int) ($validated["tahun"] ?? now("Asia/Jakarta")->format("Y"));
    }
}
