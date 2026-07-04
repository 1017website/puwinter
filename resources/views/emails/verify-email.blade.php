<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Verifikasi Email Akun Puwinter</title>
</head>
<body style="margin:0; padding:0; background:#F3F7FF; font-family:Arial, Helvetica, sans-serif; color:#0F172A;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        Verifikasi email kamu untuk mengaktifkan akun Puwinter.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#F3F7FF; margin:0; padding:0;">
        <tr>
            <td align="center" style="padding:34px 14px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; width:100%;">
                    <tr>
                        <td align="center" style="padding:0 0 18px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                                <tr>
                                    <td bgcolor="#0F172A" style="background:#0F172A; border:1px solid #1E3A8A; border-radius:999px; padding:10px 18px; text-align:center; box-shadow:0 10px 24px rgba(15, 23, 42, 0.10);">
                                        <img src="{{ $logoUrl ?? asset('images/logo.png') }}" width="150" alt="Puwinter" style="display:block; width:150px; max-width:150px; height:auto; border:0; outline:none; text-decoration:none;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#FFFFFF; border:1px solid #DDE8FF; border-radius:22px; overflow:hidden; box-shadow:0 18px 45px rgba(15, 23, 42, 0.08);">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="background:#2563EB; padding:24px 28px; text-align:center;">
                                        <div style="font-size:13px; letter-spacing:.14em; color:#DBEAFE; font-weight:700; text-transform:uppercase;">
                                            Verifikasi Akun
                                        </div>
                                        <div style="font-size:25px; line-height:1.3; color:#FFFFFF; font-weight:800; margin-top:8px;">
                                            Aktifkan akun Puwinter kamu
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:32px 30px 26px;">
                                        <p style="font-size:16px; line-height:1.7; color:#0F172A; margin:0 0 10px; font-weight:700;">
                                            Halo {{ $user->name ?? 'Siswa Puwinter' }},
                                        </p>
                                        <p style="font-size:15px; line-height:1.75; color:#475569; margin:0 0 22px;">
                                            Terima kasih sudah mendaftar di <strong>Puwinter</strong>. Silakan klik tombol di bawah ini untuk memverifikasi email dan mengaktifkan akun kamu.
                                        </p>

                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:28px auto;">
                                            <tr>
                                                <td align="center" bgcolor="#2563EB" style="border-radius:12px;">
                                                    <a href="{{ $verifyUrl }}" target="_blank" style="display:inline-block; padding:15px 28px; font-size:15px; line-height:1; color:#FFFFFF; text-decoration:none; font-weight:800; border-radius:12px; background:#2563EB;">
                                                        Verifikasi Email Saya
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:14px; padding:14px 16px; margin:0 0 20px;">
                                            <p style="font-size:13px; line-height:1.65; color:#1E40AF; margin:0;">
                                                Link verifikasi ini berlaku sampai
                                                <strong>{{ optional($expiresAt)->timezone(config('app.timezone', 'Asia/Jakarta'))->format('d M Y H:i') }} WIB</strong>.
                                            </p>
                                        </div>

                                        <p style="font-size:13px; line-height:1.7; color:#64748B; margin:0 0 8px;">
                                            Jika tombol di atas tidak bisa dibuka, salin dan tempel link berikut ke browser:
                                        </p>
                                        <p style="font-size:12px; line-height:1.7; color:#2563EB; word-break:break-all; margin:0 0 22px;">
                                            {{ $verifyUrl }}
                                        </p>

                                        <p style="font-size:13px; line-height:1.7; color:#94A3B8; margin:0;">
                                            Jika kamu tidak merasa membuat akun di Puwinter, abaikan email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:18px 10px 0;">
                            <p style="font-size:12px; line-height:1.6; color:#94A3B8; margin:0;">
                                Email otomatis dari Puwinter. Mohon tidak membalas email ini.<br>
                                @isset($emailLogId)
                                    Log ID: {{ $emailLogId }}
                                @endisset
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
