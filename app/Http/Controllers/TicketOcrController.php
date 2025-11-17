<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\Ocr\OcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class TicketOcrController extends Controller
{
    public function __construct(
        private readonly OcrService $ocrService
    ) {
        $this->middleware('auth');
    }

    public function store(Request $request): JsonResponse
    {
        // Log para debug
        $hasFile = $request->hasFile('document');
        $file = $request->file('document');
        
        \Log::info('OCR Upload Request', [
            'has_file' => $hasFile,
            'all_files' => array_keys($request->allFiles()),
            'all_input_keys' => array_keys($request->all()),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'file_info' => $file ? [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
            ] : null,
        ]);

        // Verifica se o arquivo foi enviado
        if (!$hasFile || !$file) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum arquivo foi enviado. Por favor, selecione uma imagem ou PDF.',
                'errors' => ['document' => ['Nenhum arquivo foi enviado.']],
            ], 422);
        }

        // Verifica se o upload foi bem-sucedido
        if (!$file->isValid()) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'O arquivo excede o limite de tamanho configurado no servidor.',
                UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o limite de tamanho do formulário.',
                UPLOAD_ERR_PARTIAL => 'O arquivo foi enviado parcialmente.',
                UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta uma pasta temporária no servidor.',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco.',
                UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload.',
            ];
            
            $errorCode = $file->getError();
            $errorMessage = $errorMessages[$errorCode] ?? 'Erro desconhecido no upload do arquivo.';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => ['document' => [$errorMessage]],
            ], 422);
        }

        try {
            $validated = $request->validate([
                'document' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:8192',
                'ticket_id' => 'nullable|exists:tickets,id',
            ], [
                'document.required' => 'Por favor, selecione um arquivo para upload.',
                'document.file' => 'O arquivo enviado não é válido.',
                'document.mimes' => 'O arquivo deve ser uma imagem (JPEG, PNG, WEBP) ou PDF. Tipo recebido: ' . ($file->getMimeType() ?? 'desconhecido'),
                'document.max' => 'O arquivo não pode ultrapassar 8MB. Tamanho recebido: ' . number_format($file->getSize() / 1024 / 1024, 2) . ' MB',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            \Log::error('Erro de validação no OCR upload', [
                'errors' => $errors,
                'request_data' => $request->except(['document']),
                'has_file' => $request->hasFile('document'),
            ]);
            
            $firstError = !empty($errors) ? reset($errors)[0] : $e->getMessage();
            
            return response()->json([
                'success' => false,
                'message' => $firstError,
                'errors' => $errors,
            ], 422);
        }

        /** @var UploadedFile $file */
        $file = $validated['document'];
        $storedPath = $file->store('ocr/uploads', 'local');
        $absolutePath = Storage::disk('local')->path($storedPath);

        try {
            $result = $this->ocrService->extractTicketData($absolutePath);
        } catch (Throwable $exception) {
            report($exception);
            
            $message = 'Não foi possível extrair os dados automaticamente. Verifique a qualidade da imagem e tente novamente.';
            if (app()->environment('local')) {
                $message = $exception->getMessage() ?: $message;
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => app()->environment('local') ? $exception->getMessage() : null,
            ], 500);
        } finally {
            // Remove o arquivo temporário após o processamento
            if (Storage::disk('local')->exists($storedPath)) {
                Storage::disk('local')->delete($storedPath);
            }
        }

        if (!empty($validated['ticket_id'])) {
            $ticket = Ticket::findOrFail($validated['ticket_id']);
            $this->authorize('update', $ticket);

            $fields = $result['fields'] ?? [];
            if (!is_array($fields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível estruturar os dados automaticamente. Tente novamente.',
                ], 500);
            }

            $columns = array_flip(Schema::getColumnListing($ticket->getTable()));
            $allowed = array_intersect_key($fields, $columns);
            $filtered = array_filter($allowed, static fn ($value) => $value !== null && $value !== '');

            if ($filtered) {
                $ticket->fill($filtered);
                $ticket->save();
            }

            $result['ticket'] = $ticket->fresh();
        }

        return response()->json([
            'success' => true,
            'message' => 'OCR executado com sucesso.',
            'data' => $result,
        ]);
    }
}


