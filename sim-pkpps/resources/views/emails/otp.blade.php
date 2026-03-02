{{-- resources/views/emails/otp.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #6FBA9D, #5EA98C); padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                🔐 Reset Password
                            </h1>
                            <p style="margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px;">
                                SIM Monitoring Santri - PKPPS
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 16px; color: #2A4235; font-size: 15px;">
                                Assalamu'alaikum <strong>{{ $nama }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px; color: #555; font-size: 14px; line-height: 1.7;">
                                Kami menerima permintaan reset password untuk akun Super Admin Anda. 
                                Gunakan kode OTP berikut untuk melanjutkan proses:
                            </p>

                            {{-- OTP Code --}}
                            <div style="text-align: center; margin: 28px 0;">
                                <div style="display: inline-block; background: #EBF7F2; border: 2px dashed #6FBA9D; border-radius: 12px; padding: 18px 40px;">
                                    <span style="font-size: 36px; font-weight: 800; letter-spacing: 10px; color: #2A4235; font-family: 'Courier New', monospace;">
                                        {{ $otp }}
                                    </span>
                                </div>
                            </div>

                            <p style="margin: 0 0 8px; color: #e53935; font-size: 13px; font-weight: 600; text-align: center;">
                                ⏰ Kode ini berlaku selama 10 menit.
                            </p>

                            <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0;">

                            <p style="margin: 0; color: #888; font-size: 12px; line-height: 1.7;">
                                ⚠️ Jika Anda tidak meminta reset password, abaikan email ini.
                                Jangan bagikan kode OTP ini kepada siapapun.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background: #f8fdfb; padding: 18px 32px; text-align: center; border-top: 1px solid #eee;">
                            <p style="margin: 0; color: #aaa; font-size: 11px;">
                                &copy; {{ date('Y') }} SIM PKPPS &mdash; Email ini dikirim secara otomatis
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
