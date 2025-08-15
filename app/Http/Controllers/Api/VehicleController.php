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
            $request->validate([
                // Aceita AAA0000 e AAA0A00 (7 chars sem hífen)
                'placa' => ['required','string','max:8', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/']
            ]);

            $plate = $request->input('placa');
            
            Log::info('🔍 Consultando dados do veículo', [
                'placa' => $plate
            ]);

            // Validar formato da placa
            if (!$this->isValidPlateFormat($plate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de placa inválido. Use AAA0000 (antigo) ou AAA0A00 (Mercosul).'
                ], 400);
            }

            // Buscar dados na API Brasil
            $vehicleData = $this->getVehicleDataFromApiBrasil($plate);
            
            if (!$vehicleData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $vehicleData['message'] ?? 'Erro ao consultar dados do veículo'
                ], 500);
            }

            Log::info('✅ Dados do veículo retornados', [
                'placa' => $plate,
                'data' => $vehicleData['data']
            ]);

            return response()->json([
                'success' => true,
                'data' => $vehicleData['data']
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao consultar dados do veículo', [
                'placa' => $request->input('placa'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar dados do veículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function isValidPlateFormat($plate)
    {
        // Remove hífens e espaços
        $plate = strtoupper(preg_replace('/[^A-Z0-9]/', '', $plate));
        
        // Padrão antigo: AAA0000
        $oldPattern = '/^[A-Z]{3}[0-9]{4}$/';
        
        // Padrão Mercosul: AAA0A00 (4º char é dígito e 5º é letra)
        $mercosulPattern = '/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/';
        
        return (bool) (preg_match($oldPattern, $plate) || preg_match($mercosulPattern, $plate));
    }

    private function getVehicleDataFromApiBrasil($plate)
    {
        try {
            $plate = strtoupper(preg_replace('/[^A-Z0-9]/', '', $plate));
            $baseUrl = config('services.apibrasil.base_url');
            $token = config('services.apibrasil.token');

            if (!$token) {
                throw new \RuntimeException('Token da API Brasil não configurado (APIBRASIL_BEARER_TOKEN).');
            }

            $resolveIp = env('APIBRASIL_RESOLVE_IP');
            $verifySsl = filter_var(env('APIBRASIL_VERIFY_SSL', true), FILTER_VALIDATE_BOOL);

            $curlOpts = [\CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4];
            if (!empty($resolveIp)) {
                // Mapeia api.apibrasil.io -> IP estático (SNI preservado)
                $curlOpts[\CURLOPT_RESOLVE] = ["api.apibrasil.io:443:{$resolveIp}"];
            }

            $response = \Illuminate\Support\Facades\Http::withOptions([
                    'force_ip_resolve' => 'v4',
                    'verify' => $verifySsl,
                    'curl' => $curlOpts,
                ])
                ->withToken($token)
                ->timeout(12)
                ->retry(2, 500, function ($exception, $request) {
                    return true; // retry em 5xx/429 por padrão do Http::retry
                })
                ->get(rtrim($baseUrl, '/') . '/placa/dados', [
                    'placa' => $plate,
                ]);

            if ($response->successful()) {
                $json = $response->json();

                // Mapeamento básico dos campos esperados pela UI
                $data = [
                    'placa' => $json['placa'] ?? $plate,
                    'modelo' => $json['modelo'] ?? ($json['model'] ?? null),
                    'marca' => $json['marca'] ?? ($json['brand'] ?? null),
                    'ano' => $json['ano'] ?? ($json['year'] ?? null),
                    'cor' => $json['cor'] ?? ($json['color'] ?? null),
                    'chassi' => $json['chassi'] ?? ($json['chassis'] ?? null),
                    'renavam' => $json['renavam'] ?? null,
                ];

                return [ 'success' => true, 'data' => $data ];
            }

            if ($response->status() === 404) {
                return [ 'success' => false, 'message' => 'Placa não encontrada' ];
            }

            if ($response->status() === 401 || $response->status() === 403) {
                return [ 'success' => false, 'message' => 'Falha na autenticação com a API Brasil' ];
            }

            return [ 'success' => false, 'message' => 'Erro ao consultar dados do veículo', 'status' => $response->status() ];

        } catch (\Throwable $e) {
            Log::error('❌ Erro ao consultar API Brasil', [
                'error' => $e->getMessage(),
                'placa' => $plate ?? null,
            ]);
            return [ 'success' => false, 'message' => 'Erro na integração com a API Brasil' ];
        }
    }

    private function generateRealisticVehicleData($plate)
    {
        // Gerar dados realistas baseados na placa
        $brands = ['Toyota', 'Honda', 'Volkswagen', 'Fiat', 'Chevrolet', 'Ford', 'Hyundai', 'Renault', 'Nissan', 'BMW'];
        $models = [
            'Toyota' => ['Corolla', 'Camry', 'Hilux', 'SW4', 'Etios'],
            'Honda' => ['Civic', 'City', 'HR-V', 'CR-V', 'Fit'],
            'Volkswagen' => ['Gol', 'Voyage', 'Polo', 'Jetta', 'Tiguan'],
            'Fiat' => ['Palio', 'Siena', 'Uno', 'Doblo', 'Toro'],
            'Chevrolet' => ['Onix', 'Prisma', 'Cobalt', 'Tracker', 'Spin'],
            'Ford' => ['Ka', 'Fiesta', 'Focus', 'EcoSport', 'Ranger'],
            'Hyundai' => ['HB20', 'i30', 'Tucson', 'Santa Fe', 'Creta'],
            'Renault' => ['Sandero', 'Logan', 'Duster', 'Captur', 'Kwid'],
            'Nissan' => ['March', 'Versa', 'Sentra', 'Kicks', 'Frontier'],
            'BMW' => ['X1', 'X3', 'X5', '320i', '520i']
        ];
        $colors = ['Branco', 'Preto', 'Prata', 'Cinza', 'Vermelho', 'Azul', 'Verde', 'Amarelo'];
        
        // Usar a placa para gerar dados consistentes
        $hash = crc32($plate);
        $brandIndex = $hash % count($brands);
        $brand = $brands[$brandIndex];
        $modelList = $models[$brand];
        $modelIndex = ($hash >> 8) % count($modelList);
        $model = $modelList[$modelIndex];
        $colorIndex = ($hash >> 16) % count($colors);
        $color = $colors[$colorIndex];
        $year = 2015 + ($hash % 10); // Ano entre 2015 e 2024
        
        // Gerar chassi e RENAVAM baseados na placa
        $chassi = strtoupper(substr(md5($plate . 'chassi'), 0, 17));
        $renavam = strtoupper(substr(md5($plate . 'renavam'), 0, 11));
        
        return [
            'placa' => $plate,
            'modelo' => $model,
            'ano' => (string)$year,
            'cor' => $color,
            'marca' => $brand,
            'chassi' => $chassi,
            'renavam' => $renavam
        ];
    }
} 