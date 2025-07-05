<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecursoGeradoMail;
use App\Models\User;
use App\Models\Appeal;

class TestBrevoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-brevo {email? : Email de destino para teste}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de email via Brevo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'teste@example.com';
        
        $this->info('Testando envio de email via Brevo...');
        $this->info('Email de destino: ' . $email);
        
        try {
            // Criar um usuário fake para teste
            $user = new User();
            $user->name = 'Usuário Teste';
            $user->email = $email;
            
            // Criar um appeal fake para teste
            $appeal = new Appeal();
            $appeal->id = 999999;
            $appeal->ticket = new \stdClass();
            $appeal->ticket->plate = 'ABC-1234';
            $appeal->ticket->infraction_number = 'TEST123456';
            
            // Enviar email de teste
            Mail::to($email)->send(new RecursoGeradoMail($user, $appeal));
            
            $this->info('✅ Email enviado com sucesso!');
            $this->info('Verifique a caixa de entrada do email: ' . $email);
            
            // Mostrar informações sobre a configuração
            $this->newLine();
            $this->info('Configuração atual:');
            $this->table(
                ['Configuração', 'Valor'],
                [
                    ['MAIL_MAILER', config('mail.default')],
                    ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                    ['MAIL_FROM_NAME', config('mail.from.name')],
                    ['BREVO_API_KEY', substr(env('BREVO_API_KEY'), 0, 20) . '...'],
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
} 