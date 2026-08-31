<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinição de senha</title>
</head>
<body style="margin: 0; padding: 0; background: #f7f3ea; color: #28251f; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f7f3ea; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; overflow: hidden; background: #ffffff; border: 1px solid #e7dfce; border-radius: 16px;">
                    <tr>
                        <td align="center" style="padding: 32px 32px 18px;">
                            <img src="{{ asset('images/logo.jpeg') }}" width="88" height="88" alt="Portal Invest Bahia" style="display: block; border-radius: 12px; object-fit: contain;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 36px; font-size: 16px; line-height: 1.6;">
                            <h1 style="margin: 0 0 20px; color: #17150f; font-family: Georgia, 'Times New Roman', serif; font-size: 28px; line-height: 1.2; text-align: center;">Redefina sua senha</h1>

                            <p style="margin: 0 0 16px;">Olá, {{ $name }}!</p>
                            <p style="margin: 0 0 24px;">Recebemos uma solicitação para redefinir a senha da sua conta no Portal Invest Bahia.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 4px 0 28px;">
                                        <a href="{{ $url }}" style="display: inline-block; padding: 14px 24px; border-radius: 8px; background: #d3a625; color: #17150f; font-weight: 700; text-decoration: none;">Redefinir minha senha</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px; color: #665f50; font-size: 14px;">Este link é individual, pode ser utilizado apenas uma vez e expira em {{ $expiresInMinutes }} minutos.</p>
                            <p style="margin: 0; color: #665f50; font-size: 14px;">Se você não solicitou a redefinição, ignore este e-mail. Sua senha permanecerá inalterada.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 32px; background: #17150f; color: #f7f3ea; font-size: 13px; text-align: center;">
                            Portal Invest Bahia
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
