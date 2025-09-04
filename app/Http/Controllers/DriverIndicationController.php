<?php

namespace App\Http\Controllers;

use App\Services\DriverIndicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DriverIndicationController extends Controller
{
    private DriverIndicationService $service;

    public function __construct(DriverIndicationService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint para gerar o PDF de indicação de condutor.
     * Aceita uploads multipart/form-data ou caminhos absolutos via JSON.
     * Suporta imagens (JPG, PNG, WEBP) e PDFs.
     */
    public function generate(Request $request)
    {
        // Verificar se é um teste de funcionalidade
        if ($request->has('test') && $request->input('test') === true) {
            return response()->json([
                'success' => true,
                'message' => 'Teste de funcionalidade realizado com sucesso',
                'pdf_support' => extension_loaded('imagick')
            ]);
        }

        $validated = $request->validate([
            'foto_notificacao_multa' => 'required',
            'foto_doc_carro' => 'required',
            'foto_cnh_condutor' => 'required',
            'foto_doc_proprietario' => 'required',
            'is_pessoa_juridica' => 'nullable|boolean',
            'foto_doc_representacao_pj' => 'nullable'
        ]);

        // Suporte a upload direto (arquivos) OU caminho absoluto
        $pathNotificacao = $this->storeIfUploaded($request, 'foto_notificacao_multa');
        $pathCRLV = $this->storeIfUploaded($request, 'foto_doc_carro');
        $pathCnh = $this->storeIfUploaded($request, 'foto_cnh_condutor');
        $pathProprietario = $this->storeIfUploaded($request, 'foto_doc_proprietario');
        $pathPJ = $this->storeIfUploaded($request, 'foto_doc_representacao_pj');

        try {
            $relative = $this->service->gerar_pdf_indicacao_condutor(
                $pathNotificacao,
                $pathCRLV,
                $pathCnh,
                $pathProprietario,
                (bool) ($validated['is_pessoa_juridica'] ?? false),
                $pathPJ
            );

            $url = Storage::url($relative);
            return response()->json([
                'success' => true,
                'path' => $relative,
                'url' => $url
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar indicação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Falha ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function storeIfUploaded(Request $request, string $key): ?string
    {
        if ($request->hasFile($key)) {
            $file = $request->file($key);
            
            // Validar tipo de arquivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception("Tipo de arquivo não suportado para {$key}. Use JPG, PNG, WEBP ou PDF.");
            }
            
            // Validar tamanho (máximo 10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \Exception("Arquivo muito grande para {$key}. Máximo 10MB permitido.");
            }
            
            $stored = $file->store('public/uploads/indicacao');
            $absolute = storage_path('app/' . $stored);
            return $absolute;
        }

        $val = $request->input($key);
        return is_string($val) ? $val : null;
    }
}


