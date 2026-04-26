<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\PermohonanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        Carbon::setTestNow("2026-04-13 21:57:00");

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
        $this->assertNotNull($permohonan->deadline);
        $this->assertSame("2026-04-20", $permohonan->deadline?->toDateString());
        $this->assertTrue($permohonan->reviewed_at->equalTo(Carbon::parse("2026-04-13 21:57:00")));
        Storage::disk("local")->assertExists($permohonan->file_dokumen_path);

        $notification = $warga->fresh()->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame("status_selesai", $notification->data["kind"] ?? null);
        $this->assertSame(route("warga.riwayat.show", $permohonan), $notification->data["url"] ?? null);

        Carbon::setTestNow();
    }

    public function test_admin_pengajuan_index_only_shows_active_inbox_statuses_in_latest_order(): void
    {
        Carbon::setTestNow("2026-04-13 08:00:00");
        $admin = $this->createUser("admin");

        $pendingUser = $this->createUser("warga");
        $pending = $this->createPermohonan($pendingUser, "menunggu");

        Carbon::setTestNow("2026-04-13 09:00:00");
        $processingUser = $this->createUser("warga");
        $processing = $this->createPermohonan($processingUser, "diproses");

        Carbon::setTestNow("2026-04-13 10:00:00");
        $approvedUser = $this->createUser("warga");
        $approved = $this->createPermohonan($approvedUser, "disetujui");

        Carbon::setTestNow("2026-04-13 11:00:00");
        $completedUser = $this->createUser("warga");
        $completed = $this->createPermohonan($completedUser, "selesai");

        Carbon::setTestNow("2026-04-13 12:00:00");
        $rejectedUser = $this->createUser("warga");
        $rejected = $this->createPermohonan($rejectedUser, "ditolak");

        $response = $this->actingAs($admin)->get(route("admin.pengajuan.index"));

        $response->assertOk()
            ->assertSee($pendingUser->nama_lengkap)
            ->assertSee($processingUser->nama_lengkap)
            ->assertSee($approvedUser->nama_lengkap)
            ->assertDontSee($completedUser->nama_lengkap)
            ->assertDontSee($rejectedUser->nama_lengkap)
            ->assertSeeInOrder([
                $approvedUser->nama_lengkap,
                $processingUser->nama_lengkap,
                $pendingUser->nama_lengkap,
            ]);

        $this->assertSame("menunggu", $pending->status);
        $this->assertSame("diproses", $processing->status);
        $this->assertSame("disetujui", $approved->status);
        $this->assertSame("selesai", $completed->status);
        $this->assertSame("ditolak", $rejected->status);

        Carbon::setTestNow();
    }

    public function test_admin_pengajuan_search_only_filters_within_active_inbox(): void
    {
        $admin = $this->createUser("admin");

        $activeUser = $this->createUser("warga");
        $activeUser->update([
            "nama_lengkap" => "Budi Inbox Aktif",
        ]);
        $this->createPermohonan($activeUser, "menunggu");

        $completedUser = $this->createUser("warga");
        $completedUser->update([
            "nama_lengkap" => "Budi Inbox Selesai",
        ]);
        $this->createPermohonan($completedUser, "selesai");

        $response = $this->actingAs($admin)->get(route("admin.pengajuan.index", [
            "search" => "Budi Inbox",
        ]));

        $response->assertOk()
            ->assertSee($activeUser->nama_lengkap)
            ->assertDontSee($completedUser->nama_lengkap);
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
        Carbon::setTestNow("2026-04-13 21:57:00");

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

        $wargaSelesai->refresh();
        $rtSelesai->refresh();
        $wargaMasaBerlaku = $wargaSelesai->masa_berlaku_surat?->translatedFormat("d M Y");
        $rtMasaBerlaku = $rtSelesai->masa_berlaku_surat?->translatedFormat("d M Y");

        $this->actingAs($warga)
            ->get(route("warga.riwayat.show", $wargaSelesai))
            ->assertOk()
            ->assertSee(route("pengajuan.dokumen.download", $wargaSelesai), false)
            ->assertSee("Dokumen warga siap digunakan.")
            ->assertSee("Masa Berlaku Surat")
            ->assertSee($wargaMasaBerlaku)
            ->assertSeeInOrder([
                "Ditinjau Oleh",
                "Masa Berlaku Surat",
                "Dokumen Hasil",
            ]);

        $this->actingAs($warga)
            ->get(route("warga.riwayat.show", $wargaDisetujui))
            ->assertOk()
            ->assertDontSee(route("pengajuan.dokumen.download", $wargaDisetujui), false)
            ->assertDontSee("Masa Berlaku Surat");

        $this->actingAs($rt)
            ->get(route("rt.riwayat.show", $rtSelesai))
            ->assertOk()
            ->assertSee(route("pengajuan.dokumen.download", $rtSelesai), false)
            ->assertSee("Dokumen RT siap digunakan.")
            ->assertSee("Masa Berlaku Surat")
            ->assertSee($rtMasaBerlaku)
            ->assertSeeInOrder([
                "Ditinjau Oleh",
                "Masa Berlaku Surat",
                "Dokumen Hasil",
            ]);

        Carbon::setTestNow();
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

    public function test_admin_dashboard_counts_arsip_using_same_definition_as_arsip_page(): void
    {
        $admin = $this->createUser("admin");

        $completedUser = $this->createUser("warga");
        $rejectedUser = $this->createUser("warga");
        $approvedUser = $this->createUser("warga");
        $pendingUser = $this->createUser("warga");
        $processingUser = $this->createUser("warga");
        $missingReviewedAtUser = $this->createUser("warga");

        $completed = $this->createPermohonan($completedUser, "selesai");
        $rejected = $this->createPermohonan($rejectedUser, "ditolak");
        $approved = $this->createPermohonan($approvedUser, "disetujui");
        $pending = $this->createPermohonan($pendingUser, "menunggu");
        $processing = $this->createPermohonan($processingUser, "diproses");
        $missingReviewedAt = $this->createPermohonan($missingReviewedAtUser, "selesai");

        $missingReviewedAt->update([
            "reviewed_at" => null,
        ]);

        $response = $this->actingAs($admin)->get(route("admin.dashboard"));

        $response->assertOk()->assertViewHas("stats", function (array $stats) {
            return ($stats["arsip_dokumen"] ?? null) === 3;
        });

        $arsipResponse = $this->actingAs($admin)->get(route("admin.arsip.index"));

        $arsipResponse->assertOk()
            ->assertSee($completedUser->nama_lengkap)
            ->assertSee($rejectedUser->nama_lengkap)
            ->assertSee($approvedUser->nama_lengkap)
            ->assertDontSee($pendingUser->nama_lengkap)
            ->assertDontSee($processingUser->nama_lengkap)
            ->assertDontSee($missingReviewedAtUser->nama_lengkap);
    }

    public function test_admin_can_export_arsip_excel_for_selected_months(): void
    {
        $admin = $this->createUser("admin");

        $includedCompleted = $this->createPermohonan(status: "selesai");
        $includedCompleted->update([
            "reviewed_at" => now("Asia/Jakarta")->setDate(2026, 4, 10)->setTime(9, 0),
            "file_dokumen_path" => "dokumen-hasil/{$includedCompleted->id}/maret.pdf",
            "file_dokumen_nama" => "maret.pdf",
            "file_dokumen_mime" => "application/pdf",
            "catatan_admin" => "Dokumen Maret siap.",
        ]);

        $includedRejected = $this->createPermohonan(status: "ditolak");
        $includedRejected->update([
            "reviewed_at" => now("Asia/Jakarta")->setDate(2026, 3, 8)->setTime(11, 30),
            "alasan_penolakan" => "Data belum lengkap",
        ]);

        $excludedMonth = $this->createPermohonan(status: "selesai");
        $excludedMonth->update([
            "reviewed_at" => now("Asia/Jakarta")->setDate(2026, 5, 3)->setTime(8, 15),
            "file_dokumen_path" => "dokumen-hasil/{$excludedMonth->id}/mei.pdf",
            "file_dokumen_nama" => "mei.pdf",
            "file_dokumen_mime" => "application/pdf",
        ]);

        $response = $this->actingAs($admin)->get(route("admin.arsip.export", [
            "tahun" => 2026,
            "bulan" => [3, 4],
        ]));

        $response->assertOk()->assertDownload("arsip-dokumen-2026-03_04.xlsx");

        $spreadsheet = IOFactory::load($response->baseResponse->getFile()->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame("Arsip Dokumen", $sheet->getTitle());
        $this->assertSame("No Registrasi", $sheet->getCell("B1")->getValue());
        $this->assertSame($includedCompleted->no_registrasi, $sheet->getCell("B2")->getValue());
        $this->assertSame("Selesai", $sheet->getCell("F2")->getValue());
        $this->assertSame("Ya", $sheet->getCell("J2")->getValue());
        $this->assertSame("maret.pdf", $sheet->getCell("K2")->getValue());
        $this->assertSame("application/pdf", $sheet->getCell("L2")->getValue());
        $this->assertSame("dokumen-hasil/{$includedCompleted->id}/maret.pdf", $sheet->getCell("M2")->getValue());

        $this->assertSame($includedRejected->no_registrasi, $sheet->getCell("B3")->getValue());
        $this->assertSame("Ditolak", $sheet->getCell("F3")->getValue());
        $this->assertSame("Data belum lengkap", $sheet->getCell("I3")->getValue());
        $this->assertSame("Tidak", $sheet->getCell("J3")->getValue());

        $rows = [];
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $rows[] = (string) $sheet->getCell("B{$row}")->getValue();
        }

        $this->assertContains($includedCompleted->no_registrasi, $rows);
        $this->assertContains($includedRejected->no_registrasi, $rows);
        $this->assertNotContains($excludedMonth->no_registrasi, $rows);
    }

    public function test_admin_arsip_export_defaults_to_current_month_and_skips_records_without_reviewed_at(): void
    {
        $admin = $this->createUser("admin");

        $currentMonthRecord = $this->createPermohonan(status: "selesai");
        $currentMonthRecord->update([
            "reviewed_at" => now("Asia/Jakarta"),
            "file_dokumen_path" => "dokumen-hasil/{$currentMonthRecord->id}/aktif.pdf",
            "file_dokumen_nama" => "aktif.pdf",
            "file_dokumen_mime" => "application/pdf",
        ]);

        $previousMonthRecord = $this->createPermohonan(status: "selesai");
        $previousMonthRecord->update([
            "reviewed_at" => now("Asia/Jakarta")->copy()->subMonth(),
        ]);

        $missingReviewedAt = $this->createPermohonan(status: "selesai");
        $missingReviewedAt->update([
            "reviewed_at" => null,
        ]);

        $response = $this->actingAs($admin)->get(route("admin.arsip.export", [
            "tahun" => (int) now("Asia/Jakarta")->format("Y"),
        ]));

        $currentMonthToken = now("Asia/Jakarta")->format("m");
        $response->assertOk()->assertDownload("arsip-dokumen-" . now("Asia/Jakarta")->format("Y") . "-{$currentMonthToken}.xlsx");

        $spreadsheet = IOFactory::load($response->baseResponse->getFile()->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $rows = [];
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $rows[] = (string) $sheet->getCell("B{$row}")->getValue();
        }

        $this->assertContains($currentMonthRecord->no_registrasi, $rows);
        $this->assertNotContains($previousMonthRecord->no_registrasi, $rows);
        $this->assertNotContains($missingReviewedAt->no_registrasi, $rows);
    }

    public function test_admin_arsip_export_can_filter_by_status(): void
    {
        $admin = $this->createUser("admin");

        $accepted = $this->createPermohonan(status: "disetujui");
        $accepted->update([
            "reviewed_at" => now("Asia/Jakarta")->setDate(2026, 2, 2)->setTime(10, 0),
        ]);

        $rejected = $this->createPermohonan(status: "ditolak");
        $rejected->update([
            "reviewed_at" => now("Asia/Jakarta")->setDate(2026, 2, 5)->setTime(15, 0),
            "alasan_penolakan" => "Ditolak",
        ]);

        $response = $this->actingAs($admin)->get(route("admin.arsip.export", [
            "tahun" => 2026,
            "bulan" => [2],
            "status" => "ditolak",
        ]));

        $response->assertOk()->assertDownload("arsip-dokumen-2026-02.xlsx");

        $spreadsheet = IOFactory::load($response->baseResponse->getFile()->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $rows = [];
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $rows[] = (string) $sheet->getCell("B{$row}")->getValue();
        }

        $this->assertContains($rejected->no_registrasi, $rows);
        $this->assertNotContains($accepted->no_registrasi, $rows);
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
