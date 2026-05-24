<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN
        DB::table('users')->insert([
            'nama_lengkap' => 'Administrator Desa',
            'nik' => '3328000000000001',
            'email' => 'admin@desa-dukuhtengah.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_akun' => 'aktif',
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. RT
        DB::table('users')->insert([
            'nama_lengkap' => 'Ketua RT 01',
            'nik' => '3328000000000002',
            'email' => 'rt01@desa-dukuhtengah.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'rt',
            'status_akun' => 'aktif',
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. WARGA
        DB::table('users')->insert([
            'nama_lengkap' => 'Budi Hartoni',
            'nik' => '3273123456780001',
            'email' => 'budidoreso@sample.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'warga',
            'tempat_lahir' => 'Tegal',
            'tanggal_lahir' => '1988-05-12',
            'jenis_kelamin' => 'laki-laki',
            'status_perkawinan' => 'menikah',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Jl. Raya Utama No. 10',
            'rt_rw' => '01/02',
            'desa' => 'Dukuhtengah',
            'kecamatan' => 'Bojong',
            'no_hp' => '081234567890',
            'status_akun' => 'aktif',
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. LAYANAN (12 jenis)
        $layananList = [
            'Surat Pengantar KTP',
            'Surat Pengantar KK',
            'Surat Keterangan Domisili',
            'Surat Keterangan Kelahiran',
            'Surat Keterangan Kematian',
            'Surat Pengantar Pindah',
            'Surat Keterangan Tidak Mampu',
            'Surat Pengantar SKCK',
            'Surat Keterangan Usaha',
            'Surat Keterangan Harga Tanah',
            'Surat Pengantar Nikah',
            'Surat Keterangan Belum Menikah',
        ];

        foreach ($layananList as $nama) {
            DB::table('layanan')->insert([
                'nama_layanan' => $nama,
                'slug' => Str::slug($nama),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. MONOGRAFI CATEGORIES
        $categories = [
            ['kode' => 'A', 'nama_kategori' => 'Potensi Sumber Daya Manusia', 'icon' => 'profile-2user', 'urutan' => 1],
            ['kode' => 'B', 'nama_kategori' => 'Perumahan', 'icon' => 'city-01', 'urutan' => 2],
            ['kode' => 'C', 'nama_kategori' => 'Listrik PLN', 'icon' => 'electric-home-01', 'urutan' => 3],
            ['kode' => 'D', 'nama_kategori' => 'Pendidikan Formal', 'icon' => 'mortarboard-02', 'urutan' => 4],
            ['kode' => 'E', 'nama_kategori' => 'Pendidikan Agama', 'icon' => 'mortarboard-02', 'urutan' => 5],
            ['kode' => 'F', 'nama_kategori' => 'Angka Kelulusan Pendidikan Formal', 'icon' => 'mortarboard-02', 'urutan' => 6],
            ['kode' => 'G', 'nama_kategori' => 'Angka Kelulusan Pendidikan Agama', 'icon' => 'mortarboard-02', 'urutan' => 7],
            ['kode' => 'H', 'nama_kategori' => 'Mata Pencaharian Pokok', 'icon' => 'briefcase-06', 'urutan' => 8],
            ['kode' => 'I', 'nama_kategori' => 'Cacat Mental dan Fisik', 'icon' => 'healtcare', 'urutan' => 9],
            ['kode' => 'J', 'nama_kategori' => 'Kesejahteraan Sosial', 'icon' => 'coins-dollar', 'urutan' => 10],
            ['kode' => 'K', 'nama_kategori' => 'Usaha Jasa dan Perdagangan', 'icon' => 'service', 'urutan' => 11],
        ];

        foreach ($categories as $cat) {
            DB::table('monografi_categories')->insert($cat);
        }
    }
}
