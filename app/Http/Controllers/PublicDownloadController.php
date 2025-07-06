<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appeal;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PublicDownloadController extends Controller
{
    /**
     * Download público de recurso por CPF
     */
    public function downloadRecurso($appealId, $cpf)
    {
        try {
            // Buscar o recurso
            $appeal = Appeal::with('ticket')->find($appealId);
            
            if (!$appeal) {
                return response()->json(['error' => 'Recurso não encontrado'], 404);
            }

            // Verificar se o CPF corresponde ao ticket
            $ticketCpf = preg_replace('/[^0-9]/', '', $appeal->ticket->cpf);
            $requestedCpf = preg_replace('/[^0-9]/', '', $cpf);

            if ($ticketCpf !== $requestedCpf) {
                return response()->json(['error' => 'CPF não autorizado para este recurso'], 403);
            }

            // Verificar se o arquivo existe
            if (!$appeal->pdf_path || !Storage::disk('public')->exists($appeal->pdf_path)) {
                return response()->json(['error' => 'Arquivo PDF não encontrado'], 404);
            }

            // Log do download
            Log::info('Download público de recurso', [
                'appeal_id' => $appealId,
                'cpf' => $cpf,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Retornar o arquivo para download
            return Storage::disk('public')->download($appeal->pdf_path, 'recurso_' . $appeal->ticket->ticket_number . '.pdf');

        } catch (\Exception $e) {
            Log::error('Erro no download público de recurso: ' . $e->getMessage(), [
                'appeal_id' => $appealId,
                'cpf' => $cpf
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
} 