<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageExtractionService
{
    private $geminiApiKey;
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key');
    }

    /**
     * Extrai dados de uma imagem ou PDF usando Gemini Vision API
     */
    public function extractDataFromImage(string $filePath, string $documentType): array
    {
        $tempImage = null;

        try {
            Log::info('🔍 Iniciando extração de dados', [
                'arquivo' => basename($filePath),
                'tipo' => $documentType
            ]);

            if (!file_exists($filePath)) {
                throw new \Exception('Arquivo não encontrado: ' . $filePath);
            }

            $mimeType = $this->getMimeType($filePath);
            $imagePathToProcess = $filePath;

            // Se for PDF, converte a primeira página para imagem
            if ($mimeType === 'application/pdf') {
                Log::info('📄 Arquivo é PDF, convertendo para imagem...');
                $tempImage = $this->convertPdfFirstPageToPng($filePath);
                if (!$tempImage) {
                    throw new \Exception('Falha ao converter PDF para imagem');
                }
                $imagePathToProcess = $tempImage;
                $mimeType = 'image/png';
            }

            // Ler e codificar a imagem em base64
            $imageData = file_get_contents($imagePathToProcess);
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
                                    'mime_type' => $mimeType,
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
                    'responseMimeType' => 'application/json'
                ]
            ];

            // Fazer requisição para a API do Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, $requestData);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->body();
                
                Log::error('❌ Erro na API do Gemini (Extração)', [
                    'status' => $status,
                    'response' => $body
                ]);
                
                // Tentar fallback para modelo alternativo se for erro 404 ou 429
                if ($status === 404 || $status === 429) {
                    Log::warning('⚠️ Tentando fallback para modelo alternativo do Gemini...');
                    
                    // Tenta modelo flash experimental como fallback
                    $fallbackUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
                    
                    $fallbackResponse = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($fallbackUrl . '?key=' . $this->geminiApiKey, $requestData);
                    
                    if (!$fallbackResponse->successful()) {
                        Log::error('❌ Fallback também falhou na extração', [
                            'status' => $fallbackResponse->status(),
                            'response' => $fallbackResponse->body()
                        ]);
                        throw new \Exception('Erro na API do Gemini: ' . $fallbackResponse->status());
                    }
                    
                    Log::info('✅ Fallback bem-sucedido para extração de imagem');
                    $responseData = $fallbackResponse->json();
                } else {
                    throw new \Exception('Erro na API do Gemini: ' . $status);
                }
            } else {
                $responseData = $response->json();
            }
            
            // Extrair o texto da resposta
            $extractedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('📄 Texto extraído da IA', [
                'tamanho' => strlen($extractedText),
                'tipo' => $documentType
            ]);

            // Processar o texto extraído
            $processedData = $this->processExtractedText($extractedText, $documentType);

            Log::info('✅ Dados processados com sucesso', array_keys($processedData));

            return $processedData;

        } catch (\Exception $e) {
            Log::error('❌ Erro na extração de dados', [
                'erro' => $e->getMessage(),
                'arquivo' => $filePath,
                'tipo' => $documentType
            ]);
            
            // Propagar o erro para o controller lidar (não retornar fake data aqui)
            throw $e;
        } finally {
            // Limpar imagem temporária se foi criada
            if ($tempImage && file_exists($tempImage)) {
                @unlink($tempImage);
            }
        }
    }

    /**
     * Converte a primeira página do PDF em PNG
     */
    private function convertPdfFirstPageToPng(string $pdfPath): ?string
    {
        try {
            $outputBase = storage_path('app/temp/pdfimg_' . uniqid());
            $outputDir = dirname($outputBase);
            
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0775, true);
            }

            $pdftoppm = '/usr/bin/pdftoppm';
            // -f 1 -l 1: primeira página apenas
            // -r 300: resolução 300 DPI (bom equilíbrio qualidade/tamanho)
            $cmd = sprintf('%s -f 1 -l 1 -r 300 -png %s %s', 
                escapeshellarg($pdftoppm), 
                escapeshellarg($pdfPath), 
                escapeshellarg($outputBase)
            );
            
            exec($cmd, $out, $ret);
            
            // pdftoppm adiciona sufixo -1.png ou -01.png dependendo da versão/config
            // Vamos procurar o arquivo gerado
            $files = glob($outputBase . '-*.png');
            
            if ($ret === 0 && !empty($files)) {
                return $files[0];
            }
            
            Log::warning('⚠️ Falha ao converter PDF em PNG', ['ret' => $ret, 'cmd' => $cmd]);
            return null;
        } catch (\Throwable $e) {
            Log::warning('⚠️ Erro na conversão do PDF', ['erro' => $e->getMessage()]);
            return null;
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
            case 'vehicle':
                return $this->buildVehiclePrompt();
            default:
                return $this->buildGenericPrompt();
        }
    }

    private function buildCnhPrompt(): string
    {
        return "Analise esta imagem da CNH (Carteira Nacional de Habilitação) brasileira.
Extraia os dados e retorne APENAS um JSON com a seguinte estrutura:
{
    \"cnh\": {
        \"name\": \"Nome completo\",
        \"cpf\": \"000.000.000-00\",
        \"driver_license\": \"Número do Registro (apenas números)\",
        \"birth_date\": \"YYYY-MM-DD\",
        \"category\": \"Categoria (ex: AB)\",
        \"expiry_date\": \"YYYY-MM-DD\"
    }
}
Se algum campo não estiver visível, use null. Não invente dados.";
    }

    private function buildNotificationPrompt(): string
    {
        return "Analise esta imagem de notificação de multa de trânsito brasileira.
Extraia os dados e retorne APENAS um JSON com a seguinte estrutura:
{
    \"notification\": {
        \"citation_number\": \"Número do Auto de Infração\",
        \"date\": \"YYYY-MM-DD (Data da infração)\",
        \"time\": \"HH:MM\",
        \"amount\": \"Valor da multa (ex: 195.23)\",
        \"location\": \"Local da infração completo\",
        \"reason\": \"Descrição/Motivo da infração\",
        \"plate\": \"Placa do veículo\",
        \"points\": \"Pontos na carteira (número)\",
        \"infraction_code\": \"Código da infração (ex: 74550)\"
    }
}
Se algum campo não estiver visível, use null. Não invente dados.";
    }

    private function buildVehiclePrompt(): string
    {
        return "Analise esta imagem do documento do veículo (CRLV/CRLV-e) brasileiro.
Extraia os dados e retorne APENAS um JSON com a seguinte estrutura:
{
    \"vehicle\": {
        \"plate\": \"Placa do veículo\",
        \"renavam\": \"Número do RENAVAM\",
        \"model\": \"Marca/Modelo\",
        \"color\": \"Cor\",
        \"year\": \"Ano Fabricação/Modelo (ex: 2020)\",
        \"owner_name\": \"Nome do proprietário\",
        \"owner_cpf\": \"CPF/CNPJ do proprietário\",
        \"uf\": \"UF do licenciamento\",
        \"chassis\": \"Chassi\"
    }
}
Se algum campo não estiver visível, use null. Não invente dados.";
    }

    private function buildGenericPrompt(): string
    {
        return "Analise esta imagem e extraia todos os dados relevantes em formato JSON. Retorne apenas o JSON.";
    }

    /**
     * Processa o texto extraído
     */
    private function processExtractedText(string $extractedText, string $documentType): array
    {
        // Limpar marcadores de código markdown se existirem
        $cleanText = preg_replace('/^```json\s*|\s*```$/', '', trim($extractedText));
        
        $data = json_decode($cleanText, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('❌ Erro ao decodificar JSON da IA', ['erro' => json_last_error_msg(), 'texto' => $cleanText]);
            // Tentar recuperar JSON parcial ou mal formatado se necessário
            // Por enquanto, retorna array vazio ou lança exceção
            return [];
        }

        return $data ?? [];
    }

    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'pdf' => 'application/pdf'
        ];
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}