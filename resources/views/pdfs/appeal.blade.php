<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recurso Administrativo - Multa #{{ $ticket->id }}</title>
    <style>
        @page {
            margin: 3cm 2.5cm 3cm 2.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            font-size: 12pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 0;
        }
        .header h2 {
            font-size: 14pt;
            font-weight: normal;
            margin-top: 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
        }
        .signature-line {
            width: 60%;
            margin: 20px auto;
            border: none;
            border-bottom: 1px solid #000;
            padding-top: 10px;
        }
        .page-number {
            position: fixed;
            bottom: -2cm;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .legal-indent {
            text-indent: 4cm;
        }
        p {
            margin-bottom: 10px;
            text-align: justify;
        }
        .header-info {
            margin-top: 20px;
            margin-bottom: 40px;
            line-height: 1.4;
        }
        .process-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .process-info p {
            margin: 5px 0;
            color: #495057;
        }
        .process-info strong {
            color: #212529;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
        .fecha {
            margin-top: 40px;
            margin-bottom: 5px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="page-number">Página <span class="pagenum"></span></div>
    
    <div class="header-info">
        @if($ticket->process_number)
            <div class="process-info">
                <p><strong>RECURSO ADMINISTRATIVO Nº {{ $ticket->process_number }}</p>
            </div>
        @endif
    </div>

    <div class="content">
        @php
            // Converter quebras de linha em <br>
            $processedText = nl2br(e($text));
            
            // Substituir os underscores por uma linha de assinatura estilizada
            $processedText = str_replace('___________________', '<div class="signature-line"></div>', $processedText);
            
            // Se houver número do processo, garantir que ele apareça no início do texto
            if ($ticket->process_number) {
                // Remove qualquer ocorrência existente do número do processo no texto
                $processedText = preg_replace('/RECURSO ADMINISTRATIVO Nº ' . preg_quote($ticket->process_number, '/') . '/', '', $processedText);
                
                // Adiciona o número do processo no início do texto, após o cabeçalho da JARI
                $processedText = preg_replace(
                    '/(EXCELENTÍSSIMO\(A\) SENHOR\(A\) PRESIDENTE DA JUNTA ADMINISTRATIVA DE RECURSOS DE INFRAÇÕES DE TRÂNSITO - JARI\.)/',
                    "$1\n\nRECURSO ADMINISTRATIVO Nº {$ticket->process_number}\n",
                    $processedText,
                    1
                );
            }
        @endphp
        {!! $processedText !!}
    </div>

    <div class="footer">
        <p>Documento gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 