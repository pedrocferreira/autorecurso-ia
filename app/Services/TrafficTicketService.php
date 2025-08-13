<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class TrafficTicketService
{
    /**
     * Busca multas de trânsito por CPF/CNH
     */
    public function searchTicketsByDocument(string $document, User $user): array
    {
        try {
            Log::info('🔍 Iniciando busca de multas por documento', [
                'document' => $this->maskDocument($document),
                'user_id' => $user->id
            ]);

            $results = [];

            // 1. Buscar via API do DETRAN (estadual)
            $detranResults = $this->searchDetranTickets($document, $user);
            if ($detranResults['success']) {
                $results = array_merge($results, $detranResults['tickets']);
            }

            // 2. Buscar via API do DENATRAN (federal)
            $denatranResults = $this->searchDenatranTickets($document, $user);
            if ($denatranResults['success']) {
                $results = array_merge($results, $denatranResults['tickets']);
            }

            // 3. Buscar via API do SPP (Sistema de Pontuação)
            $sppResults = $this->searchSppTickets($document, $user);
            if ($sppResults['success']) {
                $results = array_merge($results, $sppResults['tickets']);
            }

            // 4. Sincronizar com banco local
            $this->syncTicketsWithDatabase($results, $user);

            Log::info('✅ Busca de multas concluída', [
                'total_encontradas' => count($results),
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'total_tickets' => count($results),
                'tickets' => $results,
                'message' => 'Busca concluída com sucesso'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erro ao buscar multas: ' . $e->getMessage(), [
                'document' => $this->maskDocument($document),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao buscar multas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Busca multas via DETRAN estadual
     */
    private function searchDetranTickets(string $document, User $user): array
    {
        try {
            // Simular busca no DETRAN (em produção, usar APIs reais)
            $detranTickets = $this->simulateDetranSearch($document);
            
            return [
                'success' => true,
                'tickets' => $detranTickets,
                'source' => 'DETRAN'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca DETRAN: ' . $e->getMessage());
            return ['success' => false, 'tickets' => []];
        }
    }

    /**
     * Busca multas via DENATRAN federal
     */
    private function searchDenatranTickets(string $document, User $user): array
    {
        try {
            // Simular busca no DENATRAN (em produção, usar APIs reais)
            $denatranTickets = $this->simulateDenatranSearch($document);
            
            return [
                'success' => true,
                'tickets' => $denatranTickets,
                'source' => 'DENATRAN'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca DENATRAN: ' . $e->getMessage());
            return ['success' => false, 'tickets' => []];
        }
    }

    /**
     * Busca multas via SPP (Sistema de Pontuação)
     */
    private function searchSppTickets(string $document, User $user): array
    {
        try {
            // Simular busca no SPP (em produção, usar APIs reais)
            $sppTickets = $this->simulateSppSearch($document);
            
            return [
                'success' => true,
                'tickets' => $sppTickets,
                'source' => 'SPP'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca SPP: ' . $e->getMessage());
            return ['success' => false, 'tickets' => []];
        }
    }

    /**
     * Simula busca no DETRAN (substituir por API real)
     */
    private function simulateDetranSearch(string $document): array
    {
        // Em produção, fazer requisição real para API do DETRAN
        $documentClean = preg_replace('/[^0-9]/', '', $document);
        
        // Simular multas baseadas no documento
        $tickets = [];
        
        // Simular multas de velocidade (código 554)
        if (strlen($documentClean) >= 11) {
            $tickets[] = [
                'citation_number' => 'DET' . substr($documentClean, -6) . '001',
                'date' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'time' => sprintf('%02d:%02d', rand(8, 22), rand(0, 59)),
                'amount' => rand(100, 300),
                'location' => 'Av. Paulista, São Paulo - SP',
                'reason' => 'Excesso de velocidade',
                'plate' => $this->generateRandomPlate(),
                'points' => 3,
                'article' => 'Art. 218, § 1º, inciso I',
                'city' => 'São Paulo',
                'state' => 'SP',
                'infraction_code' => '554-0',
                'orgao_autuador' => 'DETRAN-SP',
                'source' => 'DETRAN'
            ];
        }

        // Simular multas de estacionamento (código 500)
        if (strlen($documentClean) >= 11) {
            $tickets[] = [
                'citation_number' => 'DET' . substr($documentClean, -6) . '002',
                'date' => Carbon::now()->subDays(rand(1, 15))->format('Y-m-d'),
                'time' => sprintf('%02d:%02d', rand(8, 18), rand(0, 59)),
                'amount' => rand(50, 150),
                'location' => 'Rua Augusta, São Paulo - SP',
                'reason' => 'Estacionamento em local proibido',
                'plate' => $this->generateRandomPlate(),
                'points' => 0,
                'article' => 'Art. 181, inciso V',
                'city' => 'São Paulo',
                'state' => 'SP',
                'infraction_code' => '500-0',
                'orgao_autuador' => 'DETRAN-SP',
                'source' => 'DETRAN'
            ];
        }

        return $tickets;
    }

    /**
     * Simula busca no DENATRAN (substituir por API real)
     */
    private function simulateDenatranSearch(string $document): array
    {
        $documentClean = preg_replace('/[^0-9]/', '', $document);
        
        $tickets = [];
        
        // Simular multas federais
        if (strlen($documentClean) >= 11) {
            $tickets[] = [
                'citation_number' => 'DEN' . substr($documentClean, -6) . '001',
                'date' => Carbon::now()->subDays(rand(1, 45))->format('Y-m-d'),
                'time' => sprintf('%02d:%02d', rand(6, 23), rand(0, 59)),
                'amount' => rand(200, 500),
                'location' => 'BR-116, km 245, Curitiba - PR',
                'reason' => 'Ultrapassagem em local proibido',
                'plate' => $this->generateRandomPlate(),
                'points' => 4,
                'article' => 'Art. 203, inciso II',
                'city' => 'Curitiba',
                'state' => 'PR',
                'infraction_code' => '540-0',
                'orgao_autuador' => 'DENATRAN',
                'source' => 'DENATRAN'
            ];
        }

        return $tickets;
    }

    /**
     * Simula busca no SPP (substituir por API real)
     */
    private function simulateSppSearch(string $document): array
    {
        $documentClean = preg_replace('/[^0-9]/', '', $document);
        
        $tickets = [];
        
        // Simular multas de pontuação
        if (strlen($documentClean) >= 11) {
            $tickets[] = [
                'citation_number' => 'SPP' . substr($documentClean, -6) . '001',
                'date' => Carbon::now()->subDays(rand(1, 60))->format('Y-m-d'),
                'time' => sprintf('%02d:%02d', rand(7, 21), rand(0, 59)),
                'amount' => rand(150, 400),
                'location' => 'Av. Brasil, Rio de Janeiro - RJ',
                'reason' => 'Dirigir sem cinto de segurança',
                'plate' => $this->generateRandomPlate(),
                'points' => 5,
                'article' => 'Art. 167, inciso I',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'infraction_code' => '565-0',
                'orgao_autuador' => 'SPP',
                'source' => 'SPP'
            ];
        }

        return $tickets;
    }

    /**
     * Sincroniza multas encontradas com o banco local
     */
    private function syncTicketsWithDatabase(array $tickets, User $user): void
    {
        foreach ($tickets as $ticketData) {
            try {
                // Verificar se a multa já existe
                $existingTicket = Ticket::where('citation_number', $ticketData['citation_number'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existingTicket) {
                    // Criar nova multa
                    Ticket::create([
                        'user_id' => $user->id,
                        'citation_number' => $ticketData['citation_number'],
                        'date' => $ticketData['date'],
                        'time' => $ticketData['time'],
                        'amount' => $ticketData['amount'],
                        'location' => $ticketData['location'],
                        'reason' => $ticketData['reason'],
                        'plate' => $ticketData['plate'],
                        'points' => $ticketData['points'] ?? 0,
                        'article' => $ticketData['article'] ?? null,
                        'city' => $ticketData['city'] ?? null,
                        'state' => $ticketData['state'] ?? null,
                        'infraction_code' => $ticketData['infraction_code'] ?? null,
                        'orgao_autuador' => $ticketData['orgao_autuador'] ?? null,
                        'vehicle_chassi' => null,
                        'vehicle_renavam' => null,
                        'phone' => null,
                        'source' => $ticketData['source'] ?? 'API'
                    ]);

                    Log::info('✅ Nova multa sincronizada', [
                        'citation_number' => $ticketData['citation_number'],
                        'user_id' => $user->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Erro ao sincronizar multa: ' . $e->getMessage(), [
                    'citation_number' => $ticketData['citation_number'] ?? 'N/A',
                    'user_id' => $user->id
                ]);
            }
        }
    }

    /**
     * Gera placa aleatória para simulação
     */
    private function generateRandomPlate(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        $plate = '';
        for ($i = 0; $i < 3; $i++) {
            $plate .= $letters[rand(0, 25)];
        }
        $plate .= '-';
        for ($i = 0; $i < 4; $i++) {
            $plate .= $numbers[rand(0, 9)];
        }
        
        return $plate;
    }

    /**
     * Mascara documento para logs
     */
    private function maskDocument(string $document): string
    {
        $clean = preg_replace('/[^0-9]/', '', $document);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '.***.***-' . substr($clean, -2);
        }
        if (strlen($clean) === 14) {
            return substr($clean, 0, 2) . '.***.***/****-' . substr($clean, -2);
        }
        return '***' . substr($clean, -3);
    }

    /**
     * Busca multas por CPF
     */
    public function searchByCpf(string $cpf, User $user): array
    {
        return $this->searchTicketsByDocument($cpf, $user);
    }

    /**
     * Busca multas por CNH
     */
    public function searchByCnh(string $cnh, User $user): array
    {
        return $this->searchTicketsByDocument($cnh, $user);
    }

    /**
     * Busca multas por placa
     */
    public function searchByPlate(string $plate, User $user): array
    {
        try {
            Log::info('🔍 Buscando multas por placa', [
                'plate' => $plate,
                'user_id' => $user->id
            ]);

            // Buscar multas locais por placa
            $localTickets = Ticket::where('plate', $plate)
                ->where('user_id', $user->id)
                ->get();

            // Buscar multas externas por placa (se disponível)
            $externalTickets = $this->searchExternalTicketsByPlate($plate, $user);

            $allTickets = $localTickets->merge($externalTickets);

            return [
                'success' => true,
                'total_tickets' => $allTickets->count(),
                'tickets' => $allTickets->toArray(),
                'message' => 'Busca por placa concluída'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erro ao buscar multas por placa: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erro ao buscar multas por placa'
            ];
        }
    }

    /**
     * Busca multas externas por placa
     */
    private function searchExternalTicketsByPlate(string $plate, User $user): array
    {
        // Em produção, integrar com APIs que permitem busca por placa
        // Por enquanto, retornar array vazio
        return [];
    }
}
