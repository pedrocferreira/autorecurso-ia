<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InfractionType;

class ListInfractionTypes extends Command
{
    protected $signature = 'infractions:list';
    protected $description = 'Lista todos os tipos de infrações cadastrados';

    public function handle()
    {
        $infractions = InfractionType::all();
        
        if ($infractions->isEmpty()) {
            $this->error('Nenhuma infração cadastrada!');
            return;
        }

        $headers = ['ID', 'Código', 'Descrição', 'Valor', 'Pontos', 'Gravidade'];
        $rows = [];

        foreach ($infractions as $infraction) {
            $rows[] = [
                $infraction->id,
                $infraction->code,
                $infraction->description,
                $infraction->formatted_amount,
                $infraction->points,
                $infraction->severity_text
            ];
        }

        $this->table($headers, $rows);
        $this->info('Total de infrações: ' . $infractions->count());
    }
} 