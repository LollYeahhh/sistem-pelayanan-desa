# n8n Workflow WhatsApp Status Surat

File [`whatsapp-cek-status-surat.json`](/d:/laragon/www/TUGAS-AKHIR/wa-service/n8n/whatsapp-cek-status-surat.json) adalah template workflow n8n untuk alur:

`Webhook -> IF -> Set -> HTTP Request -> Set -> Respond to Webhook`

## Cara pakai

1. Import file workflow JSON ke n8n.
2. Buka node `HTTP Cek Surat`.
3. Ganti URL:

```text
http://GANTI-URL-LARAVEL-ANDA/api/cek-surat/
```

dengan URL Laravel Anda, misalnya:

```text
http://localhost:8000/api/cek-surat/
```

4. Pastikan path node `Webhook` adalah `wa-incoming`.
5. Pastikan `wa-service/.env` berisi:

```text
N8N_WEBHOOK_URL=http://localhost:5678/webhook/wa-incoming
```

## Payload yang dikirim dari wa-service

`wa-service/index.js` mengirim JSON berikut ke n8n:

```json
{
  "sender": "62812xxxxxxx",
  "message": "cek status surat REG-FKSK8JV"
}
```

Di node `Webhook`, data dibaca sebagai:

```js
{{$json.body.sender}}
{{$json.body.message}}
```

## Expression utama

- Cek kata `status`:

```js
{{ ($json.body.message || '').toLowerCase() }}
```

- Ambil `no_registrasi`:

```js
{{ (($json.body.message || '').toUpperCase().match(/REG-[A-Z0-9]+/) || [null])[0] }}
```

- Mapping balasan:

```js
{{ $json.status === 'tidak ditemukan' ? 'Data surat tidak ditemukan.' : 'Status surat Anda: ' + $json.status }}
```

- Bentuk URL API:

```js
{{ 'http://URL-LARAVEL-ANDA/api/cek-surat/' + $json.no_registrasi }}
```
