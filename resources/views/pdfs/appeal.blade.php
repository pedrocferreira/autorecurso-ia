<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recurso Administrativo - Multa #{{ $ticket->id }}</title>
    <style>
        @page { margin: 2cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            font-size: 11pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12pt;
            font-weight: normal;
            margin-top: 0;
            color: #666;
        }
        .identification {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        .identification h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: #007bff;
        }
        .identification-item {
            margin-bottom: 5px;
        }
        .identification-label {
            font-weight: bold;
            color: #555;
        }
        .content {
            text-align: justify;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .content h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .signature {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            width: 250px;
            border-bottom: 1px solid #000;
            margin: 15px auto;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
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
            text-indent: 2cm;
        }
        p {
            margin-bottom: 12px;
            text-align: justify;
        }
        .date-location {
            text-align: right;
            margin: 30px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="page-number">Página <span class="pagenum"></span></div>
    
    <div class="header">
        <h1>Recurso Administrativo de Trânsito</h1>
        <h2>Contra Autuação de Infração</h2>
    </div>

    <div class="identification">
        <h3>Identificação da Infração</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Condutor:</span> {{ $ticket->name ?? 'N/I' }}
                </td>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">CPF:</span> {{ $ticket->cpf ?? 'N/I' }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">CNH:</span> {{ $ticket->driver_license ?? 'N/I' }}
                </td>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Placa:</span> {{ $ticket->plate ?? 'N/I' }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Modelo:</span> {{ $ticket->vehicle_model ?? 'N/I' }}
                </td>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Cor:</span> {{ $ticket->vehicle_color ?? 'N/I' }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Auto:</span> {{ $ticket->citation_number ?? 'N/I' }}
                </td>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Data:</span> {{ optional($ticket->date)->format('d/m/Y') ?? 'N/I' }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Órgão Autuador:</span> {{ $ticket->orgao_autuador ?? 'N/I' }}
                </td>
                <td style="width: 50%; padding: 3px 10px 3px 0; vertical-align: top;">
                    <span class="identification-label">Valor:</span> R$ {{ number_format($ticket->amount ?? 0, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="width: 100%; padding: 3px 10px 3px 0; vertical-align: top;" colspan="2">
                    <span class="identification-label">Local:</span> {{ $ticket->location ?? 'N/I' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <h3>Fundamentação do Recurso</h3>
        <div class="legal-indent">
            {!! nl2br(e($text)) !!}
        </div>
    </div>

    <div class="content">
        <h3>Pedidos</h3>
        <p class="legal-indent">
            Diante do exposto, requer a Vossa Excelência:
        </p>
        <ol style="margin-left: 2cm; line-height: 1.8;">
            <li>O acolhimento das preliminares e a consequente nulidade do auto de infração;</li>
            <li>Subsidiariamente, o cancelamento da penalidade por insuficiência de provas;</li>
            <li>A juntada e análise dos documentos apresentados;</li>
            <li>O deferimento do presente recurso administrativo.</li>
        </ol>
    </div>

    @php
        $attachments = [];
        if (isset($appeal) && $appeal->metadata) {
            $meta = is_array($appeal->metadata) ? $appeal->metadata : json_decode($appeal->metadata, true);
            if (isset($meta['attachments']) && is_array($meta['attachments'])) {
                $attachments = $meta['attachments'];
            }
        }
    @endphp

    @if(!empty($attachments))
        <div class="content">
            <h3>Anexos</h3>
            <p class="legal-indent">Segue, para fins de prova, a reprodução dos documentos anexados pelo recorrente.</p>
            @foreach($attachments as $idx => $att)
                @php
                    $publicPath = storage_path('app/public/' . ($att['path'] ?? ''));
                    $mime = $att['mime'] ?? '';
                @endphp
                @if(is_file($publicPath) && preg_match('/^image\//', $mime))
                    <div style="page-break-inside: avoid; margin-bottom: 16px;">
                        <div style="font-size: 10pt; color: #666; margin-bottom: 6px;">Anexo {{ $idx + 1 }} - {{ $att['name'] ?? 'Documento' }}</div>
                        <img src="{{ $publicPath }}" style="width: 100%; max-height: 900px; object-fit: contain; border: 1px solid #ddd; padding: 4px;" />
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="date-location">
        {{ $ticket->location ?? 'Local' }}, {{ now()->format('d') }} de {{ strftime('%B', strtotime(now())) }} de {{ now()->format('Y') }}
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <strong>{{ $ticket->name ?? 'Nome do Condutor' }}</strong><br>
        <span style="font-size: 10pt; color: #666;">CPF: {{ $ticket->cpf ?? 'N/I' }} | CNH: {{ $ticket->driver_license ?? 'N/I' }}</span>
    </div>

    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema AutoRecurso IA em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este recurso deve ser protocolado no prazo de 30 dias contados da data da notificação</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520; $y = 810; $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $size = 9; $color = [0,0,0]; $word_space = 0.0; $char_space = 0.0; $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html> 