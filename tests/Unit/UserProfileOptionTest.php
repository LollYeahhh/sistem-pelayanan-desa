<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserProfileOptionTest extends TestCase
{
    public function test_it_normalizes_legacy_labels_to_canonical_values(): void
    {
        $user = new User();
        $user->jenis_kelamin = "Laki-laki";
        $user->status_perkawinan = "Belum Menikah";

        $this->assertSame("laki-laki", $user->jenis_kelamin);
        $this->assertSame("belum_menikah", $user->status_perkawinan);
    }

    public function test_it_resolves_human_readable_labels_from_canonical_values(): void
    {
        $this->assertSame("Perempuan", User::getJenisKelaminLabel("perempuan"));
        $this->assertSame("Cerai Mati", User::getStatusPerkawinanLabel("cerai_mati"));
    }
}
