<?php

namespace Tests\Feature;

use App\Models\MonografiCategory;
use App\Models\MonografiData;
use App\Models\MonografiField;
use App\Models\MonografiSubmission;
use App\Models\User;
use App\Support\MonografiExcelExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class MonografiExcelExportTest extends TestCase
{
    use RefreshDatabase;

    private static int $userSequence = 1200000000000000;

    public function test_admin_can_download_monografi_excel_export(): void
    {
        $admin = $this->createUser("admin");
        $this->seedMonografiFixture(2026);

        $this->actingAs($admin)
            ->get(route("admin.monografi.export", ["tahun" => 2026]))
            ->assertOk()
            ->assertDownload("monografi-desa-2026.xlsx");
    }

    public function test_export_uses_selected_year_data(): void
    {
        $rtUser = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => "01/01",
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtUser->id,
            "tahun" => 2025,
            "value" => "10",
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtUser->id,
            "tahun" => 2026,
            "value" => "25",
        ]);
        $this->submitMonografi($rtUser, 2026);

        $exporter = app(MonografiExcelExporter::class);
        $path = $exporter->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("REKAP");

        $targetRow = $this->findRowByColumnValue($sheet, "B", "Jumlah Penduduk Laki-laki");

        $this->assertNotNull($targetRow);
        $this->assertSame(25, (int) $sheet->getCell("C{$targetRow}")->getCalculatedValue());
    }

    public function test_export_only_includes_submitted_monografi_data(): void
    {
        $submittedRt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Submitted",
            "rt_rw" => "01/01",
        ]);
        $draftRt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Draft",
            "rt_rw" => "02/01",
        ]);
        $legacyRt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Legacy",
            "rt_rw" => "03/01",
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        foreach ([
            [$submittedRt, "10"],
            [$draftRt, "99"],
            [$legacyRt, "77"],
        ] as [$rtUser, $value]) {
            MonografiData::create([
                "field_id" => $field->id,
                "rt_user_id" => $rtUser->id,
                "tahun" => 2026,
                "value" => $value,
            ]);
        }

        $this->submitMonografi($submittedRt, 2026);
        $this->submitMonografi($draftRt, 2026, status: "draft");

        $path = app(MonografiExcelExporter::class)->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("REKAP");

        $targetRow = $this->findRowByColumnValue($sheet, "B", "Jumlah Penduduk Laki-laki");

        $this->assertNotNull($targetRow);
        $this->assertSame("RT. 01/01", $sheet->getCell("C8")->getValue());
        $this->assertSame(10, (int) $sheet->getCell("C{$targetRow}")->getCalculatedValue());
        $this->assertSame(10, (int) $sheet->getCell("N{$targetRow}")->getCalculatedValue());
        $this->assertNull($sheet->getCell("D8")->getValue());
        $this->assertNull($sheet->getCell("D{$targetRow}")->getValue());
    }

    public function test_export_inserts_extra_fields_categories_and_rt_columns(): void
    {
        $rtUsers = collect();

        for ($i = 1; $i <= 12; $i++) {
            $rtUsers->push($this->createUser("rt", [
                "nama_lengkap" => "Ketua RT {$i}",
                "rt_rw" => str_pad((string) $i, 2, "0", STR_PAD_LEFT) . "/01",
            ]));
        }

        $categoryA = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $fieldExisting = MonografiField::create([
            "category_id" => $categoryA->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        $fieldExtra = MonografiField::create([
            "category_id" => $categoryA->id,
            "label" => "Field Tambahan A",
            "tipe_input" => "number",
            "urutan" => 2,
        ]);

        $categoryL = MonografiCategory::create([
            "kode" => "L",
            "nama_kategori" => "Kategori Baru",
            "urutan" => 12,
        ]);

        $fieldCategoryL = MonografiField::create([
            "category_id" => $categoryL->id,
            "label" => "Jumlah Entitas Baru",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        foreach ($rtUsers as $index => $rtUser) {
            $this->submitMonografi($rtUser, 2026);

            MonografiData::create([
                "field_id" => $fieldExisting->id,
                "rt_user_id" => $rtUser->id,
                "tahun" => 2026,
                "value" => (string) ($index + 1),
            ]);

            MonografiData::create([
                "field_id" => $fieldExtra->id,
                "rt_user_id" => $rtUser->id,
                "tahun" => 2026,
                "value" => (string) (($index + 1) * 2),
            ]);

            MonografiData::create([
                "field_id" => $fieldCategoryL->id,
                "rt_user_id" => $rtUser->id,
                "tahun" => 2026,
                "value" => "1",
            ]);
        }

        $path = app(MonografiExcelExporter::class)->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("REKAP");

        $this->assertSame("RT. 12/01", $sheet->getCell("N8")->getValue());
        $this->assertSame("JUMLAH", $sheet->getCell("O8")->getValue());

        $extraFieldRow = $this->findRowByColumnValue($sheet, "B", "Field Tambahan A");
        $newCategoryRow = $this->findRowByColumnPrefix($sheet, "A", "L.");
        $newCategoryFieldRow = $this->findRowByColumnValue($sheet, "B", "Jumlah Entitas Baru");

        $this->assertNotNull($extraFieldRow);
        $this->assertNotNull($newCategoryRow);
        $this->assertNotNull($newCategoryFieldRow);
        $this->assertTrue($extraFieldRow < $newCategoryRow);
        $this->assertSame("=SUM(C{$extraFieldRow}:N{$extraFieldRow})", $sheet->getCell("O{$extraFieldRow}")->getValue());
    }

    public function test_export_uses_submitted_submission_wilayah_snapshots(): void
    {
        $rtWithSnapshot = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Snapshot",
            "rt_rw" => null,
        ]);
        $rtWithoutSnapshot = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Fallback",
            "rt_rw" => null,
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        MonografiSubmission::create([
            "rt_user_id" => $rtWithSnapshot->id,
            "tahun" => 2026,
            "status" => "submitted",
            "wilayah_label" => "RT PILIHAN 07",
            "last_saved_at" => now(),
            "submitted_at" => now(),
        ]);

        MonografiSubmission::create([
            "rt_user_id" => $rtWithoutSnapshot->id,
            "tahun" => 2026,
            "status" => "submitted",
            "wilayah_label" => "RT 02",
            "last_saved_at" => now(),
            "submitted_at" => now(),
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtWithSnapshot->id,
            "tahun" => 2026,
            "value" => "12",
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtWithoutSnapshot->id,
            "tahun" => 2026,
            "value" => "8",
        ]);

        $path = app(MonografiExcelExporter::class)->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("REKAP");

        $this->assertSame("RT PILIHAN 07", $sheet->getCell("C8")->getValue());
        $this->assertSame("RT 02", $sheet->getCell("D8")->getValue());
    }

    public function test_export_hides_unused_rt_columns_and_keeps_total_visible(): void
    {
        $rtOne = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => null,
        ]);
        $rtTwo = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 02",
            "rt_rw" => null,
        ]);
        $rtThree = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 03",
            "rt_rw" => null,
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        foreach ([$rtOne, $rtTwo, $rtThree] as $index => $rtUser) {
            $this->submitMonografi($rtUser, 2026);

            MonografiData::create([
                "field_id" => $field->id,
                "rt_user_id" => $rtUser->id,
                "tahun" => 2026,
                "value" => (string) ($index + 10),
            ]);
        }

        $path = app(MonografiExcelExporter::class)->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("REKAP");

        $this->assertTrue($sheet->getColumnDimension("C")->getVisible());
        $this->assertTrue($sheet->getColumnDimension("D")->getVisible());
        $this->assertTrue($sheet->getColumnDimension("E")->getVisible());
        $this->assertFalse($sheet->getColumnDimension("F")->getVisible());
        $this->assertFalse($sheet->getColumnDimension("M")->getVisible());
        $this->assertTrue($sheet->getColumnDimension("N")->getVisible());

        $targetRow = $this->findRowByColumnValue($sheet, "B", "Jumlah Penduduk Laki-laki");
        $this->assertNotNull($targetRow);
        $this->assertSame("=SUM(C{$targetRow}:M{$targetRow})", $sheet->getCell("N{$targetRow}")->getValue());
        $this->assertSame(33, (int) $sheet->getCell("N{$targetRow}")->getCalculatedValue());
    }

    public function test_export_keeps_unsupported_sheet2_unchanged_and_blank(): void
    {
        $rtUser = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => "01/01",
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtUser->id,
            "tahun" => 2026,
            "value" => "99",
        ]);
        $this->submitMonografi($rtUser, 2026);

        $path = app(MonografiExcelExporter::class)->export(2026);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName("Sheet2");

        $this->assertSame("USIA PENDUDUK DESA DUKUHTENGAH", $sheet->getCell("A1")->getValue());
        $this->assertSame(1, (int) $sheet->getCell("B4")->getValue());
        $this->assertSame("", (string) $sheet->getCell("C4")->getValue());
        $this->assertSame("", (string) $sheet->getCell("G4")->getValue());
    }

    private function seedMonografiFixture(int $year, int $baseValue = 25): void
    {
        $rtUser = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => "01/01",
        ]);

        $category = MonografiCategory::create([
            "kode" => "A",
            "nama_kategori" => "Potensi Sumber Daya Manusia",
            "urutan" => 1,
        ]);

        $field = MonografiField::create([
            "category_id" => $category->id,
            "label" => "Jumlah Penduduk Laki-laki",
            "tipe_input" => "number",
            "urutan" => 1,
        ]);

        MonografiData::create([
            "field_id" => $field->id,
            "rt_user_id" => $rtUser->id,
            "tahun" => $year,
            "value" => (string) $baseValue,
        ]);
        $this->submitMonografi($rtUser, $year);
    }

    private function submitMonografi(
        User $rtUser,
        int $year,
        ?string $wilayahLabel = null,
        string $status = "submitted",
    ): MonografiSubmission {
        return MonografiSubmission::create([
            "rt_user_id" => $rtUser->id,
            "tahun" => $year,
            "status" => $status,
            "wilayah_label" => $wilayahLabel ?? "RT. {$rtUser->rt_rw}",
            "last_saved_at" => now(),
            "submitted_at" => $status === "submitted" ? now() : null,
        ]);
    }

    private function createUser(string $role = "warga", array $overrides = []): User
    {
        $sequence = self::$userSequence++;

        return User::create(array_merge([
            "nama_lengkap" => ucfirst($role) . " {$sequence}",
            "nik" => (string) $sequence,
            "email" => "{$role}{$sequence}@example.com",
            "password" => "password",
            "role" => $role,
            "status_akun" => "aktif",
            "is_verified" => true,
        ], $overrides));
    }

    private function findRowByColumnValue($sheet, string $column, string $needle): ?int
    {
        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            if (trim((string) $sheet->getCell("{$column}{$row}")->getValue()) === $needle) {
                return $row;
            }
        }

        return null;
    }

    private function findRowByColumnPrefix($sheet, string $column, string $prefix): ?int
    {
        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            if (str_starts_with(trim((string) $sheet->getCell("{$column}{$row}")->getValue()), $prefix)) {
                return $row;
            }
        }

        return null;
    }
}
