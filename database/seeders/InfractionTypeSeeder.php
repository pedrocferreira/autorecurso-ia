<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InfractionTypeSeeder extends Seeder
{
    public function run()
    {
        // Carregar o conteúdo do arquivo JSON
        $json = File::get(database_path('multas.json'));
        $infractions = json_decode($json, true);

        // Inserir cada infração no banco de dados
        foreach ($infractions as $infraction) {
            DB::table('infraction_types')->insert([
                'code' => $infraction['code'],
                'description' => $infraction['description'],
                'law_article' => $infraction['law_article'],
                'base_amount' => $infraction['base_amount'],
                'points' => $infraction['points'],
                'severity' => $infraction['severity'],
                'active' => $infraction['active'],
            ]);
        }
    }
}
