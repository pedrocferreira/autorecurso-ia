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
    
    <style>
        .section { margin-bottom: 14px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; font-weight: bold; margin: 10px 0 6px; text-align: center; }
        .muted { color: #555; }
        .list { margin: 6px 0 6px 18px; }
        .hr { height: 1px; background: #ccc; border: none; margin: 8px 0; }
    </style>

    <div class="title">Recurso Administrativo de Trânsito</div>

    <div class="section">
        <div class="subtitle">I. Identificação</div>
        <div>
            <strong>Condutor:</strong> {{ $ticket->name ?? 'N/I' }} — CPF: {{ $ticket->cpf ?? 'N/I' }} — CNH: {{ $ticket->driver_license ?? 'N/I' }}<br>
            <strong>Veículo:</strong> Placa {{ $ticket->plate ?? 'N/I' }} — Modelo {{ $ticket->vehicle_model ?? 'N/I' }} — Cor {{ $ticket->vehicle_color ?? 'N/I' }}<br>
            <strong>Auto/Notificação:</strong> {{ $ticket->citation_number ?? 'N/I' }} — Data {{ optional($ticket->date)->format('d/m/Y') ?? 'N/I' }} — Local {{ $ticket->location ?? 'N/I' }}
        </div>
    </div>

    <div class="section">
        <div class="subtitle">II. Síntese dos Fatos</div>
        <div class="muted">Exposição objetiva da ocorrência, data, local e contexto.</div>
        <div>{!! nl2br(e(Str::limit($text, 1500))) !!}</div>
    </div>

    <div class="section">
        <div class="subtitle">III. Preliminares</div>
        <ul class="list">
            <li>Regularidade formal do auto (art. 280 do CTB) e tempestividade da notificação (art. 281, par. único, II, CTB).</li>
            <li>Competência da autoridade autuadora e comprovação por meios idôneos.</li>
        </ul>
    </div>

    <div class="section">
        <div class="subtitle">IV. Mérito</div>
        <div>
            {!! nl2br(e($text)) !!}
        </div>
    </div>

    <div class="section">
        <div class="subtitle">V. Pedidos</div>
        <ul class="list">
            <li>O acolhimento das preliminares e a consequente nulidade do auto, se for o caso.</li>
            <li>Subsidiariamente, o cancelamento da penalidade por insuficiência de provas.</li>
            <li>A juntada e análise dos anexos apresentados.</li>
        </ul>
    </div>

    <div class="section">
        <div class="subtitle">VI. Anexos</div>
        <div class="muted">Cópia da notificação, CNH, CRLV, fotos, certificados (se houver).</div>
    </div>

    <hr class="hr" />

    <div class="section" style="text-align:right;">
        <div>{{ now()->format('d/m/Y') }}</div>
        <div>{{ $ticket->name ?? 'Assinatura' }}</div>
    </div>

    <div class="footer">
        <p>Documento gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 