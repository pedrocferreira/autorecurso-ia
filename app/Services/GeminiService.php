<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Extrai dados estruturados de uma imagem de multa usando a API Gemini.
     */
    public function analyzeTicketImage(string $filePath): array
    {
        try {
            Log::info('Iniciando análise de imagem OCR via Gemini', ['file' => $filePath]);
            
            $this->assertFileExists($filePath);

            $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
            $fileSize = filesize($filePath);
            Log::info('Arquivo validado', ['mime_type' => $mimeType, 'size' => $fileSize]);

            $imageContent = file_get_contents($filePath);
            if ($imageContent === false) {
                throw new \RuntimeException('Não foi possível ler o arquivo de imagem');
            }

            $base64Image = base64_encode($imageContent);
            $base64Size = strlen($base64Image);
            Log::info('Imagem codificada em base64', ['base64_size' => $base64Size]);
            
            // A API Gemini tem limite de ~4MB por imagem (aproximadamente 5.3MB em base64)
            if ($base64Size > 5500000) {
                Log::warning('Imagem muito grande para processamento', ['size' => $base64Size]);
                throw new \RuntimeException('A imagem é muito grande. Reduza o tamanho e tente novamente. (Máximo: ~4MB)');
            }

            $prompt = <<<PROMPT
Você receberá a foto de uma infração de trânsito brasileira. Extraia os dados relevantes e responda EXCLUSIVAMENTE em JSON no formato abaixo. Utilize null quando o campo não estiver claro.
{
  "name": string|null,
  "cpf": string|null,
  "driver_license": string|null,
  "driver_license_category": string|null,
  "address": string|null,
  "phone": string|null,
  "email": string|null,
  "plate": string|null,
  "vehicle_model": string|null,
  "vehicle_year": integer|null,
  "vehicle_color": string|null,
  "vehicle_chassi": string|null,
  "vehicle_renavam": string|null,
  "date": string|null,           // formato ISO 8601: YYYY-MM-DD
  "time": string|null,           // HH:MM se disponível
  "location": string|null,
  "amount": number|null,
  "points": integer|null,
  "reason": string|null,
  "infraction_code": string|null,
  "infraction_article": string|null,
  "citation_number": string|null,
  "issuer": string|null          // órgão autuador
}
PROMPT;

            $parts = [
                ['text' => $prompt],
                [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $base64Image,
                    ],
                ],
            ];

            Log::info('Enviando requisição estruturada para Gemini API');
            return $this->requestStructuredTicketData($parts, 'image');
        } catch (\Throwable $e) {
            Log::error('Erro em analyzeTicketImage: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $filePath,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Estrutura dados a partir de texto OCR usando a API Gemini.
     */
    public function extractTicketDataFromText(string $text): array
    {
        $prompt = <<<PROMPT
O texto abaixo foi extraído de uma multa de trânsito brasileira. Interprete-o e devolva EXCLUSIVAMENTE um JSON com o mesmo esquema especificado anteriormente. Se alguma informação não estiver clara, utilize null. Texto:
PROMPT;

        $parts = [
            ['text' => $prompt . "\n---\n" . $text . "\n---"],
        ];

        return $this->requestStructuredTicketData($parts, 'text');
    }

    /**
     * Gera um recurso para uma multa usando a API Gemini.
     */
    public function generateAppealText(Ticket $ticket, array $additionalData = []): string
    {
        try {
            Log::info('Iniciando geração de recurso via Gemini para a multa: ' . $ticket->id);

            $prompt = $this->createPrompt($ticket, $additionalData);

            $apiKey = config('services.gemini.api_key');
            if (empty($apiKey)) {
                Log::error('Chave da API Gemini não configurada');
                throw new \RuntimeException('Chave da API Gemini não configurada. Defina GEMINI_API_KEY no .env.');
            }

            $model = config('services.gemini.model', 'gemini-2.5-flash');
            // Remove sufixo -latest se presente, pois pode causar 404
            $model = str_replace('-latest', '', $model);
            
            $endpoint = sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                $model,
                $apiKey
            );
            
            Log::debug('Usando endpoint Gemini', ['model' => $model, 'endpoint' => $endpoint]);

            Log::info('Enviando requisição para Gemini API...');

            $response = Http::timeout(40)
                ->acceptJson()
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                    ],
                ]);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->body();
                $errorData = json_decode($body, true);
                
                Log::error('Falha ao chamar Gemini API', [
                    'status' => $status,
                    'body' => $body,
                ]);
                
                $errorMessage = 'Falha ao chamar a API Gemini';
                if ($status === 403) {
                    $apiMessage = $errorData['error']['message'] ?? '';
                    if (stripos($apiMessage, 'leaked') !== false || stripos($apiMessage, 'vazada') !== false) {
                        $errorMessage = 'A chave de API foi reportada como vazada e está bloqueada. Por favor, gere uma nova chave de API no Google AI Studio (https://aistudio.google.com/apikey) e atualize o arquivo .env.';
                    } else {
                        $errorMessage = 'Acesso negado pela API Gemini. Verifique a chave de API.';
                    }
                }
                
                throw new \RuntimeException($errorMessage);
            }

            $data = $response->json();

            $text = data_get($data, 'candidates.0.content.parts.0.text');
            if (empty($text)) {
                Log::error('Resposta da Gemini API não contém conteúdo esperado', ['response' => $data]);
                throw new \RuntimeException('Resposta inesperada da API Gemini');
            }

            Log::info('Resposta recebida da Gemini API com sucesso');

            return $text;
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar recurso com Gemini: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            Log::info('Retornando texto de exemplo devido à falha na API Gemini');
            return $this->getExampleText($ticket, $additionalData);
        }
    }

    /**
     * Executa a chamada estruturada para a API Gemini e interpreta o JSON retornado.
     */
    private function requestStructuredTicketData(array $parts, string $source): array
    {
        $maxRetries = 3;
        $retryDelay = 2; // segundos
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $apiKey = config('services.gemini.api_key');
                if (empty($apiKey)) {
                    throw new \RuntimeException('Chave da API Gemini não configurada. Defina GEMINI_API_KEY no .env.');
                }

                $model = config('services.gemini.model', 'gemini-2.5-flash');
                // Remove sufixo -latest se presente, pois pode causar 404
                $model = str_replace('-latest', '', $model);
                
                $endpoint = sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    $model,
                    $apiKey
                );
                
                if ($attempt > 1) {
                    Log::info("Tentativa {$attempt} de {$maxRetries} para chamar Gemini API", ['endpoint' => $endpoint]);
                } else {
                    Log::debug('Usando endpoint Gemini', ['model' => $model, 'endpoint' => $endpoint]);
                }

                $response = Http::timeout(60)
                    ->acceptJson()
                    ->post($endpoint, [
                        'contents' => [
                            [
                                'parts' => $parts,
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,
                            'maxOutputTokens' => 1024,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if ($response->successful()) {
                    // Sucesso! Processa a resposta
                    $payload = $response->json();
                    Log::debug('Resposta da Gemini API recebida', ['has_candidates' => isset($payload['candidates'])]);
                    
                    $rawJson = data_get($payload, 'candidates.0.content.parts.0.text');
                    if (empty($rawJson)) {
                        Log::error('Resposta da Gemini API sem conteúdo estruturado', [
                            'response' => $payload,
                            'candidates_count' => count($payload['candidates'] ?? []),
                        ]);
                        throw new \RuntimeException('A API Gemini não retornou dados estruturados. Tente com uma imagem mais nítida.');
                    }

                    $decoded = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);
                    if (!is_array($decoded)) {
                        throw new \RuntimeException('JSON retornado pela Gemini é inválido');
                    }

                    $fields = $decoded['fields'] ?? $decoded;

                    return [
                        'fields' => $fields,
                        'raw_json' => $rawJson,
                        'source' => $source,
                    ];
                }

                // Se chegou aqui, houve erro na resposta
                $status = $response->status();
                $body = $response->body();
                $errorData = json_decode($body, true);
                
                // Erros temporários (503, 502, 504) podem ser tentados novamente
                $isRetryable = in_array($status, [503, 502, 504, 429]);
                
                if ($isRetryable && $attempt < $maxRetries) {
                    $delay = $retryDelay * $attempt; // Backoff exponencial: 2s, 4s, 6s
                    Log::warning("Erro temporário na API Gemini (Status: {$status}). Tentando novamente em {$delay}s...", [
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries,
                        'body' => $body,
                    ]);
                    sleep($delay);
                    continue; // Tenta novamente
                }
                
                // Erro não recuperável ou esgotaram as tentativas
                Log::error('Falha ao chamar Gemini API para estruturação', [
                    'status' => $status,
                    'body' => $body,
                    'endpoint' => $endpoint,
                    'attempt' => $attempt,
                ]);
                
                $errorMessage = 'Falha ao chamar a API Gemini';
                if ($status === 400) {
                    $errorMessage = 'Requisição inválida para a API Gemini. Verifique a chave de API.';
                } elseif ($status === 403) {
                    // Verifica se a chave foi reportada como vazada
                    $apiMessage = $errorData['error']['message'] ?? '';
                    if (stripos($apiMessage, 'leaked') !== false || stripos($apiMessage, 'vazada') !== false) {
                        $errorMessage = 'A chave de API foi reportada como vazada e está bloqueada. Por favor, gere uma nova chave de API no Google AI Studio (https://aistudio.google.com/apikey) e atualize o arquivo .env.';
                    } elseif (stripos($apiMessage, 'permission') !== false || stripos($apiMessage, 'permissão') !== false) {
                        $errorMessage = 'Acesso negado pela API Gemini. Verifique se a chave de API está correta e tem as permissões necessárias.';
                    } else {
                        $errorMessage = 'Acesso negado pela API Gemini. Verifique a chave de API. Mensagem: ' . $apiMessage;
                    }
                } elseif ($status === 429) {
                    $errorMessage = 'Muitas requisições. Aguarde alguns instantes e tente novamente.';
                } elseif ($status === 503 || $status === 502 || $status === 504) {
                    $errorMessage = 'O serviço da API Gemini está temporariamente indisponível após várias tentativas. Por favor, tente novamente em alguns instantes.';
                }
                
                throw new \RuntimeException($errorMessage . ' (Status: ' . $status . ')');
            } catch (\RuntimeException $e) {
                // Se não for um erro retryable ou já esgotaram as tentativas, lança a exceção
                if (!isset($isRetryable) || !$isRetryable || $attempt >= $maxRetries) {
                    throw $e;
                }
                // Caso contrário, continua o loop para tentar novamente
            }
        }
        
        // Não deveria chegar aqui, mas por segurança:
        throw new \RuntimeException('Falha ao chamar a API Gemini após ' . $maxRetries . ' tentativas.');
    }

    /**
     * Cria o prompt utilizado pela IA com base nos dados da multa.
     */
    private function createPrompt(Ticket $ticket, array $additionalData = []): string
    {
        $user = $ticket->user;

        $name = $additionalData['name'] ?? $user->name;
        $cpf = $additionalData['cpf'] ?? '(informação não disponível)';
        $address = $additionalData['address'] ?? '(endereço não disponível)';
        $customDetails = $additionalData['custom_details'] ?? '';

        $infractionType = $ticket->infractionType;
        $lawArticle = $infractionType ? $infractionType->law_article : '';

        $prompt = "Por favor, gere um recurso formal contra uma multa de trânsito com base nas seguintes informações:

        DADOS DO CONDUTOR:
        - Nome: {$name}
        - CPF: {$cpf}
        - CNH: {$ticket->driver_license}
        - Endereço: {$address}

        DADOS DO VEÍCULO:
        - Placa: {$ticket->plate}
        - Modelo: {$ticket->vehicle_model}
        - Ano: {$ticket->vehicle_year}

        DADOS DA INFRAÇÃO:
        - Data da infração: {$ticket->date->format('d/m/Y')}
        - Local: {$ticket->location}
        - Motivo da autuação: {$ticket->reason}
        - Número da autuação: {$ticket->citation_number}
        - Valor: R$ {$ticket->amount}";

        if ($lawArticle) {
            $prompt .= "\n        - Artigo da lei: {$lawArticle}";
        }

        if ($customDetails) {
            $prompt .= "\n\n        DETALHES ADICIONAIS FORNECIDOS PELO CONDUTOR:
        {$customDetails}";
        }

        $prompt .= "\n\n        O recurso deve:
        1. Ser escrito em formato formal, como uma petição administrativa;
        2. Conter argumentos técnicos e jurídicos apropriados para contestar a infração;
        3. Citar artigos relevantes do Código de Trânsito Brasileiro;
        4. Incluir pedido de cancelamento da multa;
        5. Solicitar, alternativamente, caso o cancelamento não seja possível, a conversão da penalidade em advertência;
        6. Apresentar saudação formal e local para assinatura.

        O texto deve estar pronto para impressão no formato de um documento oficial.";

        return $prompt;
    }

    /**
     * Retorna um texto de exemplo para ambientes de teste ou fallback.
     */
    private function getExampleText(Ticket $ticket, array $additionalData = []): string
    {
        $user = $ticket->user;
        $name = $additionalData['name'] ?? $user->name;
        $cpf = $additionalData['cpf'] ?? '(informação não disponível)';
        $address = $additionalData['address'] ?? '(endereço não disponível)';

        $mes = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
        ][date('n') - 1];

        return "À AUTORIDADE DE TRÂNSITO COMPETENTE

ASSUNTO: Recurso contra autuação de trânsito - Auto de Infração nº {$ticket->citation_number}

RECORRENTE: {$name}, portador(a) do CPF nº {$cpf}, residente e domiciliado(a) em {$address}, condutor(a) do veículo de placa {$ticket->plate}, modelo {$ticket->vehicle_model}, ano {$ticket->vehicle_year}.

Senhor(a) Autoridade de Trânsito,

Venho, respeitosamente, à presença de Vossa Senhoria, com fundamento no art. 286 da Lei nº 9.503/97 (Código de Trânsito Brasileiro), apresentar RECURSO contra a penalidade aplicada conforme Auto de Infração nº {$ticket->citation_number}, pelos fatos e fundamentos a seguir expostos:

DOS FATOS:

Fui autuado(a) em {$ticket->date->format('d/m/Y')}, conforme auto de infração mencionado, pelo suposto cometimento da seguinte infração: {$ticket->reason}, no local {$ticket->location}, com aplicação de multa no valor de R$ {$ticket->amount}.

DO DIREITO:

1. Da ausência de elementos essenciais no auto de infração:
O auto de infração em questão não apresenta todos os elementos exigidos pelo art. 280 do CTB, especialmente no que se refere à tipificação clara e objetiva da conduta supostamente praticada, o que compromete sua validade.

2. Da violação aos princípios constitucionais do contraditório e ampla defesa:
A aplicação imediata de penalidade sem a oportunidade de esclarecimentos prévios viola os princípios constitucionais do contraditório e da ampla defesa, garantidos pelo art. 5º, LV, da Constituição Federal.

3. Da ausência de prova material da infração:
A autuação baseou-se apenas em observação visual do agente, sem qualquer prova material que comprove inequivocamente a prática da infração alegada, o que gera dúvida razoável sobre a ocorrência do fato.

4. Da inexistência de dolo ou culpa:
Ainda que tivesse ocorrido a infração, o que se admite apenas para argumentar, não houve dolo ou culpa de minha parte, elementos subjetivos essenciais para a caracterização da infração de trânsito.

DO PEDIDO:

Ante o exposto, REQUER:

a) O recebimento e processamento do presente recurso, com efeito suspensivo, nos termos do art. 285 do CTB;
b) No mérito, o provimento do recurso, com o consequente cancelamento da autuação e arquivamento do procedimento administrativo;
c) Alternativamente, caso não seja esse o entendimento, a conversão da penalidade de multa em advertência por escrito, nos termos do art. 267 do CTB, considerando ser o recorrente possuidor de boa conduta anterior.

Nestes termos,
Pede deferimento.

{$ticket->location}, " . date('d') . " de {$mes} de " . date('Y') . "

{$name}
CPF: {$cpf}
CNH: {$ticket->driver_license}";
    }
    private function assertFileExists(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('Arquivo não encontrado para análise OCR: %s', $filePath));
        }
    }
}