<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class MultaImageController extends Controller
{
    /**
     * Processa imagem da multa/notificação e extrai dados usando Gemini Vision
     */
    public function processImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,pdf|max:10240', // 10MB max - aceita PDFs
            'type' => 'nullable|in:cnh,notification,vehicle', // Adicionado 'vehicle' para documento do veículo
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Erro na validação da imagem',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            $type = $request->input('type', 'notification'); // Padrão é notificação
            $file = $request->file('image');
            $mimeType = $file->getMimeType();
            
            Log::info('🖼️ Iniciando processamento de arquivo', [
                'tipo' => $type, 
                'mime_type' => $mimeType,
                'is_pdf' => $mimeType === 'application/pdf'
            ]);
            
            // Converter arquivo para base64
            $fileData = $this->convertImageToBase64($file);
            
            // Processar com Gemini Vision baseado no tipo
            $extractedData = $this->extractDataWithGeminiVision($fileData, $type, $mimeType);
            
            Log::info('✅ Dados extraídos com sucesso', [
                'tipo' => $type, 
                'dados_encontrados' => count($extractedData),
                'dados_completos' => $extractedData,
                'tem_infraction_type_id' => isset($extractedData['infraction_type_id'])
            ]);
            
            $successMessage = match($type) {
                'cnh' => 'Dados da CNH extraídos com sucesso!',
                'vehicle' => 'Dados do documento do veículo extraídos com sucesso!',
                default => 'Dados da notificação extraídos com sucesso!'
            };
            
            return response()->json([
                'success' => true,
                'data' => $extractedData,
                'message' => $successMessage
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao processar imagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar a imagem',
                'message' => 'Não foi possível extrair os dados da imagem. Verifique se a imagem está nítida e contém um documento válido.'
            ], 500);
        }
    }

    /**
     * Converte arquivo (imagem ou PDF) para base64
     */
    private function convertImageToBase64($file): string
    {
        $fileContent = file_get_contents($file->getRealPath());
        return base64_encode($fileContent);
    }

    /**
     * Extrai dados usando Gemini Vision API baseado no tipo de documento
     */
    private function extractDataWithGeminiVision(string $fileBase64, string $type = 'notification', string $mimeType = 'image/jpeg'): array
    {
        $prompt = $this->buildExtractionPrompt($type);
        $systemInstruction = $this->buildSystemInstruction($type);
        
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
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $fileBase64
                                ]
                            ]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        [
                            'text' => $systemInstruction
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1, // Baixa temperatura para máxima precisão
                    'topK' => 20,
                    'topP' => 0.8,
                    'maxOutputTokens' => 1500,
                ]
            ],
            'timeout' => 60 // Timeout maior para análise de imagem
        ]);

        $result = json_decode($response->getBody(), true);
        $textResponse = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Parse da resposta estruturada
        return $this->parseExtractedData($textResponse);
    }

    /**
     * Constrói prompt específico baseado no tipo de documento
     */
    private function buildExtractionPrompt(string $type = 'notification'): string
    {
        if ($type === 'cnh') {
            return $this->buildCnhPrompt();
        } else if ($type === 'vehicle') {
            return $this->buildVehiclePrompt();
        } else {
            return $this->buildNotificationPrompt();
        }
    }

    /**
     * Constrói prompt específico para CNH
     */
    private function buildCnhPrompt(): string
    {
        return "
ANALISE ESTA IMAGEM DE CNH (CARTEIRA NACIONAL DE HABILITAÇÃO) E EXTRAIA OS DADOS ESTRUTURADOS:

RETORNE UM JSON VÁLIDO com os seguintes campos (apenas os que estão claramente visíveis):

{
  \"nome\": \"nome completo do condutor\",
  \"cpf\": \"CPF do condutor\",
  \"cnh\": \"número da CNH\",
  \"categoria_cnh\": \"categoria da CNH (A, B, C, D, E, AB, AC, AD, AE)\",
  \"endereco\": \"endereço completo se visível\",
  \"telefone\": \"telefone se visível\",
  \"email\": \"email se visível\"
}

IMPORTANTE:
- Foque apenas nos dados pessoais do condutor visíveis na CNH
- Para CPF, use formato: 000.000.000-00
- Para CNH, extraia apenas os números
- Para categoria, use apenas as letras (A, B, C, D, E, AB, AC, AD, AE)
- Se algum campo não estiver visível, use null
- Seja preciso e não invente dados que não estão na imagem

RESPONDA APENAS COM O JSON, SEM TEXTO ADICIONAL.
";
    }

    /**
     * Constrói instrução do sistema baseada no tipo de documento
     */
    private function buildSystemInstruction(string $type = 'notification'): string
    {
        if ($type === 'cnh') {
            return 'Você é um especialista em análise de documentos de identificação brasileiros. Analise imagens de CNH (Carteira Nacional de Habilitação) para extrair dados pessoais estruturados. Seja preciso e extraia apenas informações que estão claramente visíveis na imagem.';
        } else if ($type === 'vehicle') {
            return 'Você é um especialista em análise de documentos de veículos brasileiros. Analise imagens de CRLV (Certificado de Registro e Licenciamento de Veículo) para extrair dados do veículo e proprietário estruturados. Seja preciso e extraia apenas informações que estão claramente visíveis na imagem.';
        } else {
            return 'Você é um especialista em análise de documentos de trânsito brasileiro. Analise imagens de multas, notificações e auto de infrações para extrair dados estruturados. Seja preciso e extraia apenas informações que estão claramente visíveis na imagem.';
        }
    }

    /**
     * Constrói prompt específico para documento do veículo
     */
    private function buildVehiclePrompt(): string
    {
        return "
ANALISE ESTA IMAGEM DE DOCUMENTO DO VEÍCULO (CRLV) E EXTRAIA OS DADOS ESTRUTURADOS:

RETORNE UM JSON VÁLIDO com os seguintes campos (apenas os que estão claramente visíveis):

{
  \"placa\": \"AAA0000\",
  \"renavam\": \"00000000000000000\",
  \"modelo\": \"modelo do veículo\",
  \"cor\": \"cor do veículo\",
  \"ano\": \"ano do veículo\",
  \"uf\": \"UF\",
  \"municipio\": \"nome do município\",
  \"proprietario\": \"nome completo do proprietário\",
  \"cpf_proprietario\": \"CPF do proprietário\",
  \"endereco_proprietario\": \"endereço completo do proprietário\",
  \"telefone_proprietario\": \"telefone do proprietário\",
  \"email_proprietario\": \"email do proprietário\"
}

IMPORTANTE:
- Foque nos dados do veículo e do proprietário
- Para CPF, use formato: 000.000.000-00
- Para placa, use formato: AAA0000 ou AAA0A00
- Se algum campo não estiver visível, use null
- Seja preciso e não invente dados que não estão na imagem

RESPONDA APENAS COM O JSON, SEM TEXTO ADICIONAL.
";
    }

    /**
     * Constrói prompt específico para notificação/multa
     */
    private function buildNotificationPrompt(): string
    {
        return "
ANALISE ESTA IMAGEM DE MULTA/NOTIFICAÇÃO DE TRÂNSITO E EXTRAIA OS DADOS ESTRUTURADOS:

RETORNE UM JSON VÁLIDO com os seguintes campos (apenas os que estão claramente visíveis):

{
  \"nome\": \"nome completo do condutor se visível\",
  \"cpf\": \"CPF do condutor se visível\",
  \"cnh\": \"número da CNH se visível\",
  \"categoria_cnh\": \"categoria da CNH se visível\",
  \"endereco\": \"endereço completo se visível\",
  \"telefone\": \"telefone se visível\",
  \"email\": \"email se visível\",
  \"placa\": \"AAA0000\",
  \"numero_autuacao\": \"número da autuação/auto\",
  \"data_infracao\": \"YYYY-MM-DD\",
  \"hora_infracao\": \"HH:MM\",
  \"local_infracao\": \"endereço/local da infração\",
  \"cidade\": \"cidade da infração\",
  \"estado\": \"UF\",
  \"valor_multa\": \"999.99\",
  \"pontos\": \"0\",
  \"codigo_infracao\": \"código CTB (ex: 501-0, 502-0, etc)\",
  \"descricao_infracao\": \"descrição da infração\",
  \"orgao_autuador\": \"nome do órgão\",
  \"agente\": \"nome/ID do agente\",
  \"veiculo_modelo\": \"modelo do veículo se visível\",
  \"veiculo_cor\": \"cor do veículo se visível\",
  \"velocidade_permitida\": \"km/h se for excesso de velocidade\",
  \"velocidade_aferida\": \"km/h se for excesso de velocidade\",
  \"observacoes\": \"outras informações relevantes\"
}

IMPORTANTE:
- Foque nos dados da infração e do veículo
- Use formato de data brasileiro: DD/MM/AAAA → converta para YYYY-MM-DD
- Se algum campo não estiver visível, use null
- Para valores monetários, use apenas números com ponto decimal
- Para códigos de infração, extraia apenas os números/códigos visíveis
- Seja preciso e não invente dados que não estão na imagem

RESPONDA APENAS COM O JSON, SEM TEXTO ADICIONAL.
";
    }

    /**
     * Faz parse da resposta do Gemini e extrai JSON
     */
    private function parseExtractedData(string $response): array
    {
        Log::info('🔍 Analisando resposta do Gemini Vision', [
            'response_length' => strlen($response),
            'response_preview' => substr($response, 0, 500)
        ]);
        
        // Tenta extrair JSON da resposta
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $response, $jsonMatch)) {
            $jsonString = $jsonMatch[0];
            Log::info('📋 JSON extraído da resposta', ['json' => $jsonString]);
            
            try {
                $data = json_decode($jsonString, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info('✅ JSON parseado com sucesso', ['campos_encontrados' => array_keys($data)]);
                    // Limpa e valida os dados extraídos
                    return $this->cleanAndValidateExtractedData($data);
                } else {
                    Log::error('❌ Erro ao fazer parse do JSON', ['error' => json_last_error_msg()]);
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Erro ao fazer parse do JSON extraído: ' . $e->getMessage());
            }
        } else {
            Log::warning('⚠️ Nenhum JSON encontrado na resposta do Gemini');
        }
        
        // Se não conseguiu extrair JSON, tenta parse alternativo
        Log::info('🔄 Tentando parse alternativo da resposta');
        return $this->alternativeDataParsing($response);
    }

    /**
     * Limpa e valida dados extraídos
     */
    private function cleanAndValidateExtractedData(array $data): array
    {
        Log::info('🔍 Iniciando limpeza e validação dos dados', ['dados_brutos' => $data]);
        
        $cleanData = [];
        
        // Mapeia campos com validação
        $fieldMap = [
            'nome' => 'name',
            'cpf' => 'cpf',
            'cnh' => 'driver_license',
            'categoria_cnh' => 'driver_license_category',
            'endereco' => 'address',
            'telefone' => 'phone',
            'email' => 'email',
            'placa' => 'plate',
            'numero_autuacao' => 'citation_number', 
            'data_infracao' => 'date',
            'hora_infracao' => 'time',
            'local_infracao' => 'location',
            'cidade' => 'city',
            'estado' => 'state',
            'valor_multa' => 'amount',
            'pontos' => 'points',
            'codigo_infracao' => 'infraction_code',
            'codigo' => 'infraction_code', // Adiciona mapeamento alternativo
            'descricao_infracao' => 'reason',
            'orgao_autuador' => 'organ',
            'veiculo_modelo' => 'vehicle_model',
            'veiculo_cor' => 'vehicle_color',
            'observacoes' => 'observations',
            'renavam' => 'renavam',
            'ano' => 'year',
            'proprietario' => 'owner_name',
            'cpf_proprietario' => 'owner_cpf',
            'endereco_proprietario' => 'owner_address',
            'telefone_proprietario' => 'owner_phone',
            'email_proprietario' => 'owner_email'
        ];
        
        foreach ($fieldMap as $originalKey => $mappedKey) {
            if (isset($data[$originalKey]) && $data[$originalKey] !== null && $data[$originalKey] !== '') {
                $value = $data[$originalKey];
                
                // Limpeza específica por tipo de campo
                switch ($mappedKey) {
                    case 'plate':
                        $cleanData[$mappedKey] = strtoupper(preg_replace('/[^A-Z0-9]/', '', $value));
                        break;
                    case 'date':
                        $cleanData[$mappedKey] = $this->convertDateFormat($value);
                        break;
                    case 'amount':
                        $cleanData[$mappedKey] = $this->cleanMoneyValue($value);
                        break;
                    case 'points':
                        $cleanData[$mappedKey] = (int) preg_replace('/[^0-9]/', '', $value);
                        break;
                    case 'state':
                        $cleanData[$mappedKey] = strtoupper(substr(preg_replace('/[^A-Z]/', '', $value), 0, 2));
                        break;
                    case 'renavam':
                        $cleanData[$mappedKey] = preg_replace('/[^0-9]/', '', $value);
                        break;
                    case 'year':
                        $cleanData[$mappedKey] = preg_replace('/[^0-9]/', '', $value);
                        break;
                    case 'owner_cpf':
                        $cleanData[$mappedKey] = preg_replace('/[^0-9]/', '', $value);
                        break;
                    default:
                        $cleanData[$mappedKey] = trim($value);
                }
                
                Log::info("✅ Campo processado: {$originalKey} -> {$mappedKey} = {$cleanData[$mappedKey]}");
            } else {
                Log::info("❌ Campo vazio ou não encontrado: {$originalKey}");
            }
        }
        
        // Tenta mapear tipo de infração baseado no código
        if (isset($cleanData['infraction_code']) && !empty($cleanData['infraction_code'])) {
            Log::info('🔍 Tentando mapear tipo de infração', ['codigo' => $cleanData['infraction_code']]);
            $infractionType = $this->mapInfractionTypeByCode($cleanData['infraction_code']);
            if ($infractionType) {
                $cleanData['infraction_type_id'] = $infractionType->id;
                $cleanData['suggested_points'] = $infractionType->points;
                $cleanData['suggested_amount'] = $infractionType->base_amount;
                Log::info('🎯 Tipo de infração mapeado com sucesso', [
                    'id' => $infractionType->id, 
                    'codigo' => $infractionType->code,
                    'descricao' => $infractionType->description,
                    'infraction_type_id_set' => $cleanData['infraction_type_id']
                ]);
            } else {
                Log::warning('⚠️ Tipo de infração não encontrado para o código', ['codigo' => $cleanData['infraction_code']]);
            }
        } else {
            Log::info('ℹ️ Nenhum código de infração encontrado para mapear', ['infraction_code' => $cleanData['infraction_code'] ?? 'não definido']);
        }
        
        Log::info('✨ Dados limpos e validados', ['campos_extraidos' => array_keys($cleanData), 'total_campos' => count($cleanData)]);
        
        return $cleanData;
    }

    /**
     * Parse alternativo se JSON não funcionar
     */
    private function alternativeDataParsing(string $response): array
    {
        $data = [];
        
        // Tenta extrair informações usando regex
        $patterns = [
            'plate' => '/placa[:\s]*([A-Z0-9]{7})/i',
            'citation_number' => '/auto[:\s]*([0-9A-Z\-\/]+)/i', 
            'amount' => '/valor[:\s]*R?\$?\s*([0-9,\.]+)/i',
            'date' => '/data[:\s]*([0-9]{1,2}[\/\-][0-9]{1,2}[\/\-][0-9]{4})/i',
            'renavam' => '/renavam[:\s]*([0-9]+)/i',
            'year' => '/ano[:\s]*([0-9]{4})/i',
            'owner_cpf' => '/cpf[:\s]*([0-9]{3}[\.][0-9]{3}[\.][0-9]{3}[\-][0-9]{2})/i'
        ];
        
        foreach ($patterns as $field => $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                $data[$field] = trim($matches[1]);
            }
        }
        
        return $data;
    }

    /**
     * Converte formato de data brasileiro para ISO
     */
    private function convertDateFormat(string $date): string
    {
        // Formatos possíveis: DD/MM/YYYY, DD-MM-YYYY
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/', $date, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "$year-$month-$day";
        }
        
        return $date;
    }

    /**
     * Limpa valores monetários
     */
    private function cleanMoneyValue(string $value): float
    {
        // Remove R$, espaços, e converte vírgulas para pontos
        $cleaned = preg_replace('/[R$\s]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return (float) $cleaned;
    }

    /**
     * Tenta mapear código de infração para tipo específico com maior precisão
     */
    private function mapInfractionTypeByCode(string $code): ?\App\Models\InfractionType
    {
        // Remove espaços, hífens e letras para comparar só números
        $normalized = preg_replace('/[^0-9]/', '', $code);
        if (!$normalized) return null;

        // Também tenta com hífen (formato padrão do CTB)
        $withHyphen = preg_replace('/(\d{3})(\d)/', '$1-$2', $normalized);
        if (strlen($normalized) >= 4) {
            $withHyphen = substr($normalized, 0, 3) . '-' . substr($normalized, 3);
        }

        Log::info('🔍 Buscando tipo de infração com maior precisão', [
            'codigo_original' => $code,
            'codigo_normalizado' => $normalized,
            'codigo_com_hifen' => $withHyphen
        ]);

        // 1. Busca exata primeiro (mais precisa)
        $infractionType = \App\Models\InfractionType::where(function($query) use ($code, $normalized, $withHyphen) {
            $query->where('code', '=', $code)
                  ->orWhere('code', '=', $normalized)
                  ->orWhere('code', '=', $withHyphen);
        })->first();

        if ($infractionType) {
            Log::info('🎯 Tipo de infração encontrado (busca exata)', [
                'codigo_extraido' => $code,
                'tipo_encontrado' => $infractionType->code . ' - ' . $infractionType->description,
                'id_encontrado' => $infractionType->id
            ]);
            return $infractionType;
        }

        // 2. Busca por LIKE mais específica
        $infractionType = \App\Models\InfractionType::where(function($query) use ($code, $normalized, $withHyphen) {
            $query->where('code', 'LIKE', "%{$normalized}%")
                  ->orWhere('code', 'LIKE', "%{$withHyphen}%")
                  ->orWhere('law_article', 'LIKE', "%{$normalized}%")
                  ->orWhere('law_article', 'LIKE', "%{$withHyphen}%");
        })->orderBy('code')->first();

        if ($infractionType) {
            Log::info('🎯 Tipo de infração encontrado (busca LIKE)', [
                'codigo_extraido' => $code,
                'tipo_encontrado' => $infractionType->code . ' - ' . $infractionType->description,
                'id_encontrado' => $infractionType->id
            ]);
            return $infractionType;
        }

        // 3. Busca por similaridade de código melhorada
        Log::info('🔄 Tentando busca por similaridade avançada...');
        $allInfractions = \App\Models\InfractionType::all();
        
        foreach ($allInfractions as $infraction) {
            $infractionCodeClean = str_replace(['-', ' '], '', $infraction->code);
            
            // Verifica correspondência exata de códigos limpos
            if ($normalized === $infractionCodeClean) {
                Log::info('🎯 Tipo de infração encontrado (similaridade exata)', [
                    'codigo_extraido' => $code,
                    'codigo_normalizado' => $normalized,
                    'codigo_infracao_limpo' => $infractionCodeClean,
                    'tipo_encontrado' => $infraction->code . ' - ' . $infraction->description,
                    'id_encontrado' => $infraction->id
                ]);
                return $infraction;
            }
            
            // Verifica se o código extraído está contido no código da infração
            if (strlen($normalized) >= 3 && 
                (strpos($infractionCodeClean, $normalized) !== false || 
                 strpos($normalized, $infractionCodeClean) !== false)) {
                Log::info('🎯 Tipo de infração encontrado (similaridade parcial)', [
                    'codigo_extraido' => $code,
                    'codigo_normalizado' => $normalized,
                    'codigo_infracao_limpo' => $infractionCodeClean,
                    'tipo_encontrado' => $infraction->code . ' - ' . $infraction->description,
                    'id_encontrado' => $infraction->id
                ]);
                return $infraction;
            }
        }

        // 4. Busca por palavras-chave na descrição baseada no código
        Log::info('🔄 Tentando busca por palavras-chave específicas...');
        $codeBasedKeywords = $this->getKeywordsFromCode($normalized);
        
        if (!empty($codeBasedKeywords)) {
            foreach ($allInfractions as $infraction) {
                foreach ($codeBasedKeywords as $keyword) {
                    if (stripos($infraction->description, $keyword) !== false || 
                        stripos($infraction->law_article, $keyword) !== false) {
                        Log::info('🎯 Tipo de infração encontrado (palavra-chave)', [
            'codigo_extraido' => $code,
                            'palavra_chave' => $keyword,
                            'tipo_encontrado' => $infraction->code . ' - ' . $infraction->description,
                            'id_encontrado' => $infraction->id
        ]);
                        return $infraction;
                    }
                }
            }
        }

        Log::warning('❌ Nenhum tipo de infração encontrado para código: ' . $code);
        return null;
    }

    /**
     * Obtém palavras-chave baseadas em códigos comuns de infração
     */
    private function getKeywordsFromCode(string $code): array
    {
        $keywords = [];
        
        // Mapeamento baseado em códigos conhecidos do CTB
        $codeMap = [
            '554' => ['velocidade', 'excesso'],
            '162' => ['celular', 'telefone', 'aparelho'],
            '161' => ['estacionamento', 'parar'],
            '165' => ['sinalização', 'sinal'],
            '169' => ['semáforo'],
            '208' => ['cinto', 'segurança'],
            '230' => ['documentos', 'cnh'],
            '261' => ['licenciamento'],
            '503' => ['habilitação'],
            '203' => ['farol'],
            '244' => ['conversão'],
            '191' => ['ultrapassagem']
        ];
        
        foreach ($codeMap as $codePattern => $keywordList) {
            if (strpos($code, $codePattern) !== false) {
                $keywords = array_merge($keywords, $keywordList);
            }
        }
        
        return array_unique($keywords);
    }
} 