# wa-service

Service Express untuk menerima webhook WhatsApp Cloud API, meneruskan pesan ke n8n, lalu mengirim balasan kembali ke Meta Graph API.

## Dependency Eksternal

- Meta WhatsApp Cloud API
- Workflow n8n `n8n/whatsapp-cek-status-surat.json`
- Endpoint Laravel `GET /api/cek-surat/{no_registrasi}`

## Environment

Salin `.env.example` menjadi `.env`, lalu isi:

- `VERIFY_TOKEN`
- `WHATSAPP_TOKEN`
- `PHONE_NUMBER_ID`
- `N8N_WEBHOOK_URL`
- `PORT`

Contoh:

```bash
cp .env.example .env
```

## Start Produksi

Install dependency lalu jalankan dengan PM2:

```bash
npm ci
pm2 start ecosystem.config.cjs
pm2 save
```

Webhook Meta harus diarahkan ke:

```text
https://domain-wa-service-anda/webhook
```

Gunakan nilai `VERIFY_TOKEN` yang sama saat verifikasi webhook di dashboard Meta.

## Health dan Smoke Check

Endpoint health:

```text
GET /health
```

Smoke check lokal:

```bash
npm test
npm run smoke -- --url http://127.0.0.1:3001
```

`npm test` memverifikasi route health, verifikasi webhook, dan alur forward pesan ke n8n plus balasan ke Meta Graph API.

## n8n dan Callback Laravel

Pastikan workflow n8n memakai endpoint Laravel final:

```text
https://domain-laravel-anda/api/cek-surat/{no_registrasi}
```

Panduan import workflow tetap ada di [n8n/README.md](./n8n/README.md).
