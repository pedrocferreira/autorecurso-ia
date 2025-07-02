<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InfractionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class InfractionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa todos os tipos de infrações existentes
        DB::table('infraction_types')->delete();

        // Carrega as infrações do arquivo JSON
        $jsonPath = database_path('seeders/deepseek_json_20250528_1a1656.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("Arquivo JSON não encontrado em: {$jsonPath}");
            return;
        }

        $jsonData = File::get($jsonPath);
        $infractions = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Erro ao decodificar JSON: " . json_last_error_msg());
            return;
        }

        if (isset($infractions['infraction_types']) && is_array($infractions['infraction_types'])) {
            foreach ($infractions['infraction_types'] as $infraction) {
                InfractionType::create([
                    'code' => $infraction['code'],
                    'description' => $infraction['description'],
                    'law_article' => $infraction['law_article'],
                    'base_amount' => $infraction['base_amount'],
                    'points' => $infraction['points'],
                    'severity' => $infraction['severity'],
                    'active' => $infraction['active'],
                ]);
            }
            $this->command->info(count($infractions['infraction_types']) . ' tipos de infrações carregados do JSON.');
        } else {
            $this->command->error('Formato de JSON inválido ou chave "infraction_types" não encontrada.');
        }
    }
}
