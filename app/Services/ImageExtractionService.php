<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageExtractionService
{
    private $geminiApiKey;
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key');
    }

    /**
     * Extrai dados de uma imagem usando Gemini Vision API
     */
    public function extractDataFromImage(string $imagePath, string $documentType): array
    {
        try {
            Log::info('🔍 Iniciando extração de dados da imagem', [
                'arquivo' => basename($imagePath),
                'tipo' => $documentType
            ]);

            // Verificar se o arquivo existe
            if (!file_exists($imagePath)) {
                throw new \Exception('Arquivo não encontrado: ' . $imagePath);
            }

            // Ler e codificar a imagem em base64
            $imageData = file_get_contents($imagePath);
            $base64Image = base64_encode($imageData);

            // Construir o prompt baseado no tipo de documento
            $prompt = $this->buildPrompt($documentType);

            // Preparar dados para a API
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $this->getMimeType($imagePath),
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 32,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                ]
            ];

            // Fazer requisição para a API do Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, $requestData);

            if (!$response->successful()) {
                Log::error('❌ Erro na API do Gemini', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('Erro na API do Gemini: ' . $response->status());
            }

            $responseData = $response->json();
            
            // Extrair o texto da resposta
            $extractedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('📄 Texto extraído da imagem', [
                'tamanho' => strlen($extractedText),
                'tipo' => $documentType
            ]);

            // Processar o texto extraído baseado no tipo de documento
            $processedData = $this->processExtractedText($extractedText, $documentType);

            Log::info('✅ Dados processados com sucesso', $processedData);

            return $processedData;

        } catch (\Exception $e) {
            Log::error('❌ Erro na extração de dados da imagem', [
                'erro' => $e->getMessage(),
                'arquivo' => $imagePath,
                'tipo' => $documentType
            ]);

            // Retornar dados de fallback
            return $this->getFallbackData($documentType);
        }
    }

    /**
     * Constrói o prompt específico para cada tipo de documento
     */
    private function buildPrompt(string $documentType): string
    {
        switch ($documentType) {
            case 'cnh':
                return $this->buildCnhPrompt();
            case 'notification':
                return $this->buildNotificationPrompt();
            default:
                return $this->buildGenericPrompt();
        }
    }

    /**
     * Prompt específico para CNH
     */
    private function buildCnhPrompt(): string
    {
        return "Analise esta imagem da CNH (Carteira Nacional de Habilitação) e extraia os seguintes dados em formato JSON:

Dados obrigatórios:
- name: Nome completo do portador (campo NOME)
- cpf: CPF do portador (formato XXX.XXX.XXX-XX)
- driver_license: Número da CNH (campo Nº REGISTRO - este é o número principal da CNH)
- birth_date: Data de nascimento (formato YYYY-MM-DD)

Dados opcionais:
- rg: Número do RG (campo DOC. IDENTIDADE)
- father_name: Nome do pai (primeira linha do campo FILIAÇÃO)
- mother_name: Nome da mãe (segunda linha do campo FILIAÇÃO)
- category: Categoria da CNH (campo CAT. HAB. - ex: A, B, AB, C, etc.)
- issue_date: Data de emissão (campo DATA EMISSÃO)
- expiry_date: Data de vencimento (campo VALIDADE)
- first_license_date: Data da primeira habilitação (campo 1ª HABILITAÇÃO)

IMPORTANTE:
- O Nº REGISTRO é o número principal da CNH que deve ser usado como driver_license
- Procure especificamente pelo campo 'Nº REGISTRO' na imagem
- Este número geralmente tem 11 dígitos
- Não confunda com outros números como códigos de barras ou números de controle

Regras importantes:
1. Extraia apenas os dados que estão visíveis na imagem
2. Se algum dado não estiver visível, não inclua no JSON
3. Use formato JSON válido
4. Para CPF, use sempre o formato XXX.XXX.XXX-XX
5. Para datas, use formato YYYY-MM-DD
6. Para nomes, use apenas letras, espaços e acentos
7. Para driver_license, use apenas números (sem pontos ou traços)

Retorne apenas o JSON, sem texto adicional.";

    }

    /**
     * Prompt específico para notificação de multa
     */
    private function buildNotificationPrompt(): string
    {
        return "Analise esta imagem de notificação/multa de trânsito e extraia os seguintes dados em formato JSON:

Dados obrigatórios:
- citation_number: Número da notificação/auto de infração (procure por campos como 'Número do Auto', 'Auto de Infração', 'Número da Notificação')
- date: Data da infração (formato YYYY-MM-DD, procure por 'Data da Infração', 'Data', 'Dia')
- time: Horário da infração (formato HH:MM, procure por 'Hora', 'Horário', 'Hora da Infração')
- amount: Valor da multa (apenas números, sem R$, procure por 'Valor', 'Multa', 'Valor da Multa')
- location: Local da infração (procure por 'Local', 'Endereço', 'Local da Infração', 'Via')
- reason: Motivo da infração (procure por 'Infração', 'Motivo', 'Descrição', 'Tipo de Infração')
- plate: Placa do veículo (formato AAA-0000 ou AAA0A00, procure por 'Placa', 'Placa do Veículo')

Dados opcionais:
- points: Pontos na CNH (procure por 'Pontos', 'Pontuação')
- article: Artigo do CTB (procure por 'Artigo', 'Art.', 'CTB')
- city: Cidade (procure por 'Cidade', 'Município')
- state: Estado (sigla, procure por 'Estado', 'UF')
- infraction_code: Código da infração (procure por 'Código', 'Código da Infração')
- vehicle_model: Modelo do veículo (procure por 'Modelo', 'Marca/Modelo')
- vehicle_color: Cor do veículo (procure por 'Cor', 'Cor do Veículo')
- driver_name: Nome do condutor (procure por 'Condutor', 'Nome do Condutor', 'Motorista')
- driver_cpf: CPF do condutor (procure por 'CPF', 'CPF do Condutor')
- driver_cnh: CNH do condutor (procure por 'CNH', 'Número da CNH', 'Habilitação')

IMPORTANTE:
- Procure especificamente pelos campos mencionados acima
- O número da notificação geralmente é um número longo (10-15 dígitos)
- O valor da multa pode estar em formato R$ X,XX ou apenas números
- A placa pode estar no formato antigo (AAA-0000) ou Mercosul (AAA0A00)
- O local da infração geralmente inclui rua, número e bairro
- O motivo da infração pode ser uma descrição longa

Regras importantes:
1. Extraia apenas os dados que estão visíveis na imagem
2. Se algum dado não estiver visível, não inclua no JSON
3. Use formato JSON válido
4. Para datas, use formato YYYY-MM-DD
5. Para horários, use formato HH:MM
6. Para valores, use apenas números (ex: 195.23)
7. Para placa, use formato AAA-0000 (antigo) ou AAA0A00 (Mercosul)
8. Para CPF, use formato XXX.XXX.XXX-XX
9. Para nomes, use apenas letras, espaços e acentos

Retorne apenas o JSON, sem texto adicional.";
    }

    /**
     * Prompt genérico para outros tipos de documento
     */
    private function buildGenericPrompt(): string
    {
        return "Analise esta imagem e extraia todos os dados relevantes em formato JSON. Inclua qualquer informação que possa ser útil, como:

- Números de documentos
- Datas
- Nomes
- Valores
- Localizações
- Códigos ou identificadores

Retorne apenas o JSON, sem texto adicional.";
    }

    /**
     * Processa o texto extraído baseado no tipo de documento
     */
    private function processExtractedText(string $extractedText, string $documentType): array
    {
        try {
            // Limpar backticks e markdown do texto
            $cleanText = $this->cleanJsonText($extractedText);
            
            // Tentar decodificar como JSON
            $data = json_decode($cleanText, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('⚠️ Texto extraído não é JSON válido, tentando extrair dados manualmente', [
                    'texto_original' => $extractedText,
                    'texto_limpo' => $cleanText,
                    'erro_json' => json_last_error_msg()
                ]);
                
                // Fallback: extrair dados usando regex
                $data = $this->extractDataWithRegex($extractedText, $documentType);
            }

            // Validar e limpar os dados
            return $this->validateAndCleanData($data, $documentType);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao processar texto extraído', [
                'erro' => $e->getMessage(),
                'texto' => $extractedText
            ]);

            return $this->getFallbackData($documentType);
        }
    }

    /**
     * Limpa texto JSON removendo backticks e markdown
     */
    private function cleanJsonText(string $text): string
    {
        // Remover backticks e markdown
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*$/', '', $text);
        $text = preg_replace('/^```\s*/', '', $text);
        
        // Remover espaços em branco no início e fim
        $text = trim($text);
        
        Log::info('🧹 Texto JSON limpo', [
            'texto_limpo' => $text
        ]);
        
        return $text;
    }

    /**
     * Extrai dados usando regex quando o JSON não é válido
     */
    private function extractDataWithRegex(string $text, string $documentType): array
    {
        $data = [];

        if ($documentType === 'cnh') {
            // Extrair CPF
            if (preg_match('/\d{3}\.\d{3}\.\d{3}-\d{2}/', $text, $matches)) {
                $data['cpf'] = $matches[0];
            }

            // Extrair nome (assumindo que está em maiúsculas)
            if (preg_match('/[A-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŸÞßÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŸÞß\s]{10,}/', $text, $matches)) {
                $data['name'] = trim($matches[0]);
            }

            // Extrair número da CNH (Nº REGISTRO)
            if (preg_match('/Nº\s*REGISTRO[:\s]*(\d{9,11})/i', $text, $matches)) {
                $data['driver_license'] = $matches[1];
            } elseif (preg_match('/REGISTRO[:\s]*(\d{9,11})/i', $text, $matches)) {
                $data['driver_license'] = $matches[1];
            } elseif (preg_match('/\d{11}/', $text, $matches)) {
                // Fallback: procurar por qualquer número de 11 dígitos
                $data['driver_license'] = $matches[0];
            }

        } elseif ($documentType === 'notification') {
            // Extrair número da notificação
            if (preg_match('/Número\s+(?:do\s+)?(?:Auto|Notificação)[:\s]*(\d{10,15})/i', $text, $matches)) {
                $data['citation_number'] = $matches[1];
            } elseif (preg_match('/Auto\s+(?:de\s+)?Infração[:\s]*(\d{10,15})/i', $text, $matches)) {
                $data['citation_number'] = $matches[1];
            } elseif (preg_match('/Notificação[:\s]*(\d{10,15})/i', $text, $matches)) {
                $data['citation_number'] = $matches[1];
            }

            // Extrair valor da multa
            if (preg_match('/R\$\s*([0-9,\.]+)/', $text, $matches)) {
                $data['amount'] = str_replace(',', '.', $matches[1]);
            } elseif (preg_match('/Valor[:\s]*R?\$?\s*([0-9,\.]+)/i', $text, $matches)) {
                $data['amount'] = str_replace(',', '.', $matches[1]);
            } elseif (preg_match('/Multa[:\s]*R?\$?\s*([0-9,\.]+)/i', $text, $matches)) {
                $data['amount'] = str_replace(',', '.', $matches[1]);
            }

            // Extrair placa
            if (preg_match('/Placa[:\s]*([A-Z]{3}[-]?[0-9A-Z]{4})/i', $text, $matches)) {
                $data['plate'] = $matches[1];
            } elseif (preg_match('/[A-Z]{3}[-]?[0-9A-Z]{4}/', $text, $matches)) {
                $data['plate'] = $matches[0];
            }

            // Extrair data
            if (preg_match('/Data[:\s]*(\d{1,2})\/(\d{1,2})\/(\d{4})/i', $text, $matches)) {
                $data['date'] = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            } elseif (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $text, $matches)) {
                $data['date'] = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }

            // Extrair horário
            if (preg_match('/Hora[:\s]*(\d{1,2}):(\d{2})/i', $text, $matches)) {
                $data['time'] = sprintf('%02d:%02d', $matches[1], $matches[2]);
            } elseif (preg_match('/Horário[:\s]*(\d{1,2}):(\d{2})/i', $text, $matches)) {
                $data['time'] = sprintf('%02d:%02d', $matches[1], $matches[2]);
            } elseif (preg_match('/(\d{1,2}):(\d{2})/', $text, $matches)) {
                $data['time'] = sprintf('%02d:%02d', $matches[1], $matches[2]);
            }

            // Extrair local
            if (preg_match('/Local[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['location'] = trim($matches[1]);
            } elseif (preg_match('/Endereço[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['location'] = trim($matches[1]);
            } elseif (preg_match('/Via[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['location'] = trim($matches[1]);
            }

            // Extrair motivo da infração
            if (preg_match('/Infração[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['reason'] = trim($matches[1]);
            } elseif (preg_match('/Motivo[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['reason'] = trim($matches[1]);
            } elseif (preg_match('/Descrição[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['reason'] = trim($matches[1]);
            }

            // Extrair pontos
            if (preg_match('/Pontos[:\s]*(\d+)/i', $text, $matches)) {
                $data['points'] = $matches[1];
            } elseif (preg_match('/Pontuação[:\s]*(\d+)/i', $text, $matches)) {
                $data['points'] = $matches[1];
            }

            // Extrair artigo
            if (preg_match('/Artigo[:\s]*(\d+)/i', $text, $matches)) {
                $data['article'] = $matches[1];
            } elseif (preg_match('/Art\.\s*(\d+)/i', $text, $matches)) {
                $data['article'] = $matches[1];
            }

            // Extrair código da infração
            if (preg_match('/Código[:\s]*(\d+)/i', $text, $matches)) {
                $data['infraction_code'] = $matches[1];
            }

            // Extrair nome do condutor
            if (preg_match('/Condutor[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['driver_name'] = trim($matches[1]);
            } elseif (preg_match('/Motorista[:\s]*(.+?)(?:\n|$)/i', $text, $matches)) {
                $data['driver_name'] = trim($matches[1]);
            }

            // Extrair CPF do condutor
            if (preg_match('/CPF[:\s]*(\d{3}\.\d{3}\.\d{3}-\d{2})/i', $text, $matches)) {
                $data['driver_cpf'] = $matches[1];
            }

            // Extrair CNH do condutor
            if (preg_match('/CNH[:\s]*(\d{9,11})/i', $text, $matches)) {
                $data['driver_cnh'] = $matches[1];
            } elseif (preg_match('/Habilitação[:\s]*(\d{9,11})/i', $text, $matches)) {
                $data['driver_cnh'] = $matches[1];
            }
        }

        return $data;
    }

    /**
     * Valida e limpa os dados extraídos
     */
    private function validateAndCleanData(array $data, string $documentType): array
    {
        $cleanData = [];

        if ($documentType === 'cnh') {
            // Validar CPF
            if (isset($data['cpf']) && $this->isValidCpf($data['cpf'])) {
                $cleanData['cpf'] = $data['cpf'];
            }

            // Validar nome
            if (isset($data['name']) && strlen($data['name']) > 3) {
                $cleanData['name'] = trim($data['name']);
            }

            // Validar número da CNH
            if (isset($data['driver_license'])) {
                // Remover caracteres não numéricos
                $cnhNumber = preg_replace('/[^0-9]/', '', $data['driver_license']);
                
                // Verificar se tem pelo menos 9 dígitos (CNH brasileira)
                if (strlen($cnhNumber) >= 9) {
                    $cleanData['driver_license'] = $cnhNumber;
                } else {
                    Log::warning('⚠️ Número da CNH inválido', [
                        'numero_original' => $data['driver_license'],
                        'numero_limpo' => $cnhNumber,
                        'tamanho' => strlen($cnhNumber)
                    ]);
                }
            }

            // Validar data de nascimento
            if (isset($data['birth_date']) && $this->isValidDate($data['birth_date'])) {
                $cleanData['birth_date'] = $data['birth_date'];
            }

        } elseif ($documentType === 'notification') {
            // Validar valor
            if (isset($data['amount']) && is_numeric($data['amount'])) {
                $cleanData['amount'] = number_format((float)$data['amount'], 2, '.', '');
            }

            // Validar placa
            if (isset($data['plate']) && $this->isValidPlate($data['plate'])) {
                $cleanData['plate'] = strtoupper($data['plate']);
            }

            // Validar data
            if (isset($data['date']) && $this->isValidDate($data['date'])) {
                $cleanData['date'] = $data['date'];
            }

            // Validar horário
            if (isset($data['time']) && $this->isValidTime($data['time'])) {
                $cleanData['time'] = $data['time'];
            }

            // Validar local
            if (isset($data['location']) && strlen($data['location']) > 5) {
                $cleanData['location'] = trim($data['location']);
            }

            // Validar motivo
            if (isset($data['reason']) && strlen($data['reason']) > 10) {
                $cleanData['reason'] = trim($data['reason']);
            }

            // Validar número da notificação
            if (isset($data['citation_number']) && strlen($data['citation_number']) > 5) {
                $cleanData['citation_number'] = $data['citation_number'];
            }

            // Validar pontos
            if (isset($data['points']) && is_numeric($data['points']) && $data['points'] >= 0) {
                $cleanData['points'] = (int)$data['points'];
            }

            // Validar artigo
            if (isset($data['article']) && is_numeric($data['article'])) {
                $cleanData['article'] = (int)$data['article'];
            }

            // Validar código da infração
            if (isset($data['infraction_code']) && is_numeric($data['infraction_code'])) {
                $cleanData['infraction_code'] = (int)$data['infraction_code'];
            }

            // Validar nome do condutor
            if (isset($data['driver_name']) && strlen($data['driver_name']) > 3) {
                $cleanData['driver_name'] = trim($data['driver_name']);
            }

            // Validar CPF do condutor
            if (isset($data['driver_cpf']) && $this->isValidCpf($data['driver_cpf'])) {
                $cleanData['driver_cpf'] = $data['driver_cpf'];
            }

            // Validar CNH do condutor
            if (isset($data['driver_cnh'])) {
                $cnhNumber = preg_replace('/[^0-9]/', '', $data['driver_cnh']);
                if (strlen($cnhNumber) >= 9) {
                    $cleanData['driver_cnh'] = $cnhNumber;
                }
            }

            // Validar modelo do veículo
            if (isset($data['vehicle_model']) && strlen($data['vehicle_model']) > 2) {
                $cleanData['vehicle_model'] = trim($data['vehicle_model']);
            }

            // Validar cor do veículo
            if (isset($data['vehicle_color']) && strlen($data['vehicle_color']) > 2) {
                $cleanData['vehicle_color'] = trim($data['vehicle_color']);
            }

            // Validar cidade
            if (isset($data['city']) && strlen($data['city']) > 2) {
                $cleanData['city'] = trim($data['city']);
            }

            // Validar estado
            if (isset($data['state']) && strlen($data['state']) == 2) {
                $cleanData['state'] = strtoupper(trim($data['state']));
            }
        }

        return $cleanData;
    }

    /**
     * Valida se é um CPF válido
     */
    private function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return strlen($cpf) === 11;
    }

    /**
     * Valida se é uma data válida
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Valida se é um horário válido
     */
    private function isValidTime(string $time): bool
    {
        $t = \DateTime::createFromFormat('H:i', $time);
        return $t && $t->format('H:i') === $time;
    }

    /**
     * Valida se é uma placa válida
     */
    private function isValidPlate(string $plate): bool
    {
        $plate = strtoupper($plate);
        return preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$|^[A-Z]{3}[0-9]{4}$/', $plate);
    }

    /**
     * Retorna dados de fallback quando a extração falha
     */
    private function getFallbackData(string $documentType): array
    {
        if ($documentType === 'cnh') {
            return [
                'cnh' => [
                    'name' => 'Dados não extraídos',
                    'cpf' => '000.000.000-00',
                    'driver_license' => '00000000000',
                    'birth_date' => '1900-01-01'
                ]
            ];
        } elseif ($documentType === 'notification') {
            return [
                'notification' => [
                    'citation_number' => 'Não identificado',
                    'date' => date('Y-m-d'),
                    'time' => '00:00',
                    'amount' => '0.00',
                    'location' => 'Local não identificado',
                    'reason' => 'Motivo não identificado',
                    'plate' => 'AAA-0000'
                ]
            ];
        }

        return [];
    }

    /**
     * Obtém o MIME type do arquivo
     */
    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];

        return $mimeTypes[$extension] ?? 'image/jpeg';
    }
} 