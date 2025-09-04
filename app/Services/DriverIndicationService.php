<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use Exception;

class DriverIndicationService
{
    private ImageExtractionService $imageExtractionService;

    public function __construct(ImageExtractionService $imageExtractionService)
    {
        $this->imageExtractionService = $imageExtractionService;
    }

    /**
     * Gera um único PDF contendo o formulário de indicação de condutor preenchido
     * e, nas páginas seguintes, as imagens anexadas (multa, CNH, documento do proprietário e, se houver, representação PJ).
     *
     * Parâmetros devem ser caminhos absolutos dos arquivos de imagem ou PDF já disponíveis no servidor.
     *
     * @param string $foto_notificacao_multa
     * @param string $foto_doc_carro
     * @param string $foto_cnh_condutor
     * @param string $foto_doc_proprietario
     * @param bool $is_pessoa_juridica
     * @param string|null $foto_doc_representacao_pj
     * @return string Caminho relativo no storage público para o PDF gerado
     */
    public function gerar_pdf_indicacao_condutor(
        string $foto_notificacao_multa,
        string $foto_doc_carro,
        string $foto_cnh_condutor,
        string $foto_doc_proprietario,
        bool $is_pessoa_juridica = false,
        ?string $foto_doc_representacao_pj = null
    ): string {
        try {
            Log::info('📝 Iniciando geração de PDF: Indicação de Condutor');

            // Converter PDFs para imagens se necessário
            $foto_notificacao_multa = $this->convertPdfToImage($foto_notificacao_multa);
            $foto_doc_carro = $this->convertPdfToImage($foto_doc_carro);
            $foto_cnh_condutor = $this->convertPdfToImage($foto_cnh_condutor);
            $foto_doc_proprietario = $this->convertPdfToImage($foto_doc_proprietario);
            
            if ($foto_doc_representacao_pj) {
                $foto_doc_representacao_pj = $this->convertPdfToImage($foto_doc_representacao_pj);
            }

            // 1) OCR dos documentos
            $multa = $this->imageExtractionService->extractDataFromImage($foto_notificacao_multa, 'notification');
            $condutor = $this->imageExtractionService->extractDataFromImage($foto_cnh_condutor, 'cnh');
            // Proprietário pode ser RG ou CNH; tentamos como CNH e caímos para genérico
            $proprietario = $this->imageExtractionService->extractDataFromImage($foto_doc_proprietario, 'cnh');
            if (empty($proprietario)) {
                $proprietario = $this->imageExtractionService->extractDataFromImage($foto_doc_proprietario, 'generic');
            }

            $representacaoPj = [];
            if ($is_pessoa_juridica && $foto_doc_representacao_pj) {
                $representacaoPj = $this->imageExtractionService->extractDataFromImage($foto_doc_representacao_pj, 'generic');
            }

            // Normalização de campos esperados no formulário
            $dadosForm = [
                'auto_infracao' => $multa['citation_number'] ?? ($multa['notification']['citation_number'] ?? ''),
                'orgao_autuador' => $multa['orgao_autuador'] ?? '',
                'placa' => $multa['plate'] ?? '',
                'codigo_infracao' => $multa['infraction_code'] ?? '',
                'data' => $multa['date'] ?? '',
                'hora' => $multa['time'] ?? '',

                'condutor_nome' => $condutor['name'] ?? '',
                'condutor_cpf' => $condutor['cpf'] ?? '',
                'condutor_rg' => $condutor['rg'] ?? '',
                'condutor_cnh' => $condutor['driver_license'] ?? '',

                'proprietario_nome' => $proprietario['name'] ?? $proprietario['owner_name'] ?? '',
                'proprietario_doc' => $proprietario['cpf'] ?? $proprietario['rg'] ?? $proprietario['owner_cpf'] ?? '',

                'is_pj' => $is_pessoa_juridica,
                'representante_nome' => $representacaoPj['representative_name'] ?? ($proprietario['name'] ?? ''),
                'representante_doc' => $representacaoPj['representative_document'] ?? ($proprietario['cpf'] ?? $proprietario['rg'] ?? ''),
            ];

            // 2) Preparar imagens para anexos (corrigir orientação e redimensionar no HTML)
            $pathsAnexos = [];
            // Ordem sugerida: Formulário (página 1), Notificação, CNH, Documento do Proprietário, PJ (se houver)
            $pathsAnexos[] = $this->prepareImageForPdf($foto_notificacao_multa);
            // Opcional: CRLV como apoio extra (após os principais anexos)
            $pathCrlvPrepared = $this->prepareImageForPdf($foto_doc_carro);
            $pathsPrincipais = [
                $this->prepareImageForPdf($foto_cnh_condutor),
                $this->prepareImageForPdf($foto_doc_proprietario),
            ];
            if ($is_pessoa_juridica && $foto_doc_representacao_pj) {
                $pathsAnexos[] = $this->prepareImageForPdf($foto_doc_representacao_pj);
            }
            // Monta array final mantendo a ordem sugerida e adicionando CRLV ao final
            $pathsAnexos = array_merge($pathsAnexos, $pathsPrincipais);
            if ($pathCrlvPrepared) {
                $pathsAnexos[] = $pathCrlvPrepared;
            }

            // 3) Gerar PDF via view
            $dataView = [
                'form' => $dadosForm,
                'anexos' => array_values(array_filter($pathsAnexos)),
                'generated_at' => now()->format('d/m/Y H:i'),
            ];

            $pdf = Pdf::loadView('pdfs.indicacao_condutor', $dataView);

            // 4) Salvar no storage público
            $dir = 'public/indications/' . date('Y/m');
            Storage::makeDirectory($dir);
            $filename = 'indicacao_condutor_' . Str::random(10) . '.pdf';
            $path = $dir . '/' . $filename;

            Storage::put($path, $pdf->output());

            Log::info('✅ PDF de indicação gerado', ['path' => $path]);
            // Retornar caminho relativo sem o prefixo public/
            return substr($path, 7);
        } catch (\Throwable $e) {
            Log::error('❌ Erro ao gerar PDF de indicação: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Converte PDF para imagem se necessário
     * 
     * @param string $filePath Caminho do arquivo
     * @return string Caminho do arquivo (imagem original ou convertida)
     */
    private function convertPdfToImage(string $filePath): string
    {
        try {
            if (!file_exists($filePath)) {
                return $filePath;
            }

            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            // Se não for PDF, retorna o caminho original
            if ($extension !== 'pdf') {
                return $filePath;
            }

            Log::info('🔄 Tentando converter PDF para imagem', ['arquivo' => basename($filePath)]);

            // Verificar se o Imagick está disponível
            if (!extension_loaded('imagick')) {
                Log::warning('⚠️ Imagick não disponível. PDFs não podem ser processados.');
                throw new Exception('PDFs não podem ser processados. O sistema Imagick não está disponível. Por favor, envie imagens (JPG, PNG, WEBP) em vez de PDFs.');
            }

            $imagick = new Imagick();
            $imagick->setResolution(300, 300); // Alta resolução para melhor OCR
            $imagick->readImage($filePath);
            
            // Converter apenas a primeira página
            $imagick->setIteratorIndex(0);
            
            // Configurar formato de saída
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(90);
            
            // Criar diretório temporário se não existir
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0775, true);
            }
            
            // Gerar nome único para o arquivo convertido
            $outputPath = $tempDir . '/' . Str::random(16) . '.jpg';
            
            // Salvar imagem convertida
            $imagick->writeImage($outputPath);
            $imagick->clear();
            $imagick->destroy();
            
            Log::info('✅ PDF convertido para imagem', [
                'original' => basename($filePath),
                'convertido' => basename($outputPath)
            ]);
            
            return $outputPath;
            
        } catch (Exception $e) {
            Log::warning('⚠️ Falha ao converter PDF para imagem: ' . $e->getMessage());
            // Em caso de erro, retorna o arquivo original
            return $filePath;
        }
    }

    /**
     * Corrige orientação via EXIF (quando disponível) e retorna caminho absoluto
     * para uma cópia preparada em storage/app/temp.
     */
    private function prepareImageForPdf(string $absolutePath): ?string
    {
        try {
            if (!is_file($absolutePath)) {
                return null;
            }

            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                return $absolutePath; // deixa como está
            }

            $image = null;
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image = @imagecreatefromjpeg($absolutePath);
            } elseif ($extension === 'png') {
                $image = @imagecreatefrompng($absolutePath);
            } elseif ($extension === 'webp' && function_exists('imagecreatefromwebp')) {
                $image = @imagecreatefromwebp($absolutePath);
            }

            if (!$image) {
                return $absolutePath;
            }

            // Tentar ler EXIF para orientar corretamente (apenas JPEG normalmente)
            $orientation = 1;
            if (function_exists('exif_read_data') && in_array($extension, ['jpg', 'jpeg'])) {
                $exif = @exif_read_data($absolutePath);
                if (!empty($exif['Orientation'])) {
                    $orientation = (int) $exif['Orientation'];
                }
            }

            $rotated = $image;
            switch ($orientation) {
                case 3: // 180
                    $rotated = imagerotate($image, 180, 0);
                    break;
                case 6: // 90 CW
                    $rotated = imagerotate($image, -90, 0);
                    break;
                case 8: // 90 CCW
                    $rotated = imagerotate($image, 90, 0);
                    break;
            }

            // Salvar cópia em storage/app/temp
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }
            $target = $tempDir . '/' . Str::random(16) . '.' . $extension;

            $ok = false;
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $ok = @imagejpeg($rotated, $target, 90);
            } elseif ($extension === 'png') {
                $ok = @imagepng($rotated, $target, 6);
            } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                $ok = @imagewebp($rotated, $target, 80);
            }

            if ($rotated !== $image) {
                @imagedestroy($image);
            }
            @imagedestroy($rotated);

            return $ok ? $target : $absolutePath;
        } catch (\Throwable $e) {
            Log::warning('⚠️ Falha ao preparar imagem para PDF: ' . $e->getMessage());
            return $absolutePath;
        }
    }
}


