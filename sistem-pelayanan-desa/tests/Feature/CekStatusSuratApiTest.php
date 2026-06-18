<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\PermohonanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CekStatusSuratApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_format_guide_when_registration_number_is_incomplete(): void
    {
        $response = $this->getJson('/api/cek-surat/FORMAT-TIDAK-LENGKAP');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'format_tidak_lengkap',
            ])
            ->assertJsonPath('reply', fn (string $reply) => str_contains($reply, 'Cek status surat REG-ABC12345'));
    }

    public function test_it_returns_not_found_for_valid_unknown_registration_number(): void
    {
        $response = $this->getJson('/api/cek-surat/REG-ABC12345');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'tidak ditemukan',
            ]);
    }

    public function test_it_returns_submission_status_for_existing_registration_number(): void
    {
        $user = User::factory()->create([
            'nama_lengkap' => 'Budi Santoso',
        ]);

        $layanan = Layanan::create([
            'nama_layanan' => 'Surat Keterangan Domisili',
            'slug' => 'surat-keterangan-domisili',
            'is_active' => true,
        ]);

        PermohonanSurat::create([
            'user_id' => $user->id,
            'layanan_id' => $layanan->id,
            'no_registrasi' => 'REG-ABC12345',
            'status' => 'diproses',
        ]);

        $response = $this->getJson('/api/cek-surat/reg-abc12345');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'diproses',
                'status_label' => 'Sedang diproses',
                'nama' => 'Budi Santoso',
                'jenis_surat' => 'Surat Keterangan Domisili',
            ]);
    }
}
