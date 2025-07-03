<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    public function lookup(Request $request)
    {
        try {
            $placa = $request->input('placa');
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'DeviceToken' => env('APIBRASIL_DEVICE_TOKEN'),
                'Authorization' => 'Bearer ' . env('APIBRASIL_BEARER_TOKEN')
            ])->post('https://gateway.apibrasil.io/api/v2/vehicles/dados', [
                'placa' => $placa
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Retorna os dados formatados
                return response()->json([
                    'success' => true,
                    'data' => [
                        'modelo' => $data['modelo'] ?? null,
                        'marca' => $data['marca'] ?? null,
                        'ano' => $data['ano'] ?? null,
                        'cor' => $data['cor'] ?? null,
                        'municipio' => $data['municipio'] ?? null,
                        'uf' => $data['uf'] ?? null
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível consultar a placa'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar placa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 