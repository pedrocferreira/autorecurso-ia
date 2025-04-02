<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AppealGeneratedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $appeal;

    public function __construct($appeal)
    {
        $this->appeal = $appeal;
        Log::channel('email')->info('Iniciando envio de email de notificação de recurso gerado', [
            'appeal_id' => $appeal->id,
            'user_id' => $appeal->user_id,
            'user_email' => $appeal->user->email
        ]);
    }

    public function build()
    {
        try {
            Log::channel('email')->info('Construindo email de notificação de recurso gerado', [
                'appeal_id' => $this->appeal->id
            ]);

            $mail = $this->subject('Seu Recurso foi Gerado com Sucesso!')
                        ->view('emails.appeal-generated');

            // Anexa o PDF do recurso se existir
            if ($this->appeal->pdf_path && Storage::exists('public/' . $this->appeal->pdf_path)) {
                Log::channel('email')->info('Anexando PDF ao email', [
                    'appeal_id' => $this->appeal->id,
                    'pdf_path' => $this->appeal->pdf_path
                ]);

                $mail->attach(storage_path('app/public/' . $this->appeal->pdf_path), [
                    'as' => 'recurso.pdf',
                    'mime' => 'application/pdf'
                ]);

                Log::channel('email')->info('PDF anexado com sucesso', [
                    'appeal_id' => $this->appeal->id
                ]);
            } else {
                Log::channel('email')->warning('PDF não encontrado para anexar ao email', [
                    'appeal_id' => $this->appeal->id,
                    'pdf_path' => $this->appeal->pdf_path
                ]);
            }

            Log::channel('email')->info('Email de notificação de recurso gerado construído com sucesso', [
                'appeal_id' => $this->appeal->id
            ]);

            return $mail;
        } catch (\Exception $e) {
            Log::channel('email')->error('Erro ao construir email de notificação de recurso gerado', [
                'appeal_id' => $this->appeal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 