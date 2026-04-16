<?php

namespace Tests\Feature;

use App\Models\MonografiCategory;
use App\Models\MonografiField;
use App\Models\MonografiSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonografiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private static int $userSequence = 1300000000000000;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_rt_can_save_draft_and_continue_to_next_category(): void
    {
        $rt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => "01/01",
        ]);

        [$categoryA, $fieldA] = $this->createCategory("A", "Potensi SDM", 1, [
            ["label" => "Jumlah Penduduk Laki-laki", "tipe_input" => "number"],
        ]);
        [$categoryB] = $this->createCategory("B", "Perumahan", 2, [
            ["label" => "Jumlah Rumah", "tipe_input" => "number"],
        ]);

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 15,
                "action" => "save_and_next",
            ])
            ->assertRedirect(route("rt.monografi.show", ["category" => $categoryB, "rt" => $rt->id]));

        $this->assertDatabaseHas("monografi_data", [
            "field_id" => $fieldA->id,
            "rt_user_id" => $rt->id,
            "tahun" => (int) now("Asia/Jakarta")->format("Y"),
            "value" => "15",
        ]);
        $this->assertDatabaseHas("monografi_submissions", [
            "rt_user_id" => $rt->id,
            "tahun" => (int) now("Asia/Jakarta")->format("Y"),
            "status" => "draft",
            "wilayah_label" => "RT. 01/01",
        ]);
    }

    public function test_submit_requires_all_configured_fields_to_be_filled(): void
    {
        $rt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => null,
        ]);

        [$categoryA, $fieldA] = $this->createCategory("A", "Potensi SDM", 1, [
            ["label" => "Jumlah Penduduk Laki-laki", "tipe_input" => "number"],
        ]);
        [$categoryB] = $this->createCategory("B", "Perumahan", 2, [
            ["label" => "Jumlah Rumah", "tipe_input" => "number"],
        ]);

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 22,
                "action" => "submit",
            ])
            ->assertSessionHasErrors("submit");

        $this->assertDatabaseMissing("monografi_submissions", [
            "rt_user_id" => $rt->id,
            "tahun" => (int) now("Asia/Jakarta")->format("Y"),
            "status" => "submitted",
        ]);
    }

    public function test_submitted_monografi_is_read_only_until_admin_reopens_it(): void
    {
        $rt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT 01",
            "rt_rw" => null,
        ]);
        $admin = $this->createUser("admin");

        [$categoryA, $fieldA] = $this->createCategory("A", "Potensi SDM", 1, [
            ["label" => "Jumlah Penduduk Laki-laki", "tipe_input" => "number"],
        ]);

        $year = (int) now("Asia/Jakarta")->format("Y");

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 33,
                "action" => "submit",
            ])
            ->assertRedirect(route("rt.monografi.show", ["category" => $categoryA, "rt" => $rt->id]));

        $this->assertDatabaseHas("monografi_submissions", [
            "rt_user_id" => $rt->id,
            "tahun" => $year,
            "status" => "submitted",
            "wilayah_label" => "RT 01",
        ]);

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 99,
                "action" => "save",
            ])
            ->assertSessionHasErrors("submit");

        $this->assertDatabaseHas("monografi_data", [
            "field_id" => $fieldA->id,
            "rt_user_id" => $rt->id,
            "tahun" => $year,
            "value" => "33",
        ]);

        $this->actingAs($admin)
            ->get(route("admin.monografi.index", ["tahun" => $year]))
            ->assertOk()
            ->assertSee("RT 01")
            ->assertSee("Sudah Dikirim");

        $this->actingAs($admin)
            ->get(route("admin.monografi.show", ["rtUser" => $rt, "tahun" => $year]))
            ->assertOk()
            ->assertSee("Jumlah Penduduk Laki-laki")
            ->assertSee("33");

        $this->actingAs($admin)
            ->patch(route("admin.monografi.reopen", ["rtUser" => $rt, "tahun" => $year]))
            ->assertRedirect(route("admin.monografi.show", ["rtUser" => $rt, "tahun" => $year]));

        $this->assertDatabaseHas("monografi_submissions", [
            "rt_user_id" => $rt->id,
            "tahun" => $year,
            "status" => "draft",
            "wilayah_label" => "RT 01",
        ]);

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 45,
                "action" => "save",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("monografi_data", [
            "field_id" => $fieldA->id,
            "rt_user_id" => $rt->id,
            "tahun" => $year,
            "value" => "45",
        ]);
    }

    public function test_admin_index_treats_legacy_monografi_data_without_submission_as_draft(): void
    {
        $rt = $this->createUser("rt", [
            "nama_lengkap" => "Ketua RT Legacy",
            "rt_rw" => null,
        ]);
        $admin = $this->createUser("admin");

        [$categoryA, $fieldA] = $this->createCategory("A", "Potensi SDM", 1, [
            ["label" => "Jumlah Penduduk Laki-laki", "tipe_input" => "number"],
        ]);

        $this->actingAs($rt)
            ->put(route("rt.monografi.update", ["category" => $categoryA, "rt" => $rt->id]), [
                "field_{$fieldA->id}" => 17,
                "action" => "save",
            ]);

        MonografiSubmission::query()->delete();

        $this->actingAs($admin)
            ->get(route("admin.monografi.index", ["tahun" => (int) now("Asia/Jakarta")->format("Y")]))
            ->assertOk()
            ->assertSee("Draft")
            ->assertSee("RT 01");
    }

    private function createCategory(string $kode, string $nama, int $urutan, array $fields): array
    {
        $category = MonografiCategory::create([
            "kode" => $kode,
            "nama_kategori" => $nama,
            "urutan" => $urutan,
        ]);

        $createdFields = collect();

        foreach ($fields as $index => $field) {
            $createdFields->push(MonografiField::create([
                "category_id" => $category->id,
                "label" => $field["label"],
                "tipe_input" => $field["tipe_input"] ?? "number",
                "urutan" => $index + 1,
            ]));
        }

        return [$category, ...$createdFields->all()];
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
}
