# Sistem Pelayanan Desa

Aplikasi Laravel untuk layanan surat desa, monografi RT, arsip dokumen, notifikasi admin, dan aduan masyarakat.

## Stack

- PHP 8.2+
- Laravel 12
- MySQL atau PostgreSQL
- Node.js 22+ untuk build Vite

## Setup Lokal

1. Install dependency backend dan frontend.
2. Salin `.env.example` menjadi `.env`.
3. Isi koneksi database dan `APP_URL`.
4. Generate key aplikasi.
5. Jalankan migrasi.
6. Buat symbolic link storage publik.

Contoh perintah:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
```

## Baseline Produksi

Set nilai berikut saat deploy:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-aplikasi-anda`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

Repo ini sudah menyediakan migrasi untuk tabel runtime berikut:

- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `notifications`

## Checklist Deploy

Jalankan langkah ini untuk rilis produksi:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
npm ci
npm run build
php artisan config:cache
php artisan route:cache
```

Verifikasi hasilnya:

```bash
php artisan about
```

Kondisi yang harus terlihat:

- environment `production`
- debug `DISABLED`
- `public/storage` berstatus `LINKED`

## Release Gate

Gunakan gate berikut sebelum deploy:

```bash
composer run release:check
```

Perintah ini menjalankan:

- `php artisan test`
- `npm run build`

## Storage dan Upload

- Lampiran warga, RT, dan aduan disimpan pada disk `public` dan harus bisa diakses lewat `/storage`.
- Dokumen hasil admin disimpan pada disk `local` atau `storage/app/private` dan diunduh lewat controller Laravel.

Smoke test upload/download sudah tercakup di test suite fitur.

## Runtime Tambahan

- Queue worker tidak wajib untuk rilis awal selama Anda tidak mengaktifkan job queue terpisah.
- Jika `QUEUE_CONNECTION=database` dipakai untuk proses async di masa depan, jalankan worker terpisah:

```bash
php artisan queue:work --tries=1 --timeout=0
```

## Integrasi WhatsApp

`wa-service` adalah komponen wajib terpisah untuk webhook WhatsApp dan alur n8n. Lihat panduan di [../wa-service/README.md](../wa-service/README.md).
