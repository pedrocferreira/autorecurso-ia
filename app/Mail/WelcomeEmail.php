<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        Log::channel('email')->info('Iniciando envio de email de boas-vindas', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function build()
    {
        try {
            Log::channel('email')->info('Construindo email de boas-vindas', [
                'user_id' => $this->user->id
            ]);

            $mail = $this->subject('Bem-vindo ao AutoRecurso!')
                        ->view('emails.welcome');

            Log::channel('email')->info('Email de boas-vindas construído com sucesso', [
                'user_id' => $this->user->id
            ]);

            return $mail;
        } catch (\Exception $e) {
            Log::channel('email')->error('Erro ao construir email de boas-vindas', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 