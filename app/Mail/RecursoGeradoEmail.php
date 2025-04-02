<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecursoGeradoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recurso;

    public function __construct($user, $recurso)
    {
        $this->user = $user;
        $this->recurso = $recurso;
    }

    public function build()
    {
        return $this->subject('Seu Recurso foi Gerado com Sucesso!')
                    ->view('emails.recurso-gerado');
    }
} 