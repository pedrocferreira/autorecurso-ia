<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecursoGeradoMail;
use App\Models\Appeal;
use App\Models\User;

class TestEmailWithRealAppeal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-real {appeal_id? : ID do recurso} {email? : Email de destino}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de email com um recurso real do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appealId = $this->argument('appeal_id');
        $email = $this->argument('email');
        
        // Se não foi fornecido ID, pegar o último recurso
        if (!$appealId) {
            $appeal = Appeal::latest()->first();
            if (!$appeal) {
                $this->error('Nenhum recurso encontrado no banco de dados.');
                return 1;
            }
            $appealId = $appeal->id;
        } else {
            $appeal = Appeal::find($appealId);
            if (!$appeal) {
                $this->error('Recurso com ID ' . $appealId . ' não encontrado.');
                return 1;
            }
        }
        
        // Carregar relacionamentos
        $appeal->load(['ticket', 'user']);
        
        // Usar o email fornecido ou o email do usuário do recurso
        $targetEmail = $email ?? $appeal->user->email;
        
        $this->info('=== TESTE DE EMAIL COM RECURSO REAL ===');
        $this->info('ID do Recurso: ' . $appeal->id);
        $this->info('Usuário: ' . $appeal->user->name);
        $this->info('Email de destino: ' . $targetEmail);
        
        if ($appeal->ticket) {
            $this->info('Placa: ' . $appeal->ticket->plate);
            $this->info('Auto de Infração: ' . $appeal->ticket->infraction_number);
        }
        
        $this->newLine();
        
        if ($this->confirm('Deseja enviar o email de teste?')) {
            try {
                // Se estamos enviando para um email diferente, criar um usuário temporário
                if ($targetEmail !== $appeal->user->email) {
                    $user = new User();
                    $user->name = $appeal->user->name;
                    $user->email = $targetEmail;
                } else {
                    $user = $appeal->user;
                }
                
                Mail::to($targetEmail)->send(new RecursoGeradoMail($user, $appeal));
                
                $this->newLine();
                $this->info('✅ Email enviado com sucesso!');
                $this->info('Verifique a caixa de entrada do email: ' . $targetEmail);
                
                // Mostrar preview do conteúdo
                $this->newLine();
                if ($this->confirm('Deseja ver o conteúdo do recurso?')) {
                    $this->info('=== CONTEÚDO DO RECURSO ===');
                    $this->line(substr($appeal->content, 0, 500) . '...');
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Erro ao enviar email: ' . $e->getMessage());
                $this->error('Detalhes: ' . $e->getTraceAsString());
                return 1;
            }
        }
        
        return 0;
    }
} 