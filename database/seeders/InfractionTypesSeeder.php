<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InfractionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Caminho para o arquivo JSON
        $jsonPath = database_path('seeders/multas_ctb.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("Arquivo {$jsonPath} não encontrado!");
            return;
        }

        // Ler o arquivo JSON
        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['infraction_types'])) {
            $this->command->error("Formato do JSON inválido!");
            return;
        }

        // Truncar a tabela para começar limpo
        DB::table('infraction_types')->truncate();

        $this->command->info("Importando " . count($data['infraction_types']) . " infrações...");

        // Inserir as infrações em lotes para melhor performance
        $chunks = array_chunk($data['infraction_types'], 100);
        
        foreach ($chunks as $chunk) {
            $insertData = [];
            
            foreach ($chunk as $infraction) {
                $insertData[] = [
                    'code' => $infraction['code'],
                    'description' => $infraction['description'],
                    'law_article' => $infraction['law_article'],
                    'base_amount' => $infraction['base_amount'],
                    'points' => $infraction['points'],
                    'severity' => $infraction['severity'],
                    'active' => $infraction['active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            DB::table('infraction_types')->insert($insertData);
        }

        $this->command->info("✅ Importação concluída! " . count($data['infraction_types']) . " infrações inseridas na tabela 'infraction_types'.");
    }
} 