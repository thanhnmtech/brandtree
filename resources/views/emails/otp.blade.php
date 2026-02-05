<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $type === 'verification' ? 'Email Verification' : 'Password Reset' }} - BrandTree</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, Arial, sans-serif; line-height: 1.5; background-color: #f6f5fa; color: #191c1d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f6f5fa;">
        <tr>
            <td style="padding: 48px 20px;">
                <table role="presentation" width="420" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width: 420px; width: 100%; margin: 0 auto; background-color: #ffffff; border-radius: 16px;">
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 32px;">
                            <!-- Logo -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 28px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <img src="https://ai.caythuonghieu.com/assets/img/ccf8fd54d8a10b4ab49d514622f1efb57099e1a4.svg" alt="BrandTree" width="40" height="40" style="display: block; border: 0;">
                                                </td>
                                                <td style="vertical-align: middle; padding-left: 10px;">
                                                    <span style="font-size: 22px; font-weight: 600; color: #191c1d;">BrandTree</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Greeting -->
                            <h1 style="margin: 0 0 10px 0; font-size: 22px; font-weight: 600; color: #191c1d; text-align: center;">
                                {{ $type === 'verification' ? 'Verify your email' : 'Reset your password' }}
                            </h1>

                            <!-- Message -->
                            <p style="margin: 0 0 28px 0; color: #5c5f60; font-size: 14px; text-align: center; line-height: 1.6;">
                                @if($type === 'verification')
                                    Enter the following verification code to complete your sign up.
                                @else
                                    Enter the following code to reset your password.
                                @endif
                            </p>

                            <!-- OTP Container -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="background-color: #eff2f1; border-radius: 12px; padding: 24px;">
                                        <p style="margin: 0; font-size: 32px; font-weight: 700; color: #191c1d; letter-spacing: 8px; font-family: Monaco, 'Courier New', monospace;">{{ $otp }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry Text -->
                            <p style="margin: 20px 0; color: #5c5f60; font-size: 13px; text-align: center;">
                                If you didn't request this code, you can safely ignore this email. This code will expire in <strong style="color: #191c1d;">10 minutes</strong>.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #98a2b3; font-size: 12px;">&copy; {{ date('Y') }} BrandTree. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
