<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageExtractionService;

class DocumentExtractionController extends Controller
{
    protected $imageExtractionService;

    public function __construct(ImageExtractionService $imageExtractionService)
    {
        $this->imageExtractionService = $imageExtractionService;
    }

    public function extract(Request $request)
    {
        try {
            Log::info('📤 Recebida requisição de extração', [
                'type' => $request->input('type'),
                'has_file' => $request->hasFile('file')
            ]);

            $request->validate([
                'file' => 'required|file|max:10240', // 10MB
                'type' => 'required|in:cnh,notification,vehicle'
            ], [
                'file.required' => 'Por favor, selecione um arquivo.',
                'file.file' => 'O arquivo enviado não é válido.',
                'file.max' => 'O arquivo deve ter no máximo 10MB.',
                'type.required' => 'O tipo de documento é obrigatório.',
                'type.in' => 'Tipo de documento inválido.'
            ]);

            $file = $request->file('file');
            $type = $request->input('type');

            // Garantir diretório temporário e armazenar arquivo
            Storage::disk('public')->makeDirectory('temp');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filepath = $file->storeAs('temp', $filename, 'public');
            $fullPath = Storage::disk('public')->path($filepath);

            // Copiar o arquivo original para um diretório persistente
            $userId = auth()->id() ?: 'guest';
            $safeName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
            $persistDir = 'appeals_uploads/' . $userId;
            Storage::disk('public')->makeDirectory($persistDir);
            $persistFilename = time() . '_' . $safeName;
            $persistPath = $persistDir . '/' . $persistFilename;
            Storage::disk('public')->put($persistPath, file_get_contents($fullPath));

            // Extrair dados usando o serviço Gemini
            $extractedData = $this->imageExtractionService->extractDataFromImage($fullPath, $type);

            // Remover arquivo temporário
            Storage::disk('public')->delete($filepath);

            // Processamento adicional específico por tipo
            if ($type === 'notification' && isset($extractedData['notification'])) {
                // Mapear tipo de infração
                $reason = $extractedData['notification']['reason'] ?? '';
                $points = $extractedData['notification']['points'] ?? null;
                $extractedData['notification']['infraction_type_id'] = $this->mapInfractionType($reason, $points);
            }

            // Anexar metadados do arquivo
            $extractedData['uploaded_file'] = [
                'path' => $persistPath,
                'mime' => $file->getMimeType(),
                'name' => $file->getClientOriginalName(),
                'type' => $type
            ];

            Log::info('✅ Extração concluída com sucesso', array_keys($extractedData));

            return response()->json([
                'success' => true,
                'message' => 'Dados extraídos com sucesso',
                'data' => $extractedData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Erro na extração de documentos', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapeia a descrição da infração para um ID interno
     */
    private function mapInfractionType($reason, $points = null)
    {
        $reasonLower = strtolower($reason);
        
        // Padrões comuns de infrações
        $patterns = [
            'velocidade' => ['velocidade', 'excesso', 'radar', 'limite', 'acima', 'superior'],
            'estacionamento' => ['estacionar', 'estacionamento', 'parar', 'vaga'],
            'sinalização' => ['sinal', 'semáforo', 'placa', 'pare', 'preferencial', 'vermelho'],
            'cnh' => ['cnh', 'habilitação', 'carteira', 'licença'],
            'documentos' => ['documento', 'licenciamento', 'ipva', 'seguro'],
            'celular' => ['celular', 'telefone', 'aparelho'],
            'cinto' => ['cinto', 'segurança'],
            'licenciamento' => ['licenciamento', 'licenciamento anual', 'veículo sem licenciamento']
        ];
        
        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($reasonLower, $keyword) !== false) {
                    return $this->getInfractionTypeIdByCategory($category, $points);
                }
            }
        }
        
        return 1; // Padrão
    }
    
    private function getInfractionTypeIdByCategory($category, $points = null)
    {
        // Para infrações de velocidade, considerar a severidade baseada nos pontos
        if ($category === 'velocidade' && $points !== null) {
            $pointsInt = intval($points);
            if ($pointsInt <= 3) return 242; // Leve
            if ($pointsInt <= 5) return 243; // Média
            return 244; // Grave
        }
        
        $categoryMap = [
            'velocidade' => 242,
            'estacionamento' => 1,
            'sinalização' => 2,
            'cnh' => 2,
            'documentos' => 6,
            'celular' => 8,
            'cinto' => 7,
            'licenciamento' => 2
        ];
        
        return $categoryMap[$category] ?? 1;
    }
}