<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recurso Gerado - AutoRecurso</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">Recurso Gerado com Sucesso!</h1>
        
        <p>Olá {{ $appeal->user->name }},</p>
        
        <p>Seu recurso foi gerado com sucesso! Abaixo estão os detalhes:</p>
        
        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Número do Recurso:</strong> #{{ $appeal->id }}</p>
            <p><strong>Data de Geração:</strong> {{ $appeal->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($appeal->status) }}</p>
        </div>
        
        <p>Você pode acessar seu recurso através do botão abaixo:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('recursos/'.$appeal->id) }}" 
               style="background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Ver Recurso
            </a>
        </div>
        
        <p>Lembre-se de acompanhar o status do seu recurso através da plataforma.</p>
        
        <p>Atenciosamente,<br>Equipe AutoRecurso</p>
    </div>
</body>
</html> 