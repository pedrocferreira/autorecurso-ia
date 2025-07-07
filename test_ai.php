<?php

require_once 'vendor/autoload.php';

// Carrega o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Teste de Geração de Recurso com IA ===\n";

// Dados de teste
$testData = [
    'name' => 'João Silva',
    'cpf' => '12345678901',
    'email' => 'joao@teste.com',
    'phone' => '11999999999',
    'driver_license' => '12345678901',
    'placa' => 'ABC1234',
    'vehicle_model' => 'Gol',
    'vehicle_year' => '2020',
    'ticket_number' => '123456789',
    'organ' => 'DETRAN-SP',
    'data' => '2024-01-15',
    'location' => 'São Paulo, SP',
    'infraction_type' => 1,
    'amount' => '130.16',
    'points' => '4',
    'was_driver' => 'sim',
    'had_signage' => 'nao',
    'details' => 'Não havia placa de velocidade visível'
];

echo "📝 Testando geração com GPT-4...\n";

try {
    $prompt = "Gere um recurso administrativo simples para contestar uma multa de trânsito. Nome: {$testData['name']}, CPF: {$testData['cpf']}, Auto: {$testData['ticket_number']}";
    
    $result = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
        'model' => 'gpt-4-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Você é um advogado especialista em recursos de multas de trânsito no Brasil.'
            ],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.4,
        'max_tokens' => 500
    ]);
    
    $text = $result->choices[0]->message->content;
    echo "✅ GPT-4 funcionando!\n";
    echo "📄 Texto gerado:\n";
    echo substr($text, 0, 200) . "...\n";
    
} catch (Exception $e) {
    echo "❌ Erro no GPT-4: " . $e->getMessage() . "\n";
}

echo "=== Fim do teste ===\n"; 