<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "=== Teste Simples de E-mail ===\n";

try {
    echo "📧 Enviando e-mail de teste...\n";
    
    Mail::raw('Este é um e-mail de teste do sistema AutoRecurso. Se você recebeu este e-mail, o sistema está funcionando corretamente!', function($message) {
        $message->to('pedroocferreira@gmail.com')
                ->subject('Teste de E-mail - AutoRecurso')
                ->from('nao-responda@autorecurso.online', 'AutoRecurso');
    });
    
    echo "✅ E-mail enviado com sucesso!\n";
    echo "📬 Verifique sua caixa de entrada (e spam)\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao enviar e-mail: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Fim do teste ===\n"; 