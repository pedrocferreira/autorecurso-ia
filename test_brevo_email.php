<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "=== Teste Específico do Brevo ===\n";

try {
    echo "📧 Enviando e-mail via Brevo...\n";
    
    // Teste simples
    Mail::raw('Teste do Brevo - ' . date('Y-m-d H:i:s'), function($message) {
        $message->to('pedroocferreira@gmail.com')
                ->subject('Teste Brevo - AutoRecurso')
                ->from('nao-responda@autorecurso.online', 'AutoRecurso');
    });
    
    echo "✅ E-mail enviado via Brevo!\n";
    
    // Verificar configuração
    echo "🔧 Configuração atual:\n";
    echo "- Mailer: " . config('mail.default') . "\n";
    echo "- From: " . config('mail.from.address') . "\n";
    echo "- Name: " . config('mail.from.name') . "\n";
    echo "- Brevo API Key: " . (config('services.brevo.key') ? 'Configurada' : 'NÃO CONFIGURADA') . "\n";
    
    // Verificar logs
    echo "📋 Últimos logs de e-mail:\n";
    $logs = file_get_contents(storage_path('logs/laravel.log'));
    $emailLogs = preg_grep('/mail|email|brevo/i', explode("\n", $logs));
    $recentLogs = array_slice($emailLogs, -10);
    foreach ($recentLogs as $log) {
        echo "- " . $log . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Fim do teste ===\n"; 