<?php

namespace App\Http\Controllers;

use App\Models\InfractionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class InfractionJustificationController extends Controller
{
    /**
     * Gera justificativas específicas para um tipo de infração usando Gemini AI
     */
    public function generateJustifications(Request $request)
    {
        try {
            $request->validate([
                'infraction_type_id' => 'required|exists:infraction_types,id'
            ]);

            $infractionType = InfractionType::findOrFail($request->infraction_type_id);
            
            Log::info('Gerando justificativas para tipo de infração', [
                'infraction_id' => $infractionType->id,
                'code' => $infractionType->code,
                'description' => $infractionType->description
            ]);

            // Gera as justificativas usando Gemini
            $justifications = $this->generateWithGemini($infractionType);

            return response()->json([
                'success' => true,
                'infraction' => [
                    'id' => $infractionType->id,
                    'code' => $infractionType->code,
                    'description' => $infractionType->description,
                    'article' => $infractionType->law_article,
                    'points' => $infractionType->points,
                    'amount' => $infractionType->base_amount
                ],
                'justifications' => $justifications
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar justificativas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar justificativas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Gera justificativas contextualizadas baseadas nos dados específicos da multa
     */
    public function generateContextualizedJustifications(Request $request)
    {
        try {
            $request->validate([
                'infraction_type_id' => 'required|exists:infraction_types,id',
                'multa_data' => 'required|array',
                'multa_data.location' => 'nullable|string',
                'multa_data.date' => 'nullable|date',
                'multa_data.time' => 'nullable|string',
                'multa_data.amount' => 'nullable|numeric',
                'multa_data.citation_number' => 'nullable|string',
                'multa_data.vehicle_plate' => 'nullable|string'
            ]);

            $infractionType = InfractionType::findOrFail($request->infraction_type_id);
            $multaData = $request->input('multa_data', []);
            
            Log::info('Gerando justificativas contextualizadas', [
                'infraction_id' => $infractionType->id,
                'code' => $infractionType->code,
                'description' => $infractionType->description,
                'multa_data' => $multaData
            ]);

            // Gera as justificativas contextualizadas usando Gemini
            $justifications = $this->generateContextualizedWithGemini($infractionType, $multaData);

            return response()->json([
                'success' => true,
                'infraction' => [
                    'id' => $infractionType->id,
                    'code' => $infractionType->code,
                    'description' => $infractionType->description,
                    'article' => $infractionType->law_article,
                    'points' => $infractionType->points,
                    'amount' => $infractionType->base_amount
                ],
                'multa_context' => $multaData,
                'justifications' => $justifications
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar justificativas contextualizadas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar justificativas contextualizadas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Gera justificativas usando Gemini AI
     */
    private function generateWithGemini(InfractionType $infractionType)
    {
        $prompt = $this->buildJustificationPrompt($infractionType);

        // Loga o prompt enviado para a IA
        Log::info('📝 Prompt enviado para Gemini', [
            'infraction_id' => $infractionType->id,
            'code' => $infractionType->code,
            'description' => $infractionType->description,
            'prompt' => $prompt
        ]);

        $client = new Client();
        $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'key' => config('services.gemini.api_key')
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        [
                            'text' => 'Você é um advogado especialista em direito de trânsito brasileiro com profundo conhecimento do CTB, resoluções do CONTRAN e jurisprudência. Sua tarefa é analisar tipos específicos de infrações e sugerir argumentos jurídicos válidos para contestação.'
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1500,
                ]
            ],
            'timeout' => 30
        ]);

        $result = json_decode($response->getBody(), true);
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Loga a resposta bruta recebida da IA
        Log::info('📩 Resposta bruta da Gemini', [
            'infraction_id' => $infractionType->id,
            'response_text' => $text
        ]);

        return $this->parseJustifications($text, $infractionType);
    }

    /**
     * Constrói o prompt específico para análise de justificativas
     */
    private function buildJustificationPrompt(InfractionType $infractionType)
    {
        return "ANÁLISE JURÍDICA ESPECIALIZADA - INFRAÇÃO DE TRÂNSITO

DADOS DA INFRAÇÃO:
- Código: {$infractionType->code}
- Descrição: {$infractionType->description}
- Artigo Legal: {$infractionType->law_article}
- Pontos: {$infractionType->points}
- Valor Base: R$ " . number_format($infractionType->base_amount, 2, ',', '.') . "
- Gravidade: {$infractionType->severity}

TAREFA:
Como advogado especialista em direito de trânsito brasileiro, analise esta infração específica e forneça MÚLTIPLAS OPÇÕES de argumentos jurídicos que podem ser usados para contestá-la. O usuário deve poder escolher entre diferentes estratégias de defesa.

FORMATO DA RESPOSTA:
Retorne EXATAMENTE no formato JSON abaixo (sem texto adicional):

{
  \"estrategias_defesa\": [
    {
      \"id\": \"estrategia_1\",
      \"nome\": \"Nome da Estratégia de Defesa\",
      \"descricao\": \"Breve descrição da estratégia\",
      \"argumentos\": [
        {
          \"titulo\": \"Título do Argumento\",
          \"descricao\": \"Descrição técnica detalhada do argumento\",
      \"fundamentacao\": \"Base legal específica (CTB, Resolução CONTRAN, etc.)\",
      \"aplicabilidade\": \"baixa|media|alta\",
          \"explicacao\": \"Quando este argumento se aplica\",
          \"probabilidade_sucesso\": \"baixa|media|alta\"
        }
      ],
      \"vantagens\": [
        \"Vantagem 1 da estratégia\",
        \"Vantagem 2 da estratégia\"
      ],
      \"desvantagens\": [
        \"Desvantagem 1 da estratégia\",
        \"Desvantagem 2 da estratégia\"
      ],
      \"recomendacao\": \"Recomendado para casos onde...\"
    }
  ],
  \"argumentos_gerais\": [
    {
      \"titulo\": \"Argumento Geral\",
      \"descricao\": \"Descrição do argumento\",
      \"fundamentacao\": \"Base legal\",
      \"aplicabilidade\": \"baixa|media|alta\"
    }
  ],
  \"dicas_evidencias\": [
    \"Dica prática 1 sobre evidências\",
    \"Dica prática 2 sobre evidências\"
  ],
  \"artigos_relevantes\": [
    \"Art. XXX do CTB - Descrição\",
    \"Resolução CONTRAN XXX - Descrição\"
  ],
  \"instrucoes_escolha\": \"Como escolher a melhor estratégia baseada no seu caso específico\"
}

DIRETRIZES IMPORTANTES:
1. Forneça 3-4 estratégias diferentes de defesa
2. Cada estratégia deve ter 2-4 argumentos específicos
3. Inclua argumentos sobre vícios formais, procedimentais e técnicos
4. Cite artigos específicos do CTB e resoluções CONTRAN
5. Avalie a probabilidade de sucesso de cada argumento
6. Seja específico para o tipo de infração analisada
7. Inclua vantagens e desvantagens de cada estratégia
8. Dê orientações sobre como escolher a melhor estratégia

RESPONDA APENAS COM O JSON:";
    }

    /**
     * Faz parse das justificativas geradas pela IA
     */
    private function parseJustifications(string $response, InfractionType $infractionType, array $multaData = [])
    {
        // Remove possíveis marcações de código
        $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $response);
        $cleanResponse = trim($cleanResponse);
        
        Log::info('🔍 Analisando resposta da IA', [
            'infraction_id' => $infractionType->id,
            'response_length' => strlen($cleanResponse),
            'is_contextualized' => !empty($multaData)
        ]);
        
        try {
            $data = json_decode($cleanResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('❌ Erro no parse JSON', ['error' => json_last_error_msg()]);
                return $this->getFallbackJustifications($infractionType, $multaData);
            }
            
            // Valida estrutura esperada (suporta tanto formato antigo quanto novo)
            $hasValidStructure = false;
            
            if (isset($data['estrategias_defesa']) && is_array($data['estrategias_defesa'])) {
                // Novo formato com estratégias
                $hasValidStructure = true;
                $data['formato'] = 'estrategias';
            } elseif (isset($data['justificativas']) && is_array($data['justificativas'])) {
                // Formato antigo (compatibilidade)
                $hasValidStructure = true;
                $data['formato'] = 'justificativas';
            }
            
            if (!$hasValidStructure) {
                Log::warning('⚠️ Estrutura de justificativas inválida');
                return $this->getFallbackJustifications($infractionType, $multaData);
            }
            
            // Enriquece os dados se for contextualizado
            if (!empty($multaData)) {
                $data['contexto_multa'] = [
                    'local' => $multaData['location'] ?? null,
                    'data' => $multaData['date'] ?? null,
                    'horario' => $multaData['time'] ?? null,
                    'valor' => $multaData['amount'] ?? null,
                    'autuacao' => $multaData['citation_number'] ?? null
                ];
                
                // Adiciona análise de relevância baseada no contexto
                if ($data['formato'] === 'estrategias') {
                    foreach ($data['estrategias_defesa'] as &$estrategia) {
                        if (isset($estrategia['argumentos'])) {
                            foreach ($estrategia['argumentos'] as &$argumento) {
                                $argumento['relevancia_contextual'] = $this->calculateContextualRelevance(
                                    $argumento, 
                                    $infractionType, 
                                    $multaData
                                );
                            }
                        }
                    }
                } else {
                    foreach ($data['justificativas'] as &$justificativa) {
                        $justificativa['relevancia_contextual'] = $this->calculateContextualRelevance(
                            $justificativa, 
                            $infractionType, 
                            $multaData
                        );
                    }
                }
            }

            Log::info('✅ Justificativas parseadas com sucesso', [
                'infraction_id' => $infractionType->id,
                'total_estrategias' => isset($data['estrategias_defesa']) ? count($data['estrategias_defesa']) : 0,
                'total_justificativas' => isset($data['justificativas']) ? count($data['justificativas']) : 0,
                'categoria' => $data['categoria'] ?? 'não definida',
                'contextualizada' => !empty($multaData)
            ]);
            
            return $data;

        } catch (\Exception $e) {
            Log::error('❌ Erro ao fazer parse das justificativas: ' . $e->getMessage());
            return $this->getFallbackJustifications($infractionType, $multaData);
        }
    }

    /**
     * Calcula a relevância contextual de uma justificativa baseada nos dados da multa
     */
    private function calculateContextualRelevance(array $justificativa, InfractionType $infractionType, array $multaData): string
    {
        $score = 0;
        $factors = [];
        
        // Fatores baseados no local
        if (isset($multaData['location'])) {
            $location = strtolower($multaData['location']);
            
            // Locais urbanos vs rurais
            if (strpos($location, 'centro') !== false || strpos($location, 'avenida') !== false) {
                $score += 2;
                $factors[] = 'local_urbano_alta_demanda';
            }
            
            // Proximidade de escolas, hospitais
            if (strpos($location, 'escola') !== false || strpos($location, 'hospital') !== false) {
                $score += 3;
                $factors[] = 'zona_especial_protegida';
            }
            
            // Rodovias
            if (strpos($location, 'rodovia') !== false || strpos($location, 'br-') !== false) {
                $score += 1;
                $factors[] = 'rodovia_federal';
            }
        }
        
        // Fatores baseados no horário
        if (isset($multaData['time'])) {
            $hour = (int) substr($multaData['time'], 0, 2);
            
            // Horário de rush
            if (($hour >= 7 && $hour <= 9) || ($hour >= 17 && $hour <= 19)) {
                $score += 2;
                $factors[] = 'horario_pico';
            }
            
            // Madrugada
            if ($hour >= 22 || $hour <= 6) {
                $score += 1;
                $factors[] = 'horario_baixo_movimento';
            }
        }
        
        // Fatores baseados no tipo de infração
        $code = $infractionType->code ?? '';
        if (strpos($code, '554') !== false && isset($multaData['location'])) {
            // Excesso de velocidade em área específica
            $score += 2;
            $factors[] = 'velocidade_area_especifica';
        }
        
        // Determina relevância final
        if ($score >= 5) {
            return 'alta';
        } elseif ($score >= 3) {
            return 'media';
        } else {
            return 'baixa';
        }
    }

    /**
     * Retorna justificativas de fallback caso a IA falhe
     */
    private function getFallbackJustifications(InfractionType $infractionType, array $multaData = []): array
    {
        $isContextualized = !empty($multaData);
        $location = $multaData['location'] ?? 'local da infração';
        $date = $multaData['date'] ?? 'data da infração';
        
        $fallback = [
            'formato' => 'estrategias',
            'estrategias_defesa' => [
                [
                    'id' => 'estrategia_1',
                    'nome' => 'Estratégia de Análise Formal',
                    'descricao' => 'Foco na verificação dos requisitos formais do auto de infração',
                    'relevancia_contexto' => $isContextualized ? 'alta' : 'media',
                    'argumentos' => [
                        [
                            'titulo' => 'Análise dos Requisitos Formais',
                            'descricao' => $isContextualized 
                                ? "Verificação da conformidade do auto de infração com os requisitos do art. 280 do CTB, considerando as circunstâncias específicas ocorridas em {$location} em {$date}."
                                : 'Verificação da conformidade do auto de infração com os requisitos do art. 280 do CTB.',
                            'fundamentacao' => 'Art. 280 do CTB - Requisitos obrigatórios do auto de infração',
                            'aplicabilidade' => 'alta',
                            'explicacao' => 'Aplica-se a todos os casos onde há questionamento sobre a forma da autuação',
                            'probabilidade_sucesso' => 'media'
                        ],
                        [
                            'titulo' => 'Verificação de Competência',
                            'descricao' => 'Análise da competência do órgão autuador para aplicar a penalidade',
                            'fundamentacao' => 'Art. 280, §1º do CTB - Competência para autuação',
                    'aplicabilidade' => 'alta',
                            'explicacao' => 'Verifica se o órgão tem competência para autuar naquele local',
                            'probabilidade_sucesso' => 'baixa'
                        ]
                    ],
                    'vantagens' => [
                        'Base legal sólida',
                        'Aplicável a qualquer tipo de infração',
                        'Foco em aspectos técnicos'
                    ],
                    'desvantagens' => [
                        'Pode ser genérico demais',
                        'Depende da qualidade do auto'
                    ],
                    'recomendacao' => 'Recomendado para casos onde há dúvidas sobre a forma da autuação'
                ],
                [
                    'id' => 'estrategia_2',
                    'nome' => 'Estratégia de Proporcionalidade',
                    'descricao' => 'Foco na análise da proporcionalidade da penalidade aplicada',
                    'relevancia_contexto' => $isContextualized ? 'alta' : 'media',
                    'argumentos' => [
                        [
                            'titulo' => 'Princípio da Proporcionalidade',
                            'descricao' => $isContextualized
                                ? "Análise da proporcionalidade da penalidade aplicada considerando as condições específicas do local ({$location}) e circunstâncias da infração."
                                : 'Análise da proporcionalidade da penalidade aplicada face às circunstâncias da infração.',
                            'fundamentacao' => 'Princípio constitucional da proporcionalidade e razoabilidade',
                    'aplicabilidade' => 'media',
                            'explicacao' => 'Especialmente relevante em casos onde a penalidade parece desproporcional',
                            'probabilidade_sucesso' => 'baixa'
                        ]
                    ],
                    'vantagens' => [
                        'Argumento constitucional forte',
                        'Aplicável a casos específicos',
                        'Foco no mérito'
                    ],
                    'desvantagens' => [
                        'Subjetivo',
                        'Depende de interpretação judicial'
                    ],
                    'recomendacao' => 'Recomendado para casos onde a penalidade parece excessiva'
                ]
            ],
            'argumentos_gerais' => [
                [
                    'titulo' => 'Argumento Geral de Defesa',
                    'descricao' => 'Argumento geral aplicável a qualquer tipo de infração',
                    'fundamentacao' => 'Princípios gerais do direito administrativo',
                    'aplicabilidade' => 'baixa'
                ]
            ],
            'dicas_evidencias' => [
                'Verificar se o auto contém todos os elementos obrigatórios',
                'Analisar as condições do local da infração',
                'Documentar circunstâncias específicas que possam justificar a conduta'
            ],
            'artigos_relevantes' => [
                'Art. 280 do CTB - Elementos obrigatórios do auto de infração',
                'Art. 286 do CTB - Processo administrativo'
            ],
            'instrucoes_escolha' => 'Escolha a estratégia baseada nas características específicas do seu caso. A Estratégia de Análise Formal é mais técnica, enquanto a Estratégia de Proporcionalidade foca no mérito da penalidade.'
        ];
        
        if ($isContextualized) {
            $fallback['contexto_especifico'] = [
                'local_analise' => "Análise das características específicas de {$location}",
                'temporal_analise' => "Considerações sobre as condições na data {$date}",
                'circunstancial_analise' => 'Avaliação das circunstâncias gerais que podem influenciar o caso'
            ];
            
            $fallback['contexto_multa'] = [
                'local' => $multaData['location'] ?? null,
                'data' => $multaData['date'] ?? null,
                'horario' => $multaData['time'] ?? null,
                'valor' => $multaData['amount'] ?? null,
                'autuacao' => $multaData['citation_number'] ?? null
            ];
        }
        
        Log::info('📋 Usando justificativas de fallback', [
            'infraction_id' => $infractionType->id,
            'contextualizada' => $isContextualized
        ]);
        
        return $fallback;
    }

    /**
     * Gera justificativas contextualizadas usando Gemini AI
     */
    private function generateContextualizedWithGemini(InfractionType $infractionType, array $multaData)
    {
        $prompt = $this->buildContextualizedPrompt($infractionType, $multaData);

        // Loga o prompt enviado para a IA
        Log::info('📝 Prompt contextualizado enviado para Gemini', [
            'infraction_id' => $infractionType->id,
            'code' => $infractionType->code,
            'description' => $infractionType->description,
            'multa_data' => $multaData,
            'prompt' => $prompt
        ]);

        $client = new Client();
        $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'key' => config('services.gemini.api_key')
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        [
                            'text' => 'Você é um advogado especialista em direito de trânsito brasileiro com foco em argumentações técnicas e contextualizadas. Analise ESPECIFICAMENTE os dados da multa fornecidos e gere justificativas que se aplicam ao caso concreto, considerando local, data, hora e circunstâncias específicas. Retorne APENAS JSON válido sem texto adicional.'
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 20,
                    'topP' => 0.8,
                    'maxOutputTokens' => 2000,
                ]
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Loga a resposta bruta recebida da IA
        Log::info('📩 Resposta contextualizada da Gemini', [
            'infraction_id' => $infractionType->id,
            'response_text' => $text
        ]);

        return $this->parseJustifications($text, $infractionType, $multaData);
    }

    /**
     * Constrói o prompt contextualizado para análise de justificativas específicas
     */
    private function buildContextualizedPrompt(InfractionType $infractionType, array $multaData)
    {
        $location = $multaData['location'] ?? 'local não especificado';
        $date = $multaData['date'] ?? 'data não especificada';
        $time = $multaData['time'] ?? 'horário não especificado';
        $amount = $multaData['amount'] ?? 'valor não especificado';
        $citationNumber = $multaData['citation_number'] ?? 'número não especificado';
        $vehiclePlate = $multaData['vehicle_plate'] ?? 'placa não especificada';

        return "ANÁLISE JURÍDICA CONTEXTUALIZADA - INFRAÇÃO DE TRÂNSITO

DADOS DA INFRAÇÃO:
- Código: {$infractionType->code}
- Descrição: {$infractionType->description}
- Artigo Legal: {$infractionType->law_article}
- Pontos: {$infractionType->points}
- Valor Base: R$ " . number_format($infractionType->base_amount, 2, ',', '.') . "
- Gravidade: {$infractionType->severity}

DADOS ESPECÍFICOS DA MULTA:
- Local da Infração: {$location}
- Data da Infração: {$date}
- Horário da Infração: {$time}
- Valor da Multa: R$ {$amount}
- Número da Autuação: {$citationNumber}
- Placa do Veículo: {$vehiclePlate}

TAREFA:
Como advogado especialista em direito de trânsito brasileiro, analise esta infração específica considerando EXATAMENTE os dados fornecidos acima. Gere MÚLTIPLAS ESTRATÉGIAS de defesa contextualizadas que podem ser usadas para contestá-la, levando em conta:

1. As características específicas do LOCAL da infração
2. O HORÁRIO e DATA específicos da autuação
3. As circunstâncias que podem ter influenciado a situação
4. Argumentos técnicos aplicáveis ao caso concreto
5. Vícios formais ou materiais possíveis

FORMATO DA RESPOSTA:
Retorne EXATAMENTE no formato JSON abaixo (sem texto adicional):

{
  \"contexto_especifico\": {
    \"local_analise\": \"Análise específica das características do local da infração\",
    \"temporal_analise\": \"Análise do horário e data da infração\",
    \"circunstancial_analise\": \"Análise das circunstâncias específicas do caso\"
  },
  \"estrategias_defesa\": [
    {
      \"id\": \"estrategia_1\",
      \"nome\": \"Nome da Estratégia Contextualizada\",
      \"descricao\": \"Breve descrição da estratégia aplicada ao caso específico\",
      \"relevancia_contexto\": \"alta|media|baixa\",
      \"argumentos\": [
        {
          \"titulo\": \"Título do Argumento Contextualizado\",
          \"descricao\": \"Descrição técnica detalhada aplicada ao caso específico\",
          \"fundamentacao\": \"Base legal específica (CTB, Resolução CONTRAN, etc.)\",
          \"aplicabilidade\": \"baixa|media|alta\",
          \"contexto_aplicacao\": \"Como este argumento se aplica especificamente a este caso\",
          \"probabilidade_sucesso\": \"baixa|media|alta\",
          \"argumentos_especificos\": [
            \"Argumento específico 1 baseado nos dados da multa\",
            \"Argumento específico 2 baseado no local/horário\"
          ]
        }
      ],
      \"vantagens\": [
        \"Vantagem 1 da estratégia para este caso específico\",
        \"Vantagem 2 da estratégia baseada no contexto\"
      ],
      \"desvantagens\": [
        \"Desvantagem 1 da estratégia para este caso\",
        \"Desvantagem 2 baseada no contexto\"
      ],
      \"recomendacao\": \"Recomendado para este caso específico porque...\"
    }
  ],
  \"argumentos_gerais\": [
    {
      \"titulo\": \"Argumento Geral Aplicável\",
      \"descricao\": \"Descrição do argumento aplicado ao contexto\",
      \"fundamentacao\": \"Base legal\",
      \"aplicabilidade\": \"baixa|media|alta\"
    }
  ],
  \"dicas_evidencias\": [
    \"Dica prática específica para este tipo de local/situação\",
    \"Evidência recomendada baseada nas circunstâncias\"
  ],
  \"artigos_relevantes\": [
    \"Art. XXX do CTB - Descrição específica\",
    \"Resolução CONTRAN XXX - Aplicação ao caso\"
  ],
  \"precedentes_contextualizados\": [
    \"Precedente jurisprudencial aplicável à situação específica\",
    \"Decisão judicial similar ao contexto da multa\"
  ],
  \"instrucoes_escolha\": \"Como escolher a melhor estratégia baseada no contexto específico da sua multa\"
}

DIRETRIZES IMPORTANTES:
1. Foque em argumentos jurídicos VÁLIDOS e tecnicamente sólidos aplicados ao caso específico
2. Cite artigos específicos do CTB e resoluções CONTRAN
3. Considere as características do local da infração na análise
4. Analise se o horário da infração pode influenciar os argumentos
5. Sugira evidências específicas baseadas no contexto
6. Mantenha foco na legislação brasileira de trânsito
7. Priorize argumentos com maior aplicabilidade ao caso concreto
8. Considere vícios formais específicos que podem ocorrer neste tipo de situação";
    }
} 