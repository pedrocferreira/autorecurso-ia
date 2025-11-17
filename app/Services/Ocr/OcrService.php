<?php

namespace App\Services\Ocr;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class OcrService
{
    public function __construct(
        private readonly GeminiService $geminiService
    ) {
    }

    /**
     * Extrai os dados estruturados de uma imagem ou PDF de multa.
     *
     * @param  string  $absolutePath Caminho absoluto do arquivo no disco.
     * @return array{fields: array<string, mixed>, raw_text?: string|null, raw_json?: string|null, driver: string}
     */
    public function extractTicketData(string $absolutePath): array
    {
        $driver = config('services.ocr.driver', 'gemini');
        $absolutePath = realpath($absolutePath) ?: $absolutePath;

        if (!is_file($absolutePath)) {
            throw new \InvalidArgumentException(sprintf('Arquivo não encontrado para OCR: %s', $absolutePath));
        }

        $result = [
            'fields' => [],
            'raw_text' => null,
            'raw_json' => null,
            'driver' => $driver,
        ];

        if ($driver === 'olmocr') {
            try {
                $rawText = $this->extractUsingOlmocr($absolutePath);
                if (!empty($rawText)) {
                    $result['raw_text'] = $rawText;
                    $structured = $this->geminiService->extractTicketDataFromText($rawText);
                    $result['fields'] = $structured['fields'] ?? [];
                    $result['raw_json'] = $structured['raw_json'] ?? null;
                    $result['driver'] = 'olmocr+gemini';

                    return $result;
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao utilizar olmOCR, aplicando fallback para Gemini Vision.', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $structured = $this->geminiService->analyzeTicketImage($absolutePath);

        $result['fields'] = $structured['fields'] ?? [];
        $result['raw_json'] = $structured['raw_json'] ?? null;
        $result['driver'] = 'gemini';

        return $result;
    }

    /**
     * Executa o pipeline do olmOCR via script auxiliar.
     *
     * @return string|null Texto bruto extraído ou null em caso de falha.
     */
    private function extractUsingOlmocr(string $absolutePath): ?string
    {
        $scriptPath = config('services.ocr.olmocr.script_path');
        $pythonPath = config('services.ocr.olmocr.python_path', base_path('.venv/bin/python'));

        if (empty($scriptPath) || !is_file($scriptPath)) {
            throw new \RuntimeException('Script do olmOCR não localizado. Verifique OLMOCR_SCRIPT_PATH.');
        }

        if (empty($pythonPath) || !is_file($pythonPath)) {
            throw new \RuntimeException('Python configurado para o olmOCR não encontrado. Verifique OLMOCR_PYTHON_PATH.');
        }

        $process = new Process([$pythonPath, $scriptPath, '--input', $absolutePath]);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput() ?: $process->getOutput();
            Log::error('olmOCR retornou erro', ['error' => $error]);
            throw new \RuntimeException('olmOCR não pôde processar o arquivo. Consulte os logs.');
        }

        $output = trim($process->getOutput());
        $payload = json_decode($output, true);

        if (!is_array($payload) || empty($payload['raw_text'])) {
            Log::warning('olmOCR executou, mas não retornou texto válido.', ['output' => $output]);
            return null;
        }

        return $payload['raw_text'];
    }
}


