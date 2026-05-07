<?php

namespace Tests\Feature;

use App\Models\AduanMasyarakat;
use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananFieldSection;
use App\Models\LayananFormVersion;
use App\Models\PermohonanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageSmokeTest extends TestCase
{
    use RefreshDatabase;

    private static int $sequence = 1400000000000000;

    public function test_public_aduan_attachment_is_stored_and_downloadable_by_admin(): void
    {
        Storage::fake("public");

        $response = $this->post(route("aduan.store"), [
            "nama_depan" => "Siti",
            "nama_belakang" => "Aminah",
            "email" => "siti@example.com",
            "isi_aduan" => "Lampu jalan mati sejak kemarin.",
            "lampiran" => UploadedFile::fake()->create("aduan.pdf", 128, "application/pdf"),
        ]);

        $response->assertRedirect(route("home") . "#aduan");

        $aduan = AduanMasyarakat::query()->firstOrFail();

        $this->assertNotNull($aduan->lampiran_path);
        Storage::disk("public")->assertExists($aduan->lampiran_path);

        $admin = $this->createUser("admin");
        $storedFilename = basename($aduan->lampiran_path);

        $this->actingAs($admin)
            ->get(route("admin.aduan.lampiran", $aduan))
            ->assertOk()
            ->assertHeader("content-disposition", "attachment; filename={$storedFilename}");
    }

    public function test_public_permohonan_attachment_can_be_previewed_and_downloaded_by_admin(): void
    {
        Storage::fake("public");

        $admin = $this->createUser("admin");
        $warga = $this->createUser("warga");
        [$layanan, $field] = $this->createFileUploadLayanan();

        $response = $this->actingAs($warga)->post(route("warga.ajukan.store", $layanan), [
            "field_{$field->id}" => UploadedFile::fake()->image("ktp.jpg"),
        ]);

        $response->assertRedirect(route("warga.riwayat.index"));

        $permohonan = PermohonanSurat::query()
            ->with("lampiran")
            ->latest("id")
            ->firstOrFail();

        $lampiran = $permohonan->lampiran->first();

        $this->assertNotNull($lampiran);
        Storage::disk("public")->assertExists($lampiran->file_path);

        $this->actingAs($admin)
            ->get(route("admin.pengajuan.lampiran.preview", [$permohonan, $field]))
            ->assertOk()
            ->assertHeader("content-disposition", 'inline; filename="' . basename($lampiran->file_path) . '"');

        $this->actingAs($admin)
            ->get(route("admin.pengajuan.lampiran.download", [$permohonan, $field]))
            ->assertOk()
            ->assertHeader("content-disposition", 'attachment; filename=' . basename($lampiran->file_path));
    }

    private function createUser(string $role): User
    {
        $sequence = self::$sequence++;

        return User::create([
            "nama_lengkap" => ucfirst($role) . " {$sequence}",
            "nik" => (string) $sequence,
            "email" => "{$role}{$sequence}@example.com",
            "password" => "password",
            "role" => $role,
            "status_akun" => "aktif",
            "is_verified" => true,
        ]);
    }

    private function createFileUploadLayanan(): array
    {
        $layanan = Layanan::create([
            "nama_layanan" => "Surat Keterangan Usaha",
            "slug" => "surat-keterangan-usaha",
            "is_active" => true,
        ]);

        $version = LayananFormVersion::create([
            "layanan_id" => $layanan->id,
            "version_number" => 1,
            "status" => "published",
            "published_at" => now(),
        ]);

        $section = LayananFieldSection::create([
            "layanan_id" => $layanan->id,
            "form_version_id" => $version->id,
            "nama_section" => "Dokumen Pendukung",
            "urutan" => 1,
        ]);

        $field = LayananField::create([
            "section_id" => $section->id,
            "label" => "Upload KTP",
            "tipe_input" => "file",
            "placeholder" => null,
            "is_required" => true,
            "urutan" => 1,
            "opsi_select" => null,
        ]);

        return [$layanan, $field];
    }
}
