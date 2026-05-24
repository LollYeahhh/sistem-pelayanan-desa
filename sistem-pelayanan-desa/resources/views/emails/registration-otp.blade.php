<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Registrasi Akun</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:24px;overflow:hidden;border:1px solid #dbeafe;">
                    <tr>
                        <td style="background:#0f5fb8;color:#ffffff;padding:24px 28px;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;opacity:0.9;">NO-REPLY BROADCAST</div>
                            <h1 style="margin:10px 0 0;font-size:24px;line-height:1.3;">Kode OTP Registrasi Akun</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Halo {{ $name }},</p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#334155;">
                                Gunakan kode OTP berikut untuk memverifikasi email registrasi akun pada {{ config("app.name") }}.
                            </p>

                            <div style="margin:24px 0;padding:22px;border-radius:20px;background:#e0f2fe;border:1px solid #7dd3fc;text-align:center;">
                                <div style="font-size:12px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#0369a1;margin-bottom:10px;">Kode OTP</div>
                                <div style="font-size:36px;line-height:1;font-weight:800;letter-spacing:0.22em;color:#0f172a;">{{ $code }}</div>
                            </div>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                                Kode ini berlaku selama {{ $expiresInMinutes }} menit. Jangan bagikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan petugas desa.
                            </p>
                            <p style="margin:0 0 18px;font-size:14px;line-height:1.7;color:#475569;">
                                Jika Anda tidak melakukan registrasi, abaikan email ini.
                            </p>

                            <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;text-align:center;">
                                Email ini dikirim otomatis oleh sistem <strong>Desa Dukuhtengah</strong>. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
