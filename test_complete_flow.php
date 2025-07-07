<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Appeal;
use App\Models\Ticket;
use App\Models\User;
use App\Models\CreditTransaction;
use App\Http\Controllers\ChatController;

echo "=== Teste do Fluxo Completo de Geração de Recurso ===\n";

// Dados de teste
$testData = [
    'name' => 'Maria Santos',
    'cpf' => '98765432100',
    'email' => 'maria@teste.com',
    'phone' => '11988888888',
    'driver_license' => '98765432100',
    'placa' => 'XYZ5678',
    'vehicle_model' => 'Onix',
    'vehicle_year' => '2021',
    'ticket_number' => '987654321',
    'organ' => 'DETRAN-RJ',
    'data' => '2024-02-20',
    'location' => 'Rio de Janeiro, RJ',
    'infraction_type' => 1,
    'amount' => '293.47',
    'points' => '7',
    'was_driver' => 'sim',
    'had_signage' => 'sim',
    'details' => 'Placa de velocidade estava visível mas não estava respeitando o limite'
];

echo "📝 Dados de teste preparados\n";

try {
    // Criar usuário
    echo "👤 Criando usuário...\n";
    $cpf = preg_replace('/[^0-9]/', '', $testData['cpf']);
    $user = User::where('cpf', $cpf)->first();
    
    if (!$user) {
        $user = User::create([
            'name' => $testData['name'],
            'email' => $testData['email'],
            'cpf' => $cpf,
            'phone' => $testData['phone'],
            'password' => bcrypt('autorecurso' . rand(1000, 9999)),
            'email_verified_at' => now(),
            'credits' => 0
        ]);
        echo "✅ Usuário criado: ID {$user->id}\n";
    } else {
        echo "✅ Usuário encontrado: ID {$user->id}\n";
    }

    // Criar ticket
    echo "🎫 Criando ticket...\n";
    $ticket = Ticket::create([
        'user_id' => $user->id,
        'name' => $testData['name'],
        'cpf' => $cpf,
        'email' => $testData['email'],
        'phone' => $testData['phone'],
        'driver_license' => $testData['driver_license'],
        'driver_license_category' => 'B',
        'address' => 'Endereço não informado',
        'plate' => strtoupper($testData['placa']),
        'vehicle_model' => $testData['vehicle_model'],
        'vehicle_year' => $testData['vehicle_year'],
        'vehicle_color' => 'Não informado',
        'vehicle_chassi' => 'Não informado',
        'vehicle_renavam' => 'Não informado',
        'ticket_number' => $testData['ticket_number'],
        'organ' => $testData['organ'],
        'date' => $testData['data'],
        'amount' => $testData['amount'],
        'points' => $testData['points'],
        'reason' => $testData['details'],
        'infraction_type_id' => $testData['infraction_type'],
        'location' => $testData['location'],
        'was_driver' => $testData['was_driver'] === 'sim',
        'had_signage' => $testData['had_signage'] === 'sim'
    ]);
    echo "✅ Ticket criado: ID {$ticket->id}\n";

    // Testar geração de texto com IA
    echo "🤖 Testando geração de texto com IA...\n";
    $controller = new ChatController(
        app(\App\Services\OpenAIService::class),
        app(\App\Services\PDFService::class),
        app(\App\Services\CreditService::class),
        app(\App\Services\AbacatePayService::class)
    );
    
    // Usar reflection para acessar método privado
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('generateAppealWithHybridIA');
    $method->setAccessible(true);
    
    $appealText = $method->invoke($controller, $testData);
    
    if (!empty($appealText)) {
        echo "✅ Texto gerado com sucesso!\n";
        echo "📄 Primeiros 200 caracteres:\n";
        echo substr($appealText, 0, 200) . "...\n";
        
        // Criar recurso
        echo "📋 Criando recurso...\n";
        $appeal = Appeal::create([
            'ticket_id' => $ticket->id,
            'text' => $appealText,
            'generated_text' => $appealText,
            'pdf_path' => null, // Será gerado depois
            'status' => 'pending',
            'user_id' => $user->id,
            'metadata' => json_encode([
                'generated_via' => 'test_script',
                'test_data' => true,
                'generated_at' => now()->toISOString()
            ])
        ]);
        echo "✅ Recurso criado: ID {$appeal->id}\n";
        
        // Gerar PDF
        echo "📄 Gerando PDF...\n";
        $pdfMethod = $reflection->getMethod('generateAppealPDF');
        $pdfMethod->setAccessible(true);
        
        $pdfPath = $pdfMethod->invoke($controller, $appealText, $ticket);
        echo "✅ PDF gerado: {$pdfPath}\n";
        
        // Atualizar recurso com caminho do PDF
        $appeal->update(['pdf_path' => $pdfPath]);
        echo "✅ Recurso atualizado com caminho do PDF\n";
        
    } else {
        echo "❌ Falha na geração do texto\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Fim do teste ===\n"; 