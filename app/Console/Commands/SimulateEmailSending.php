<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SimulateEmailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:simulate {email : Email de destino}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula o envio de email de recurso usando Brevo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('=== SIMULAÇÃO DE ENVIO DE EMAIL DE RECURSO ===');
        $this->info('Email de destino: ' . $email);
        $this->newLine();
        
        try {
            // Enviar email simples de teste
            Mail::raw('Este é um email de teste do AutoRecurso usando Brevo.

Seu recurso de multa foi gerado com sucesso!

Detalhes da Multa:
- Placa do Veículo: ABC-1234
- Auto de Infração: TEST123456
- Data: ' . now()->format('d/m/Y H:i:s') . '

Este é apenas um teste para verificar se o sistema de email está funcionando corretamente.

Atenciosamente,
Equipe AutoRecurso', function($message) use ($email) {
                $message->to($email)
                        ->subject('Teste de Email - AutoRecurso')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✅ Email enviado com sucesso!');
            $this->info('Por favor, verifique a caixa de entrada do email: ' . $email);
            $this->newLine();
            
            // Mostrar configurações atuais
            $this->info('Configurações de Email:');
            $this->table(
                ['Configuração', 'Valor'],
                [
                    ['Mailer', config('mail.default')],
                    ['De (Email)', config('mail.from.address')],
                    ['De (Nome)', config('mail.from.name')],
                    ['Brevo Configurado', config('mail.default') === 'brevo' ? 'Sim' : 'Não'],
                ]
            );
            
            // Verificar logs
            $this->newLine();
            $this->info('💡 Dicas:');
            $this->line('- Verifique também a pasta de SPAM/Lixo Eletrônico');
            $this->line('- O email pode demorar alguns minutos para chegar');
            $this->line('- Se não receber, verifique se o email está correto');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar email: ' . $e->getMessage());
            $this->newLine();
            $this->error('Detalhes do erro:');
            $this->line($e->getTraceAsString());
            
            // Sugestões de debug
            $this->newLine();
            $this->warn('🔧 Sugestões de debug:');
            $this->line('1. Verifique se a API Key do Brevo está correta no .env');
            $this->line('2. Verifique se o email remetente está verificado no Brevo');
            $this->line('3. Verifique os logs em storage/logs/laravel.log');
            
            return 1;
        }
        
        return 0;
    }
} 