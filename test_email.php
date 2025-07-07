<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Appeal;
use App\Models\User;
use App\Http\Controllers\ChatController;

echo "=== Teste de Envio de E-mail ===\n";

// Busca o recurso
$appeal = Appeal::find(26);

if (!$appeal) {
    echo "❌ Recurso com ID 26 não encontrado\n";
    exit(1);
}

echo "✅ Recurso encontrado: ID {$appeal->id}\n";

// Busca o usuário
$user = User::find($appeal->user_id);
if (!$user) {
    echo "❌ Usuário não encontrado\n";
    exit(1);
}

echo "✅ Usuário encontrado: {$user->name} ({$user->email})\n";

// Verifica se tem PDF
if (empty($appeal->pdf_path)) {
    echo "❌ Recurso não tem PDF gerado\n";
    exit(1);
}

echo "✅ PDF encontrado: {$appeal->pdf_path}\n";

try {
    // Cria instância do controller
    $controller = new ChatController(
        app(\App\Services\OpenAIService::class),
        app(\App\Services\PDFService::class),
        app(\App\Services\CreditService::class),
        app(\App\Services\AbacatePayService::class)
    );
    
    // Usa reflection para acessar método privado
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('sendRecursoEmail');
    $method->setAccessible(true);
    
    echo "📧 Enviando e-mail...\n";
    $method->invoke($controller, $user, $appeal);
    
    echo "✅ E-mail enviado com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao enviar e-mail: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Fim do teste ===\n"; 