<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Indicação de Condutor</title>
	<style>
		@page { margin: 1.8cm; }
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; color: #333; }
		.header { text-align: center; margin-bottom: 14px; }
		.box { border: 1px solid #444; padding: 10px; margin-bottom: 10px; }
		.label { font-weight: bold; color: #222; }
		table { width: 100%; border-collapse: collapse; }
		td { padding: 4px 6px; vertical-align: top; }
		.small { font-size: 9pt; color: #666; }
		.section-title { font-weight: bold; margin: 6px 0; font-size: 12pt; }
		.signature-blocks { margin-top: 28px; }
		.signature { width: 48%; display: inline-block; text-align: center; }
		.sig-line { margin: 16px auto 4px; width: 95%; border-bottom: 1px solid #000; height: 1px; }
		.page-break { page-break-after: always; }
		.attachment { margin: 8px 0; page-break-inside: avoid; }
		img.attachment-img { width: 100%; height: auto; max-height: 980px; object-fit: contain; border: 1px solid #ddd; padding: 4px; }
	</style>
</head>
<body>
	<div class="header">
		<h2>Formulário de Indicação de Condutor - Documento Equivalente</h2>
		<div class="small">Conforme Resolução CONTRAN 918/2022</div>
	</div>

	<div class="box">
		<div class="section-title">Dados da Infração</div>
		<table>
			<tr>
				<td><span class="label">Auto de Infração:</span> {{ $form['auto_infracao'] ?? '' }}</td>
				<td><span class="label">Órgão Autuador:</span> {{ $form['orgao_autuador'] ?? '' }}</td>
			</tr>
			<tr>
				<td><span class="label">Placa:</span> {{ $form['placa'] ?? '' }}</td>
				<td><span class="label">Código da Infração:</span> {{ $form['codigo_infracao'] ?? '' }}</td>
			</tr>
			<tr>
				<td><span class="label">Data:</span> {{ $form['data'] ?? '' }}</td>
				<td><span class="label">Hora:</span> {{ $form['hora'] ?? '' }}</td>
			</tr>
		</table>
	</div>

	<div class="box">
		<div class="section-title">Dados do Condutor Indicado</div>
		<table>
			<tr>
				<td style="width: 60%"><span class="label">Nome:</span> {{ $form['condutor_nome'] ?? '' }}</td>
				<td style="width: 40%"><span class="label">CPF:</span> {{ $form['condutor_cpf'] ?? '' }}</td>
			</tr>
			<tr>
				<td><span class="label">RG:</span> {{ $form['condutor_rg'] ?? '' }}</td>
				<td><span class="label">CNH (Nº Registro):</span> {{ $form['condutor_cnh'] ?? '' }}</td>
			</tr>
		</table>
	</div>

	<div class="box">
		<div class="section-title">Dados do Proprietário</div>
		<table>
			<tr>
				<td style="width: 60%"><span class="label">Nome/Razão Social:</span> {{ $form['proprietario_nome'] ?? '' }}</td>
				<td style="width: 40%"><span class="label">Documento (CPF/CNPJ/RG):</span> {{ $form['proprietario_doc'] ?? '' }}</td>
			</tr>
			@if(!empty($form['is_pj']))
			<tr>
				<td colspan="2"><span class="label">Representante Legal:</span> {{ $form['representante_nome'] ?? '' }} - {{ $form['representante_doc'] ?? '' }}</td>
			</tr>
			@endif
		</table>
	</div>

	<div class="box">
		<div class="section-title">Declaração</div>
		<p class="small" style="line-height: 1.6; text-align: justify;">
			Declaro, para os devidos fins, ser verdadeira a indicação do condutor acima identificado como responsável pela
			infração relacionada, ciente das sanções previstas na legislação vigente em caso de falsidade. Autorizo o órgão
			autoador a proceder às anotações necessárias.
		</p>
		<div class="signature-blocks">
			<div class="signature">
				<div class="sig-line"></div>
				<div class="small">Assinatura do Proprietário</div>
			</div>
			<div class="signature" style="float: right;">
				<div class="sig-line"></div>
				<div class="small">Assinatura do Condutor</div>
			</div>
		</div>
		<div class="small" style="margin-top: 10px;">Local e Data: ______________________________</div>
	</div>

	@if(!empty($anexos))
		<div class="page-break"></div>
		<div class="section-title">Anexos</div>
		@foreach($anexos as $idx => $path)
			<div class="attachment">
				<div class="small">Anexo {{ $idx + 1 }}</div>
				<img class="attachment-img" src="{{ $path }}" />
			</div>
			@if($idx + 1 < count($anexos))
				<div class="page-break"></div>
			@endif
		@endforeach
	@endif

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



