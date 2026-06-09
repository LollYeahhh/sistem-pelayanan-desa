<?php

namespace App\Support;

use App\Models\MonografiCategory;
use App\Models\MonografiData;
use App\Models\MonografiField;
use App\Models\MonografiSubmission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class MonografiExcelExporter
{
    /**
     * Workbook template contains additional sheets that are intentionally
     * preserved as-is. Only the sheets below are mapped to monografi_data.
     */
    private const RT_AGGREGATE_SHEETS = [
        "Penghitungan Profil Desa",
        "REKAP",
    ];

    /**
     * Sheet1 is the total-only summary sheet in the current template.
     */
    private const TOTAL_ONLY_SHEET = "Sheet1";

    public function __construct(
        protected MonografiWilayahResolver $wilayahResolver,
    ) {
    }

    public function export(int $year): string
    {
        $templatePath = resource_path("templates/monografi-template.xlsx");

        if (!File::exists($templatePath)) {
            throw new RuntimeException("Template monografi Excel tidak ditemukan.");
        }

        [$categories, $rtUsers, $valueLookup, $wilayahLookup] = $this->loadData(
            $year,
        );

        $spreadsheet = IOFactory::load($templatePath);

        $this->replaceYearInHeaders($spreadsheet, $year);
        $this->fillConfiguredSheets(
            $spreadsheet,
            $categories,
            $rtUsers,
            $valueLookup,
            $wilayahLookup,
        );

        $directory = storage_path("app/exports");
        File::ensureDirectoryExists($directory);

        $path = $directory . DIRECTORY_SEPARATOR . sprintf(
            "monografi-desa-%d-%s.xlsx",
            $year,
            Str::uuid(),
        );

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->setPreCalculateFormulas(true);
        $writer->save($path);

        return $path;
    }

    protected function loadData(int $year): array
    {
        $categories = MonografiCategory::with([
            "fields" => fn($query) => $query->orderBy("urutan")->orderBy("id"),
        ])
            ->orderBy("urutan")
            ->orderBy("id")
            ->get();

        $allRtUsers = User::query()
            ->where("role", "rt")
            ->orderBy("id")
            ->get(["id", "nama_lengkap", "rt_rw"]);

        $submittedSubmissions = MonografiSubmission::query()
            ->where("tahun", $year)
            ->where("status", "submitted")
            ->whereIn("rt_user_id", $allRtUsers->pluck("id"))
            ->get(["rt_user_id", "wilayah_label"]);

        $submittedRtIds = $submittedSubmissions
            ->pluck("rt_user_id")
            ->values();

        $rtUsers = $this->wilayahResolver->decorateRtUsers(
            $allRtUsers
                ->whereIn("id", $submittedRtIds)
                ->values(),
        );

        $fieldIds = $categories
            ->flatMap(fn(MonografiCategory $category) => $category->fields->pluck("id"))
            ->filter()
            ->values();

        $entries = $fieldIds->isEmpty()
            ? collect()
            : MonografiData::query()
                ->whereIn("field_id", $fieldIds)
                ->where("tahun", $year)
                ->whereIn("rt_user_id", $submittedRtIds)
                ->get(["field_id", "rt_user_id", "value"]);

        $valueLookup = [];

        foreach ($entries as $entry) {
            $valueLookup[$entry->field_id][$entry->rt_user_id] = $entry->value;
        }

        $submissionWilayahLookup = $submittedSubmissions
            ->pluck("wilayah_label", "rt_user_id")
            ->all();

        $fallbackWilayahLookup = $rtUsers
            ->mapWithKeys(fn(User $user) => [$user->id => $user->wilayah_label])
            ->all();

        return [
            $categories,
            $rtUsers,
            $valueLookup,
            array_replace($fallbackWilayahLookup, $submissionWilayahLookup),
        ];
    }

    protected function fillConfiguredSheets(
        Spreadsheet $spreadsheet,
        Collection $categories,
        Collection $rtUsers,
        array $valueLookup,
        array $wilayahLookup,
    ): void {
        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            if (
                in_array($sheet->getTitle(), self::RT_AGGREGATE_SHEETS, true)
            ) {
                $this->fillRtAggregateSheet(
                    $sheet,
                    $categories,
                    $rtUsers,
                    $valueLookup,
                    $wilayahLookup,
                );
                continue;
            }

            if ($sheet->getTitle() === self::TOTAL_ONLY_SHEET) {
                $this->fillTotalOnlySheet($sheet, $categories, $rtUsers, $valueLookup);
                continue;
            }

            // Preserve unsupported sheets exactly as defined in the template.
            // Example: Sheet2 contains age-distribution layout that is not sourced
            // from monografi_data in the current system, so it is intentionally left blank.
        }
    }

    protected function fillRtAggregateSheet(
        Worksheet $sheet,
        Collection $categories,
        Collection $rtUsers,
        array $valueLookup,
        array $wilayahLookup,
    ): void {
        $headerRow = 8;
        $dataStartRow = 9;
        $firstRtColumnIndex = Coordinate::columnIndexFromString("C");
        $totalColumnIndex =
            $this->findColumnIndexByValue($sheet, $headerRow, "JUMLAH") ??
            Coordinate::columnIndexFromString("N");

        $totalColumnIndex = $this->ensureRtColumns(
            $sheet,
            $headerRow,
            $firstRtColumnIndex,
            $totalColumnIndex,
            $rtUsers->count(),
        );

        $totalColumnLetter = Coordinate::stringFromColumnIndex($totalColumnIndex);
        $categoryStyle = $sheet->getStyle("A9:{$totalColumnLetter}9")->exportArray();
        $fieldStyle = $sheet->getStyle("A10:{$totalColumnLetter}10")->exportArray();
        $categoryRowHeight = $sheet->getRowDimension(9)->getRowHeight();
        $fieldRowHeight = $sheet->getRowDimension(10)->getRowHeight();
        $categoryMergeColumns = $this->mergeColumnsForRow($sheet, 9);
        $existingHighestDataRow = max($dataStartRow, $sheet->getHighestDataRow());

        $this->configureRtHeaders(
            $sheet,
            $headerRow,
            $firstRtColumnIndex,
            $totalColumnIndex,
            $rtUsers,
            $wilayahLookup,
        );
        $this->configureRtColumnVisibility(
            $sheet,
            $firstRtColumnIndex,
            $totalColumnIndex,
            $rtUsers->count(),
        );
        $this->removeMergesFromRow($sheet, $dataStartRow);
        $this->removeRowsStartingAt($sheet, $dataStartRow);

        $requiredRows = $this->requiredDataRowCount($categories);

        if ($requiredRows > 0) {
            $sheet->insertNewRowBefore($dataStartRow, $requiredRows);
        }

        $currentRow = $dataStartRow;

        foreach ($categories as $category) {
            $sheet->getStyle("A{$currentRow}:{$totalColumnLetter}{$currentRow}")
                ->applyFromArray($categoryStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight($categoryRowHeight);

            if ($categoryMergeColumns !== null) {
                $sheet->mergeCells(sprintf(
                    "%s%d:%s%d",
                    $categoryMergeColumns["start"],
                    $currentRow,
                    $categoryMergeColumns["end"] === $totalColumnLetter
                        ? $totalColumnLetter
                        : $categoryMergeColumns["end"],
                    $currentRow,
                ));
            }

            $sheet->setCellValue(
                "A{$currentRow}",
                Str::upper($category->kode . ". " . $category->nama_kategori),
            );
            $currentRow++;

            $fieldNumber = 1;

            foreach ($category->fields as $field) {
                $sheet->getStyle("A{$currentRow}:{$totalColumnLetter}{$currentRow}")
                    ->applyFromArray($fieldStyle);
                $sheet->getRowDimension($currentRow)->setRowHeight($fieldRowHeight);

                $sheet->setCellValue("A{$currentRow}", $fieldNumber);
                $sheet->setCellValue("B{$currentRow}", $field->label);

                $lastRtColumnIndex = max($firstRtColumnIndex, $totalColumnIndex - 1);
                $hasNumericValues = $field->tipe_input === "number";

                for (
                    $columnIndex = $firstRtColumnIndex;
                    $columnIndex < $totalColumnIndex;
                    $columnIndex++
                ) {
                    $rt = $rtUsers->get($columnIndex - $firstRtColumnIndex);
                    $cell = Coordinate::stringFromColumnIndex($columnIndex) . $currentRow;

                    if (!$rt) {
                        $sheet->setCellValue($cell, null);
                        continue;
                    }

                    $this->writeFieldValue(
                        $sheet,
                        $cell,
                        $field,
                        $valueLookup[$field->id][$rt->id] ?? null,
                    );
                }

                $totalCell = $totalColumnLetter . $currentRow;
                $aggregated = $this->aggregateFieldValue(
                    $field,
                    $rtUsers,
                    $valueLookup[$field->id] ?? [],
                    false,
                );

                if ($hasNumericValues && $rtUsers->isNotEmpty()) {
                    $sheet->setCellValue(
                        $totalCell,
                        sprintf(
                            "=SUM(%s%d:%s%d)",
                            Coordinate::stringFromColumnIndex($firstRtColumnIndex),
                            $currentRow,
                            Coordinate::stringFromColumnIndex($lastRtColumnIndex),
                            $currentRow,
                        ),
                    );
                } else {
                    $this->writeAggregateValue($sheet, $totalCell, $aggregated);
                }

                $fieldNumber++;
                $currentRow++;
            }
        }

        if ($existingHighestDataRow >= $currentRow) {
            $this->removeRowsStartingAt($sheet, $currentRow);
        }
    }

    protected function fillTotalOnlySheet(
        Worksheet $sheet,
        Collection $categories,
        Collection $rtUsers,
        array $valueLookup,
    ): void {
        $headerRow = 8;
        $dataStartRow = 9;
        $categoryStyle = $sheet->getStyle("A9:C9")->exportArray();
        $fieldStyle = $sheet->getStyle("A10:C10")->exportArray();
        $categoryRowHeight = $sheet->getRowDimension(9)->getRowHeight();
        $fieldRowHeight = $sheet->getRowDimension(10)->getRowHeight();
        $categoryMergeColumns = $this->mergeColumnsForRow($sheet, 9);
        $existingHighestDataRow = max($dataStartRow, $sheet->getHighestDataRow());

        $this->removeMergesFromRow($sheet, $dataStartRow);
        $this->removeRowsStartingAt($sheet, $dataStartRow);

        $requiredRows = $this->requiredDataRowCount($categories);

        if ($requiredRows > 0) {
            $sheet->insertNewRowBefore($dataStartRow, $requiredRows);
        }

        $currentRow = $dataStartRow;

        foreach ($categories as $category) {
            $sheet->getStyle("A{$currentRow}:C{$currentRow}")
                ->applyFromArray($categoryStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight($categoryRowHeight);

            if ($categoryMergeColumns !== null) {
                $sheet->mergeCells(sprintf(
                    "%s%d:%s%d",
                    $categoryMergeColumns["start"],
                    $currentRow,
                    $categoryMergeColumns["end"],
                    $currentRow,
                ));
            }

            $sheet->setCellValue(
                "A{$currentRow}",
                Str::upper($category->kode . ". " . $category->nama_kategori),
            );
            $currentRow++;

            foreach ($category->fields as $field) {
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")
                    ->applyFromArray($fieldStyle);
                $sheet->getRowDimension($currentRow)->setRowHeight($fieldRowHeight);
                $sheet->setCellValue("A{$currentRow}", null);
                $sheet->setCellValue("B{$currentRow}", $field->label);

                $aggregated = $this->aggregateFieldValue(
                    $field,
                    $rtUsers,
                    $valueLookup[$field->id] ?? [],
                    true,
                );

                $this->writeAggregateValue($sheet, "C{$currentRow}", $aggregated);
                $currentRow++;
            }
        }

        if ($existingHighestDataRow >= $currentRow) {
            $this->removeRowsStartingAt($sheet, $currentRow);
        }
    }

    protected function ensureRtColumns(
        Worksheet $sheet,
        int $headerRow,
        int $firstRtColumnIndex,
        int $totalColumnIndex,
        int $requiredRtCount,
    ): int {
        $existingRtCount = max(0, $totalColumnIndex - $firstRtColumnIndex);

        if ($requiredRtCount <= $existingRtCount) {
            return $totalColumnIndex;
        }

        $difference = $requiredRtCount - $existingRtCount;
        $sourceRtColumnIndex = max($firstRtColumnIndex, $totalColumnIndex - 1);
        $sourceRtColumnLetter = Coordinate::stringFromColumnIndex(
            $sourceRtColumnIndex,
        );
        $totalColumnLetter = Coordinate::stringFromColumnIndex($totalColumnIndex);
        $headerMergeRange = $this->findMergeRangeForCell(
            $sheet,
            $sourceRtColumnLetter,
            $headerRow,
        );

        $sheet->insertNewColumnBefore($totalColumnLetter, $difference);

        for (
            $columnIndex = $totalColumnIndex;
            $columnIndex < $totalColumnIndex + $difference;
            $columnIndex++
        ) {
            $newColumnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->duplicateStyle(
                $sheet->getStyle(
                    "{$sourceRtColumnLetter}{$headerRow}:{$sourceRtColumnLetter}" .
                        ($headerRow + 1),
                ),
                "{$newColumnLetter}{$headerRow}:{$newColumnLetter}" .
                    ($headerRow + 1),
            );
            $sheet->getColumnDimension($newColumnLetter)->setWidth(
                $sheet->getColumnDimension($sourceRtColumnLetter)->getWidth(),
            );

            if ($headerMergeRange !== null) {
                $startRow = $headerMergeRange["start_row"];
                $endRow = $headerMergeRange["end_row"];
                $sheet->mergeCells(
                    "{$newColumnLetter}{$startRow}:{$newColumnLetter}{$endRow}",
                );
            }
        }

        return $totalColumnIndex + $difference;
    }

    protected function configureRtHeaders(
        Worksheet $sheet,
        int $headerRow,
        int $firstRtColumnIndex,
        int $totalColumnIndex,
        Collection $rtUsers,
        array $wilayahLookup,
    ): void {
        $lastRtColumnIndex = max($firstRtColumnIndex, $totalColumnIndex - 1);

        for (
            $columnIndex = $firstRtColumnIndex;
            $columnIndex <= $lastRtColumnIndex;
            $columnIndex++
        ) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            $rt = $rtUsers->get($columnIndex - $firstRtColumnIndex);
            $label = $rt
                ? ($wilayahLookup[$rt->id] ?? $rt->wilayah_label ?? null)
                : null;

            $sheet->setCellValue("{$columnLetter}{$headerRow}", $label);

            if (
                $this->findMergeRangeForCell($sheet, $columnLetter, $headerRow) ===
                null
            ) {
                $sheet->setCellValue("{$columnLetter}" . ($headerRow + 1), null);
            }
        }
    }

    protected function configureRtColumnVisibility(
        Worksheet $sheet,
        int $firstRtColumnIndex,
        int $totalColumnIndex,
        int $requiredRtCount,
    ): void {
        $lastRtColumnIndex = max($firstRtColumnIndex, $totalColumnIndex - 1);

        for (
            $columnIndex = $firstRtColumnIndex;
            $columnIndex <= $lastRtColumnIndex;
            $columnIndex++
        ) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            $isActiveRtColumn =
                $requiredRtCount > 0 &&
                $columnIndex < ($firstRtColumnIndex + $requiredRtCount);

            $sheet->getColumnDimension($columnLetter)->setVisible($isActiveRtColumn);
        }

        $sheet->getColumnDimension(
            Coordinate::stringFromColumnIndex($totalColumnIndex),
        )->setVisible(true);
    }

    protected function replaceYearInHeaders(Spreadsheet $spreadsheet, int $year): void
    {
        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $highestColumnIndex = Coordinate::columnIndexFromString(
                $sheet->getHighestColumn(),
            );

            for ($row = 1; $row <= min(10, $sheet->getHighestRow()); $row++) {
                for ($column = 1; $column <= $highestColumnIndex; $column++) {
                    $cell = $sheet->getCell(
                        Coordinate::stringFromColumnIndex($column) . $row,
                    );
                    $value = $cell->getValue();

                    if (!is_string($value)) {
                        continue;
                    }

                    $updated = preg_replace(
                        "/\b20\d{2}\b/u",
                        (string) $year,
                        $value,
                    );

                    if ($updated !== null && $updated !== $value) {
                        $cell->setValueExplicit($updated, DataType::TYPE_STRING);
                    }
                }
            }
        }
    }

    protected function requiredDataRowCount(Collection $categories): int
    {
        return $categories->sum(
            fn(MonografiCategory $category) => 1 + $category->fields->count(),
        );
    }

    protected function removeRowsStartingAt(Worksheet $sheet, int $startRow): void
    {
        $highestRow = $sheet->getHighestRow();

        if ($highestRow >= $startRow) {
            $sheet->removeRow($startRow, $highestRow - $startRow + 1);
        }
    }

    protected function removeMergesFromRow(Worksheet $sheet, int $startRow): void
    {
        foreach (array_values($sheet->getMergeCells()) as $range) {
            [$start, $end] = Coordinate::rangeBoundaries($range);

            if ($start[1] >= $startRow || $end[1] >= $startRow) {
                $sheet->unmergeCells($range);
            }
        }
    }

    protected function mergeColumnsForRow(Worksheet $sheet, int $row): ?array
    {
        foreach (array_values($sheet->getMergeCells()) as $range) {
            [$start, $end] = Coordinate::rangeBoundaries($range);

            if ($start[1] === $row && $end[1] === $row) {
                return [
                    "start" => Coordinate::stringFromColumnIndex($start[0]),
                    "end" => Coordinate::stringFromColumnIndex($end[0]),
                ];
            }
        }

        return null;
    }

    protected function findMergeRangeForCell(
        Worksheet $sheet,
        string $columnLetter,
        int $row,
    ): ?array {
        foreach (array_values($sheet->getMergeCells()) as $range) {
            [$start, $end] = Coordinate::rangeBoundaries($range);
            $columnIndex = Coordinate::columnIndexFromString($columnLetter);

            if (
                $columnIndex >= $start[0] &&
                $columnIndex <= $end[0] &&
                $row >= $start[1] &&
                $row <= $end[1]
            ) {
                return [
                    "range" => $range,
                    "start_row" => $start[1],
                    "end_row" => $end[1],
                ];
            }
        }

        return null;
    }

    protected function findColumnIndexByValue(
        Worksheet $sheet,
        int $row,
        string $needle,
    ): ?int {
        $highestColumnIndex = Coordinate::columnIndexFromString(
            $sheet->getHighestColumn(),
        );

        for ($column = 1; $column <= $highestColumnIndex; $column++) {
            $value = trim((string) $sheet
                ->getCell(Coordinate::stringFromColumnIndex($column) . $row)
                ->getFormattedValue());

            if (Str::upper($value) === Str::upper($needle)) {
                return $column;
            }
        }

        return null;
    }

    protected function writeFieldValue(
        Worksheet $sheet,
        string $cell,
        MonografiField $field,
        mixed $value,
    ): void {
        if ($value === null || trim((string) $value) === "") {
            $sheet->setCellValue($cell, null);

            return;
        }

        if ($field->tipe_input === "number" && is_numeric($value)) {
            $numericValue = str_contains((string) $value, ".")
                ? (float) $value
                : (int) $value;

            $sheet->setCellValueExplicit($cell, $numericValue, DataType::TYPE_NUMERIC);

            return;
        }

        $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
    }

    protected function writeAggregateValue(
        Worksheet $sheet,
        string $cell,
        mixed $value,
    ): void {
        if ($value === null || $value === "") {
            $sheet->setCellValue($cell, null);

            return;
        }

        if (is_int($value) || is_float($value)) {
            $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_NUMERIC);

            return;
        }

        $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
    }

    protected function aggregateFieldValue(
        MonografiField $field,
        Collection $rtUsers,
        array $fieldValuesByRt,
        bool $allowTextJoin,
    ): mixed {
        $values = [];

        foreach ($rtUsers as $rtUser) {
            $value = $fieldValuesByRt[$rtUser->id] ?? null;

            if ($value === null || trim((string) $value) === "") {
                continue;
            }

            $values[] = $value;
        }

        if ($values === []) {
            return null;
        }

        if (
            $field->tipe_input === "number" &&
            collect($values)->every(fn($value) => is_numeric($value))
        ) {
            $sum = array_sum(array_map("floatval", $values));

            return floor($sum) === $sum ? (int) $sum : $sum;
        }

        $uniqueValues = array_values(
            array_unique(
                array_map(fn($value) => trim((string) $value), $values),
            ),
        );

        if (count($uniqueValues) === 1) {
            return $uniqueValues[0];
        }

        return $allowTextJoin ? implode("; ", $uniqueValues) : null;
    }
}
