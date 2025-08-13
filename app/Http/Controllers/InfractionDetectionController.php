<?php

namespace App\Http\Controllers;

use App\Models\InfractionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class InfractionDetectionController extends Controller
{
    /**
     * Detecta automaticamente o tipo de infração baseado no motivo
     */
    public function detectInfractionType(Request $request)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'multa_data' => 'nullable|array'
            ]);

            $reason = $request->input('reason');
            $multaData = $request->input('multa_data', []);
            
            Log::info('🔍 Detectando tipo de infração', [
                'reason' => $reason,
                'multa_data' => $multaData
            ]);

            // Primeiro, tentar detecção por palavras-chave
            $detectedType = $this->detectByKeywords($reason);
            
            Log::info('🔍 Resultado da detecção por palavras-chave', [
                'detected' => $detectedType ? 'sim' : 'não',
                'type' => $detectedType ? [
                    'id' => $detectedType->id,
                    'code' => $detectedType->code,
                    'description' => $detectedType->description
                ] : null
            ]);
            
            if ($detectedType) {
                Log::info('✅ Tipo detectado por palavras-chave', [
                    'infraction_id' => $detectedType->id,
                    'code' => $detectedType->code
                ]);
                
                return response()->json([
                    'success' => true,
                    'detected_type' => [
                        'id' => $detectedType->id,
                        'code' => $detectedType->code,
                        'description' => $detectedType->description,
                        'confidence' => 'alta'
                    ],
                    'method' => 'keywords'
                ]);
            }

            // Se não encontrou por palavras-chave, usar IA
            $detectedType = $this->detectWithAI($reason, $multaData);
            
            if ($detectedType) {
                return response()->json([
                    'success' => true,
                    'detected_type' => [
                        'id' => $detectedType->id,
                        'code' => $detectedType->code,
                        'description' => $detectedType->description,
                        'confidence' => 'media'
                    ],
                    'method' => 'ai'
                ]);
            }

            // Fallback: retornar tipos mais comuns
            return response()->json([
                'success' => true,
                'suggested_types' => $this->getCommonTypes(),
                'method' => 'fallback'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao detectar tipo de infração: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao detectar tipo de infração. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Detecta por palavras-chave
     */
    private function detectByKeywords(string $reason): ?InfractionType
    {
        $reason = strtolower($reason);
        
        // Mapeamento de palavras-chave para códigos de infração
        $keywordMapping = [
            // Excesso de velocidade - mais específico
            'velocidade' => ['745-5', '746-3', '747-1'],
            'excesso de velocidade' => ['745-5', '746-3', '747-1'],
            'excesso' => ['745-5', '746-3', '747-1'],
            'km/h' => ['745-5', '746-3', '747-1'],
            'acima da velocidade' => ['745-5', '746-3', '747-1'],
            'radar' => ['745-5', '746-3', '747-1'],
            'lombada eletrônica' => ['745-5', '746-3', '747-1'],
            
            // Estacionamento - mais específico
            'estacionar' => ['545-21', '545-11', '545-22'],
            'estacionamento' => ['545-21', '545-11', '545-22'],
            'estacionamento irregular' => ['545-21'],
            'parar' => ['545-21', '545-11', '545-22'],
            'proibido estacionar' => ['545-21'],
            'zona azul' => ['545-21'],
            
            // Semáforo - mais específico
            'semáforo' => ['605-03'],
            'sinal' => ['605-03'],
            'vermelho' => ['605-03'],
            'semáforo vermelho' => ['605-03'],
            'sinal vermelho' => ['605-03'],
            'avançar sinal' => ['605-03'],
            
            // Celular - mais específico
            'celular' => ['736-62'],
            'telefone' => ['736-62'],
            'cel' => ['736-62'],
            'usar celular' => ['736-62'],
            'dirigir com celular' => ['736-62'],
            
            // Cinto de segurança - mais específico
            'cinto' => ['519-51'],
            'cinto de segurança' => ['519-51'],
            'sem cinto' => ['519-51'],
            'segurança' => ['519-51'],
            
            // CNH vencida - mais específico
            'cnh' => ['501-0', '502-9.1', '502-9.2'],
            'habilitação' => ['501-0', '502-9.1', '502-9.2'],
            'vencida' => ['501-0', '502-9.1', '502-9.2'],
            'cnh vencida' => ['501-0'],
            'habilitação vencida' => ['501-0'],
            
            // Placa - mais específico
            'placa' => ['640-8', '641-6'],
            'licenciamento' => ['640-8', '641-6'],
            'placa vencida' => ['640-8'],
            'licenciamento vencido' => ['640-8'],
            
            // Conversão - mais específico
            'conversão' => ['604-1.1'],
            'proibida' => ['604-1.1'],
            'conversão proibida' => ['604-1.1'],
            
            // Ultrapassagem - mais específico
            'ultrapassagem' => ['587-8', '588-6', '590-8'],
            'ultrapassar' => ['587-8', '588-6', '590-8'],
            'ultrapassagem proibida' => ['587-8'],
            
            // Faixa - mais específico
            'faixa' => ['612-2', '613-0'],
            'pedestre' => ['612-2', '613-0'],
            'faixa de pedestre' => ['612-2'],
            
            // Álcool - mais específico
            'álcool' => ['556-80'],
            'bafômetro' => ['556-80'],
            'embriagado' => ['556-80'],
            'dirigir embriagado' => ['556-80'],
            
            // Outros tipos comuns
            'farol' => ['533-1', '534-0'],
            'luz' => ['533-1', '534-0'],
            'documento' => ['501-0', '502-9.1', '502-9.2'],
            'documentos' => ['501-0', '502-9.1', '502-9.2'],
            'ipva' => ['640-8'],
            'dut' => ['640-8'],
            'seguro' => ['640-8']
        ];

        // Primeiro, tentar frases completas (mais específicas)
        foreach ($keywordMapping as $keyword => $codes) {
            if (strlen($keyword) > 3 && strpos($reason, $keyword) !== false) {
                Log::info("🔍 Frase completa encontrada: '{$keyword}'");
                
                // Encontrar o tipo de infração mais específico
                foreach ($codes as $code) {
                    $infractionType = InfractionType::where('code', $code)
                        ->where('active', true)
                        ->first();
                    
                    if ($infractionType) {
                        Log::info("✅ Tipo encontrado para frase completa: {$code}");
                        return $infractionType;
                    }
                }
            }
        }
        
        // Se não encontrou frases completas, tentar palavras-chave individuais
        foreach ($keywordMapping as $keyword => $codes) {
            if (strlen($keyword) <= 3 && strpos($reason, $keyword) !== false) {
                Log::info("🔍 Palavra-chave encontrada: '{$keyword}'");
                
                // Encontrar o tipo de infração mais específico
                foreach ($codes as $code) {
                    $infractionType = InfractionType::where('code', $code)
                        ->where('active', true)
                        ->first();
                    
                    if ($infractionType) {
                        Log::info("✅ Tipo encontrado para palavra-chave: {$code}");
                        return $infractionType;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Detecta usando IA
     */
    private function detectWithAI(string $reason, array $multaData): ?InfractionType
    {
        try {
            $client = new Client();
            
            $prompt = $this->buildDetectionPrompt($reason, $multaData);
            
            $response = $client->post(config('services.gemini.url') . '?key=' . config('services.gemini.api_key'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => config('services.gemini.api_key')
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 10,
                        'topP' => 0.8,
                        'maxOutputTokens' => 500,
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('🤖 Resposta da IA para detecção', ['response' => $text]);
            
            // Extrair código da resposta
            if (preg_match('/código[:\s]*(\d{3})/i', $text, $matches)) {
                $code = $matches[1];
                
                $infractionType = InfractionType::where('code', $code)
                    ->where('active', true)
                    ->first();
                
                if ($infractionType) {
                    return $infractionType;
                }
            }
            
            return null;

        } catch (\Exception $e) {
            Log::error('❌ Erro na detecção por IA', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Constrói o prompt para detecção
     */
    private function buildDetectionPrompt(string $reason, array $multaData): string
    {
        $location = $multaData['location'] ?? 'não especificado';
        $date = $multaData['date'] ?? 'não especificado';
        $time = $multaData['time'] ?? 'não especificado';
        
        return "ANÁLISE DE INFRAÇÃO DE TRÂNSITO

DADOS DA INFRAÇÃO:
- Motivo: {$reason}
- Local: {$location}
- Data: {$date}
- Horário: {$time}

TAREFA:
Analise o motivo da infração e identifique qual é o código da infração mais adequado baseado no CTB (Código de Trânsito Brasileiro).

CÓDIGOS PRINCIPAIS:
- 500-509: Estacionamento irregular
- 521-523: Semáforo/sinal
- 530-531: Conversão proibida
- 532: Dirigir sob influência de álcool
- 540-541: Ultrapassagem irregular
- 550-551: Faixa de pedestre
- 554-559: Excesso de velocidade
- 565: Cinto de segurança
- 570-571: CNH vencida
- 580-581: Placa/licenciamento

RESPONDA APENAS COM:
código: XXX

Onde XXX é o código da infração mais adequado.";
    }

    /**
     * Retorna tipos mais comuns
     */
    private function getCommonTypes(): array
    {
        return InfractionType::where('active', true)
            ->whereIn('code', ['554', '500', '521', '555', '565'])
            ->orderBy('code')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'code' => $type->code,
                    'description' => $type->description
                ];
            })
            ->toArray();
    }
} 