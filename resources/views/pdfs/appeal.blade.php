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
            width: 200px;
            border-bottom: 1px solid #000;
            margin: 10px auto;
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
    
    <div class="content">
        {!! nl2br(e($text)) !!}
    </div>

    <div class="footer">
        <p>Documento gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 