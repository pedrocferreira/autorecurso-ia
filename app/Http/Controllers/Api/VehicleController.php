<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function lookup(Request $request)
    {
        try {
            // Validação da entrada
            $request->validate([
                'placa' => 'required|string|min:7|max:8'
            ]);

            $placa = strtoupper(trim($request->input('placa')));
            
            // Validação básica do formato da placa brasileira
            if (!$this->isValidPlateFormat($placa)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de placa inválido. Use o formato AAA0000 ou AAA0A00'
                ], 422);
            }

            Log::info('Consultando placa na API Brasil (novo endpoint)', [
                'placa' => $placa,
                'ip' => $request->ip()
            ]);

            // Verificar se as credenciais estão configuradas
            $apiToken = env('VEHICLE_API_TOKEN');

            if (empty($apiToken)) {
                Log::error('Token da API Brasil não configurado', [
                    'vehicle_api_token_exists' => !empty($apiToken)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Serviço de consulta temporariamente indisponível'
                ], 503);
            }

            // Fazer a requisição para o novo endpoint da API Brasil
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiToken
                ])
                ->post('https://gateway.apibrasil.io/api/v2/vehicles/base/000/dados', [
                    'placa' => $placa,
                    'homolog' => false
                ]);

            // Log da resposta da API
            Log::info('Resposta da API Brasil (novo formato)', [
                'status' => $response->status(),
                'placa' => $placa,
                'response_size' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Verifica se a resposta contém erro
                if (isset($data['error']) && $data['error'] === true) {
                    Log::warning('API Brasil retornou erro', [
                        'placa' => $placa,
                        'message' => $data['message'] ?? 'Erro desconhecido'
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => $data['message'] ?? 'Placa não encontrada ou dados indisponíveis'
                    ], 404);
                }

                // Extrair dados do veículo da nova estrutura de resposta
                $vehicleData = $data['data'] ?? [];
                
                // Formatar e retornar os dados extraídos
                $formattedData = [
                    'modelo' => $this->formatString($vehicleData['MODELO'] ?? $vehicleData['modelo'] ?? null),
                    'marca' => $this->formatString($vehicleData['MARCA'] ?? $vehicleData['marca'] ?? null),
                    'ano' => $this->formatYear($vehicleData['ano'] ?? $vehicleData['ano_modelo'] ?? null),
                    'cor' => $this->formatString($vehicleData['cor'] ?? $vehicleData['cor_veiculo']['cor'] ?? null),
                    'municipio' => $this->formatString($vehicleData['municipio'] ?? null),
                    'uf' => strtoupper($vehicleData['uf'] ?? $vehicleData['uf_placa'] ?? ''),
                    'combustivel' => $this->formatString($vehicleData['combustivel'] ?? null),
                    'chassi' => $vehicleData['chassi'] ?? null,
                    'placa_formatada' => $vehicleData['placa'] ?? $placa
                ];

                Log::info('Dados do veículo encontrados (novo formato)', [
                    'placa' => $placa,
                    'dados' => $formattedData,
                    'api_message' => $data['message'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $formattedData,
                    'api_info' => [
                        'balance' => $data['balance'] ?? null,
                        'message' => $data['message'] ?? null,
                        'cost' => $this->extractCostFromMessage($data['message'] ?? '')
                    ]
                ]);
            }

            // Log de erro de resposta
            Log::error('API Brasil retornou erro HTTP', [
                'status' => $response->status(),
                'placa' => $placa,
                'response' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível consultar a placa no momento'
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar placa na API Brasil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'placa' => $request->input('placa', 'N/A')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => app()->environment('local') ? $e->getMessage() : 'Tente novamente mais tarde'
            ], 500);
        }
    }

    /**
     * Valida o formato da placa brasileira
     */
    private function isValidPlateFormat($placa)
    {
        // Formatos válidos:
        // AAA0000 (formato antigo)
        // AAA0A00 (formato Mercosul)
        return preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa) || 
               preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa);
    }

    /**
     * Formata strings removendo caracteres especiais
     */
    private function formatString($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(ucwords(strtolower($value)));
    }

    /**
     * Formata e valida ano
     */
    private function formatYear($value)
    {
        if (empty($value)) {
            return null;
        }
        
        $year = intval($value);
        
        // Valida se é um ano plausível
        if ($year < 1900 || $year > (date('Y') + 2)) {
            return null;
        }
        
        return $year;
    }

    /**
     * Extrai o custo da mensagem da API
     */
    private function extractCostFromMessage($message)
    {
        if (preg_match('/R\$\s*([\d,\.]+)/', $message, $matches)) {
            return $matches[1];
        }
        return null;
    }
} 