<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Appeal;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

echo "=== Teste de Geração de PDF ===\n";

// Busca o recurso
$appeal = Appeal::find(26);

if (!$appeal) {
    echo "❌ Recurso com ID 26 não encontrado\n";
    exit(1);
}

echo "✅ Recurso encontrado: ID {$appeal->id}\n";
echo "📋 Ticket ID: {$appeal->ticket_id}\n";

// Busca o ticket
$ticket = Ticket::find($appeal->ticket_id);
if (!$ticket) {
    echo "❌ Ticket com ID {$appeal->ticket_id} não encontrado\n";
    exit(1);
}

echo "✅ Ticket encontrado: ID {$ticket->id}\n";

// Verifica se o template existe
$templatePath = resource_path('views/pdfs/appeal.blade.php');
if (!file_exists($templatePath)) {
    echo "❌ Template não encontrado em: {$templatePath}\n";
    exit(1);
}

echo "✅ Template encontrado\n";

// Verifica se tem texto gerado
if (empty($appeal->generated_text)) {
    echo "❌ Recurso não tem texto gerado\n";
    exit(1);
}

echo "✅ Texto do recurso encontrado (" . strlen($appeal->generated_text) . " caracteres)\n";

try {
    // Tenta gerar o PDF com as variáveis corretas
    echo "🔄 Gerando PDF...\n";
    $pdf = Pdf::loadView('pdfs.appeal', [
        'text' => $appeal->generated_text,
        'ticket' => $ticket
    ]);
    
    // Define o caminho do arquivo
    $pdfPath = storage_path('app/appeals/recurso_chat_' . $ticket->id . '_' . time() . '.pdf');
    echo "📁 Salvando em: {$pdfPath}\n";
    
    // Salva o PDF
    $pdf->save($pdfPath);
    
    // Verifica se foi salvo
    if (file_exists($pdfPath)) {
        echo "✅ PDF gerado com sucesso!\n";
        echo "📊 Tamanho do arquivo: " . filesize($pdfPath) . " bytes\n";
        
        // Atualiza o recurso com o caminho do PDF
        $appeal->update(['pdf_path' => 'appeals/recurso_chat_' . $ticket->id . '_' . time() . '.pdf']);
        echo "✅ Recurso atualizado com caminho do PDF\n";
    } else {
        echo "❌ PDF não foi salvo\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao gerar PDF: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Fim do teste ===\n"; 