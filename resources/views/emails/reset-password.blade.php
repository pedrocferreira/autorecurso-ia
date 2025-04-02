<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recuperação de Senha - AutoRecurso</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">Recuperação de Senha</h1>
        
        <p>Olá {{ $user->name }},</p>
        
        <p>Recebemos uma solicitação para redefinir a senha da sua conta no AutoRecurso.</p>
        
        <p>Para criar uma nova senha, clique no botão abaixo:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('reset-password/'.$token) }}" 
               style="background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Redefinir Senha
            </a>
        </div>
        
        <p>Se você não solicitou a recuperação de senha, por favor ignore este email.</p>
        
        <p>Este link expira em 60 minutos.</p>
        
        <p>Atenciosamente,<br>Equipe AutoRecurso</p>
    </div>
</body>
</html> 