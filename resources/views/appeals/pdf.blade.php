<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurso de Multa - {{ $ticket->plate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            margin: 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 2cm;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5cm;
        }
        .addressee {
            margin-bottom: 1.5cm;
        }
        .sender {
            margin-bottom: 1.5cm;
        }
        .subject {
            font-weight: bold;
            margin-bottom: 1cm;
        }
        .content {
            text-align: justify;
            margin-bottom: 1.5cm;
        }
        .signature {
            margin-top: 2cm;
        }
        .footer {
            text-align: center;
            font-size: 10pt;
            margin-top: 2cm;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Recurso Administrativo de Autuação de Trânsito</h1>
    </div>

    <div class="addressee">
        <p>À AUTORIDADE DE TRÂNSITO COMPETENTE</p>
        <p>Órgão Autuador: {{ strtoupper($ticket->citation_number ? substr($ticket->citation_number, 0, 3) : 'DETRAN') }}</p>
    </div>

    <div class="sender">
        <p><strong>RECORRENTE:</strong> {{ $user->name }}</p>
        <p><strong>CPF/CNPJ:</strong> (Documento do recorrente)</p>
        <p><strong>ENDEREÇO:</strong> (Endereço do recorrente)</p>
    </div>

    <div class="subject">
        <p>ASSUNTO: Recurso contra autuação de trânsito - Auto de Infração nº {{ $ticket->citation_number ?: '[NÚMERO DA AUTUAÇÃO]' }}</p>
    </div>

    <div class="content">
        {!! nl2br(e($appeal->generated_text)) !!}
    </div>

    <div class="signature">
        <p>{{ $ticket->location }}, {{ now()->format('d') }} de {{ strftime('%B', strtotime(now())) }} de {{ now()->format('Y') }}</p>
        <p style="margin-top: 2cm;">___________________________________________</p>
        <p>{{ $user->name }}</p>
        <p>CPF: (CPF do recorrente)</p>
    </div>

    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema AutoRecurso em {{ $generated_at }}</p>
    </div>
</body>
</html>
