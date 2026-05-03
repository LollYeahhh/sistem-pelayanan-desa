<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileCanonicalValuesTest extends TestCase
{
    use RefreshDatabase;

    private static int $sequence = 1000000000000000;

    public function test_warga_profile_preselects_canonical_enum_values(): void
    {
        $user = $this->createUser([
            "role" => "warga",
            "jenis_kelamin" => "laki-laki",
            "status_perkawinan" => "menikah",
            "alamat" => "Jl. Melati 10",
            "rt_rw" => "001/002",
            "desa" => "Sukamaju",
            "kecamatan" => "Cicendo",
        ]);

        $response = $this->actingAs($user)->get(route("warga.profil"));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<option\s+value="laki-laki"[^>]*selected/s', $response->getContent());
        $this->assertMatchesRegularExpression('/<option\s+value="menikah"[^>]*selected/s', $response->getContent());
        $response->assertDontSee("Alamat & Domisili");
        $response->assertDontSee('name="alamat"', false);
        $response->assertDontSee('name="rt_rw"', false);
        $response->assertDontSee('name="desa"', false);
        $response->assertDontSee('name="kecamatan"', false);
    }

    public function test_warga_profile_update_persists_canonical_values(): void
    {
        $user = $this->createUser([
            "role" => "warga",
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route("warga.profil"))
            ->put(route("warga.profil.update"), [
                "nama_lengkap" => "Warga Uji",
                "nik" => $user->nik,
                "tempat_lahir" => "Bandung",
                "tanggal_lahir" => "1990-01-01",
                "jenis_kelamin" => "perempuan",
                "agama" => "Islam",
                "status_perkawinan" => "cerai_hidup",
                "pekerjaan" => "Guru",
                "no_hp" => "08123456789",
            ]);

        $response->assertRedirect(route("warga.profil"));

        $user->refresh();

        $this->assertSame("perempuan", $user->jenis_kelamin);
        $this->assertSame("cerai_hidup", $user->status_perkawinan);
    }

    public function test_warga_profile_update_does_not_overwrite_existing_address_data(): void
    {
        $user = $this->createUser([
            "role" => "warga",
            "alamat" => "Jl. Mawar 1",
            "rt_rw" => "001/002",
            "desa" => "Sukamaju",
            "kecamatan" => "Cicendo",
        ]);

        $this
            ->actingAs($user)
            ->from(route("warga.profil"))
            ->put(route("warga.profil.update"), [
                "nama_lengkap" => "Warga Uji",
                "nik" => $user->nik,
                "tempat_lahir" => "Bandung",
                "tanggal_lahir" => "1990-01-01",
                "jenis_kelamin" => "perempuan",
                "agama" => "Islam",
                "status_perkawinan" => "cerai_hidup",
                "pekerjaan" => "Guru",
                "no_hp" => "08123456789",
            ])
            ->assertRedirect(route("warga.profil"));

        $user->refresh();

        $this->assertSame("Jl. Mawar 1", $user->getRawOriginal("alamat"));
        $this->assertSame("001/002", $user->getRawOriginal("rt_rw"));
        $this->assertSame("Sukamaju", $user->getRawOriginal("desa"));
        $this->assertSame("Cicendo", $user->getRawOriginal("kecamatan"));
    }

    public function test_warga_profile_update_rejects_legacy_labels(): void
    {
        $user = $this->createUser([
            "role" => "warga",
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route("warga.profil"))
            ->put(route("warga.profil.update"), [
                "nama_lengkap" => "Warga Uji",
                "nik" => $user->nik,
                "jenis_kelamin" => "Laki-laki",
                "status_perkawinan" => "Belum Menikah",
            ]);

        $response->assertRedirect(route("warga.profil"));
        $response->assertSessionHasErrors(["jenis_kelamin", "status_perkawinan"]);
    }

    public function test_rt_profile_preselects_canonical_enum_values(): void
    {
        $user = $this->createUser([
            "role" => "rt",
            "jenis_kelamin" => "perempuan",
            "status_perkawinan" => "belum_menikah",
            "alamat" => "Jl. Kenanga 2",
            "rt_rw" => "003/004",
            "desa" => "Sukaluyu",
            "kecamatan" => "Coblong",
        ]);

        $response = $this->actingAs($user)->get(route("rt.profil"));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<option\s+value="perempuan"[^>]*selected/s', $response->getContent());
        $this->assertMatchesRegularExpression('/<option\s+value="belum_menikah"[^>]*selected/s', $response->getContent());
        $response->assertDontSee('name="alamat"', false);
        $response->assertDontSee('name="rt_rw"', false);
        $response->assertDontSee('name="desa"', false);
        $response->assertDontSee('name="kecamatan"', false);
    }

    public function test_rt_profile_update_does_not_overwrite_existing_address_data(): void
    {
        $user = $this->createUser([
            "role" => "rt",
            "alamat" => "Jl. Kenanga 2",
            "rt_rw" => "003/004",
            "desa" => "Sukaluyu",
            "kecamatan" => "Coblong",
        ]);

        $this
            ->actingAs($user)
            ->from(route("rt.profil"))
            ->put(route("rt.profil.update"), [
                "nama_lengkap" => "RT Uji",
                "nik" => $user->nik,
                "tempat_lahir" => "Garut",
                "tanggal_lahir" => "1989-02-02",
                "jenis_kelamin" => "laki-laki",
                "agama" => "Islam",
                "status_perkawinan" => "menikah",
                "pekerjaan" => "Wiraswasta",
                "no_hp" => "08123450000",
            ])
            ->assertRedirect(route("rt.profil"));

        $user->refresh();

        $this->assertSame("Jl. Kenanga 2", $user->getRawOriginal("alamat"));
        $this->assertSame("003/004", $user->getRawOriginal("rt_rw"));
        $this->assertSame("Sukaluyu", $user->getRawOriginal("desa"));
        $this->assertSame("Coblong", $user->getRawOriginal("kecamatan"));
    }

    public function test_admin_update_accepts_canonical_values(): void
    {
        $admin = $this->createUser([
            "role" => "admin",
            "email" => "admin@example.com",
            "nik" => "1000000000000999",
        ]);

        $user = $this->createUser([
            "role" => "warga",
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route("admin.warga.update", $user), [
                "nama_lengkap" => "Warga Admin",
                "nik" => $user->nik,
                "tempat_lahir" => "Garut",
                "tanggal_lahir" => "1992-02-02",
                "jenis_kelamin" => "laki-laki",
                "status_perkawinan" => "menikah",
                "agama" => "Islam",
                "pekerjaan" => "Petani",
            ]);

        $response->assertRedirect(route("admin.warga.show", $user));

        $user->refresh();

        $this->assertSame("laki-laki", $user->jenis_kelamin);
        $this->assertSame("menikah", $user->status_perkawinan);
    }

    private function createUser(array $overrides = []): User
    {
        $sequence = self::$sequence++;

        return User::create(array_merge([
            "nama_lengkap" => "User {$sequence}",
            "nik" => (string) $sequence,
            "email" => "user{$sequence}@example.com",
            "password" => "password",
            "role" => "warga",
        ], $overrides));
    }
}
