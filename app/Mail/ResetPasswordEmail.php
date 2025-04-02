<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        Log::channel('email')->info('Iniciando envio de email de recuperação de senha', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function build()
    {
        try {
            Log::channel('email')->info('Construindo email de recuperação de senha', [
                'user_id' => $this->user->id
            ]);

            $mail = $this->subject('Recuperação de Senha - AutoRecurso')
                        ->view('emails.reset-password');

            Log::channel('email')->info('Email de recuperação de senha construído com sucesso', [
                'user_id' => $this->user->id
            ]);

            return $mail;
        } catch (\Exception $e) {
            Log::channel('email')->error('Erro ao construir email de recuperação de senha', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 