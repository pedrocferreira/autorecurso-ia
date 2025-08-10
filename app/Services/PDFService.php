<?php

namespace App\Services;

use App\Models\Appeal;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PDFService
{
    /**
     * Gera um PDF para um recurso.
     *
     * @param Appeal $appeal
     * @return string O caminho para o PDF gerado
     */
    public function generatePDF(Appeal $appeal): string
    {
        try {
            Log::info('Iniciando geração de PDF para o recurso: ' . $appeal->id);

            $ticket = $appeal->ticket;
            $user = $appeal->user;

            $data = [
                'appeal' => $appeal,
                'ticket' => $ticket,
                'user' => $user,
                'generated_at' => now()->format('d/m/Y H:i:s'),
            ];

            Log::info('Carregando view para o PDF');
            $pdf = PDF::loadView('appeals.pdf', $data);

            // Define o nome do arquivo
            $filename = 'recurso_' . $ticket->plate . '_' . Str::random(8) . '.pdf';
            $path = 'appeals/' . $user->id . '/' . $filename;

            // Armazena o PDF
            Log::info('Salvando PDF no storage: ' . $path);

            // Garante que o diretório existe
            Storage::makeDirectory('public/appeals/' . $user->id);

            $result = Storage::put('public/' . $path, $pdf->output());

            if (!$result) {
                Log::error('Falha ao salvar o PDF no storage');
                throw new \Exception('Não foi possível salvar o PDF');
            }

            Log::info('PDF gerado com sucesso');
            return $path;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
