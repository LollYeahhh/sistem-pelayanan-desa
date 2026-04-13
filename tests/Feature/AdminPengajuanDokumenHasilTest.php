<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\PermohonanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPengajuanDokumenHasilTest extends TestCase
{
    use RefreshDatabase;

    private static int $userSequence = 1100000000000000;

    private static int $layananSequence = 1;

    public function test_admin_can_approve_permohonan_from_menunggu_to_disetujui(): void
    {
        $admin = $this->createUser("admin");
        $permohonan = $this->createPermohonan(status: "menunggu");

        $response = $this
            ->actingAs($admin)
            ->patch(route("admin.pengajuan.approve", $permohonan));

        $response->assertRedirect(route("admin.pengajuan.show", $permohonan));

        $permohonan->refresh();

        $this->assertSame("disetujui", $permohonan->status);
        $this->assertSame($admin->id, $permohonan->reviewed_by);
        $this->assertNotNull($permohonan->reviewed_at);
    }

    public function test_admin_cannot_complete_permohonan_before_it_is_disetujui(): void
    {
        Storage::fake("local");

        $admin = $this->createUser("admin");
        $permohonan = $this->createPermohonan(status: "menunggu");

        $response = $this
            ->actingAs($admin)
            ->patch(route("admin.pengajuan.complete", $permohonan), [
                "file_dokumen" => UploadedFile::fake()->create("hasil.pdf", 100, "application/pdf"),
            ]);

        $response->assertRedirect(route("admin.pengajuan.show", $permohonan));
        $response->assertSessionHas("error");

        $permohonan->refresh();

        $this->assertSame("menunggu", $permohonan->status);
        $this->assertNull($permohonan->file_dokumen_path);
    }

    public function test_admin_can_upload_valid_document_and_mark_permohonan_as_selesai(): void
    {
        Storage::fake("local");

        $admin = $this->createUser("admin");
        $warga = $this->createUser("warga");
        $permohonan = $this->createPermohonan($warga, "disetujui");

        $response = $this
            ->actingAs($admin)
            ->patch(route("admin.pengajuan.complete", $permohonan), [
                "file_dokumen" => UploadedFile::fake()->create("surat-keterangan.pdf", 256, "application/pdf"),
                "catatan_admin" => "Silakan gunakan dokumen ini untuk kebutuhan administrasi.",
            ]);

        $response->assertRedirect(route("admin.pengajuan.show", $permohonan));

        $permohonan->refresh();

        $this->assertSame("selesai", $permohonan->status);
        $this->assertSame("surat-keterangan.pdf", $permohonan->file_dokumen_nama);
        $this->assertSame("application/pdf", $permohonan->file_dokumen_mime);
        $this->assertSame("Silakan gunakan dokumen ini untuk kebutuhan administrasi.", $permohonan->catatan_admin);
        $this->assertNotNull($permohonan->dokumen_selesai_at);
        Storage::disk("local")->assertExists($permohonan->file_dokumen_path);

        $notification = $warga->fresh()->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame("status_selesai", $notification->data["kind"] ?? null);
        $this->assertSame(route("warga.riwayat.show", $permohonan), $notification->data["url"] ?? null);
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        Storage::fake("local");

        $admin = $this->createUser("admin");
        $permohonan = $this->createPermohonan(status: "disetujui");

        $response = $this
            ->actingAs($admin)
            ->from(route("admin.pengajuan.show", $permohonan))
            ->patch(route("admin.pengajuan.complete", $permohonan), [
                "file_dokumen" => UploadedFile::fake()->image("hasil.png"),
            ]);

        $response->assertRedirect(route("admin.pengajuan.show", $permohonan));
        $response->assertSessionHasErrors("file_dokumen");
    }

    public function test_only_owner_or_admin_can_download_final_document_and_missing_file_returns_404(): void
    {
        Storage::fake("local");

        $owner = $this->createUser("warga");
        $admin = $this->createUser("admin");
        $otherUser = $this->createUser("warga");

        $permohonan = $this->createPermohonan($owner, "selesai");
        Storage::disk("local")->put("dokumen-hasil/{$permohonan->id}/hasil.pdf", "dummy");
        $permohonan->update([
            "file_dokumen_path" => "dokumen-hasil/{$permohonan->id}/hasil.pdf",
            "file_dokumen_nama" => "hasil.pdf",
            "file_dokumen_mime" => "application/pdf",
            "dokumen_selesai_at" => now(),
        ]);

        $this->actingAs($owner)
            ->get(route("pengajuan.dokumen.download", $permohonan))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route("pengajuan.dokumen.download", $permohonan))
            ->assertOk();

        $this->actingAs($otherUser)
            ->get(route("pengajuan.dokumen.download", $permohonan))
            ->assertForbidden();

        Storage::disk("local")->delete($permohonan->file_dokumen_path);

        $this->actingAs($owner)
            ->get(route("pengajuan.dokumen.download", $permohonan))
            ->assertNotFound();
    }

    public function test_warga_and_rt_history_pages_show_download_button_only_for_completed_documents(): void
    {
        Storage::fake("local");

        $warga = $this->createUser("warga");
        $rt = $this->createUser("rt");

        $wargaSelesai = $this->createPermohonan($warga, "selesai");
        $wargaSelesai->update([
            "file_dokumen_path" => "dokumen-hasil/{$wargaSelesai->id}/warga.pdf",
            "file_dokumen_nama" => "warga.pdf",
            "file_dokumen_mime" => "application/pdf",
            "catatan_admin" => "Dokumen warga siap digunakan.",
            "dokumen_selesai_at" => now(),
        ]);

        $wargaDisetujui = $this->createPermohonan($warga, "disetujui");

        $rtSelesai = $this->createPermohonan($rt, "selesai");
        $rtSelesai->update([
            "file_dokumen_path" => "dokumen-hasil/{$rtSelesai->id}/rt.pdf",
            "file_dokumen_nama" => "rt.pdf",
            "file_dokumen_mime" => "application/pdf",
            "catatan_admin" => "Dokumen RT siap digunakan.",
            "dokumen_selesai_at" => now(),
        ]);

        $this->actingAs($warga)
            ->get(route("warga.riwayat.show", $wargaSelesai))
            ->assertOk()
            ->assertSee(route("pengajuan.dokumen.download", $wargaSelesai), false)
            ->assertSee("Dokumen warga siap digunakan.");

        $this->actingAs($warga)
            ->get(route("warga.riwayat.show", $wargaDisetujui))
            ->assertOk()
            ->assertDontSee(route("pengajuan.dokumen.download", $wargaDisetujui), false);

        $this->actingAs($rt)
            ->get(route("rt.riwayat.show", $rtSelesai))
            ->assertOk()
            ->assertSee(route("pengajuan.dokumen.download", $rtSelesai), false)
            ->assertSee("Dokumen RT siap digunakan.");
    }

    public function test_admin_arsip_page_includes_completed_permohonan_and_download_action(): void
    {
        $admin = $this->createUser("admin");
        $permohonan = $this->createPermohonan(status: "selesai");
        $permohonan->update([
            "file_dokumen_path" => "dokumen-hasil/{$permohonan->id}/arsip.pdf",
            "file_dokumen_nama" => "arsip.pdf",
            "file_dokumen_mime" => "application/pdf",
            "reviewed_at" => now(),
            "dokumen_selesai_at" => now(),
        ]);

        $this->actingAs($admin)
            ->get(route("admin.arsip.index"))
            ->assertOk()
            ->assertSee("Selesai")
            ->assertSee(route("pengajuan.dokumen.download", $permohonan), false);
    }

    private function createUser(string $role = "warga"): User
    {
        $sequence = self::$userSequence++;

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

    private function createPermohonan(?User $user = null, string $status = "menunggu"): PermohonanSurat
    {
        $user ??= $this->createUser("warga");
        $layanan = $this->createLayanan();

        return PermohonanSurat::create([
            "user_id" => $user->id,
            "layanan_id" => $layanan->id,
            "no_registrasi" => "REG-" . strtoupper(dechex(self::$layananSequence + 1000)),
            "status" => $status,
            "reviewed_at" => in_array($status, ["disetujui", "ditolak", "selesai"], true) ? now() : null,
        ]);
    }

    private function createLayanan(): Layanan
    {
        $sequence = self::$layananSequence++;

        return Layanan::create([
            "nama_layanan" => "Layanan {$sequence}",
            "slug" => "layanan-{$sequence}",
            "is_active" => true,
        ]);
    }
}
