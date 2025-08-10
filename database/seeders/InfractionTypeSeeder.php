<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InfractionType;
use Illuminate\Support\Facades\DB;

class InfractionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa todos os tipos de infrações existentes
        DB::table('infraction_types')->delete();

        // Infrações comuns
        InfractionType::create([
            'code' => '745-50',
            'description' => 'Avançar o sinal vermelho do semáforo',
            'law_article' => 'Art. 208 do CTB',
            'base_amount' => 293.47,
            'points' => 7,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '518-51',
            'description' => 'Exceder a velocidade em até 20%',
            'law_article' => 'Art. 218, I do CTB',
            'base_amount' => 130.16,
            'points' => 4,
            'severity' => 'medium',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '519-30',
            'description' => 'Exceder a velocidade entre 20% e 50%',
            'law_article' => 'Art. 218, II do CTB',
            'base_amount' => 195.23,
            'points' => 5,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '520-70',
            'description' => 'Exceder a velocidade acima de 50%',
            'law_article' => 'Art. 218, III do CTB',
            'base_amount' => 880.41,
            'points' => 7,
            'severity' => 'very_severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '736-30',
            'description' => 'Estacionar em local proibido',
            'law_article' => 'Art. 181, XVII do CTB',
            'base_amount' => 88.38,
            'points' => 3,
            'severity' => 'light',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '555-00',
            'description' => 'Dirigir utilizando celular',
            'law_article' => 'Art. 252, V do CTB',
            'base_amount' => 293.47,
            'points' => 7,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '596-70',
            'description' => 'Dirigir sem cinto de segurança',
            'law_article' => 'Art. 167 do CTB',
            'base_amount' => 195.23,
            'points' => 5,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '574-60',
            'description' => 'Conduzir veículo sem equipamento obrigatório',
            'law_article' => 'Art. 230, IX do CTB',
            'base_amount' => 195.23,
            'points' => 5,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '763-31',
            'description' => 'Dirigir com carteira de habilitação vencida há mais de 30 dias',
            'law_article' => 'Art. 162, V do CTB',
            'base_amount' => 293.47,
            'points' => 7,
            'severity' => 'severe',
            'active' => true,
        ]);

        InfractionType::create([
            'code' => '670-00',
            'description' => 'Não dar preferência a pedestres na faixa',
            'law_article' => 'Art. 214, V do CTB',
            'base_amount' => 293.47,
            'points' => 7,
            'severity' => 'severe',
            'active' => true,
        ]);
    }
}
