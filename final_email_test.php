<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Appeal;
use App\Models\User;
use App\Mail\RecursoGeradoMail;

echo "=== Teste Final de E-mail ===\n";

// Busca o recurso
$appeal = Appeal::find(26);
$user = User::find($appeal->user_id);

echo "📧 Enviando e-mail do recurso...\n";
echo "👤 Para: {$user->name} ({$user->email})\n";
echo "📄 Recurso ID: {$appeal->id}\n";
echo "📋 PDF: {$appeal->pdf_path}\n";

try {
    // Envia o e-mail
    Mail::to($user->email)->send(new RecursoGeradoMail($user, $appeal));
    
    echo "✅ E-mail enviado com sucesso!\n";
    echo "📬 Verifique sua caixa de entrada: {$user->email}\n";
    echo "🔗 Link de download: https://autorecurso.online/download/recurso/{$appeal->id}/" . preg_replace('/[^0-9]/', '', $appeal->ticket->cpf) . "\n";
    
    // Verifica se o PDF existe
    if (file_exists(storage_path('app/public/' . $appeal->pdf_path))) {
        echo "✅ PDF existe no servidor\n";
    } else {
        echo "❌ PDF não encontrado no servidor\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao enviar e-mail: " . $e->getMessage() . "\n";
}

echo "=== Fim do teste ===\n"; 