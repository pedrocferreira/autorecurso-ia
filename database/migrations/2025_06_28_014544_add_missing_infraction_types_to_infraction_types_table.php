<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar tipos de infração que estavam faltando
        DB::table('infraction_types')->insert([
            [
                'code' => '162-10',
                'description' => 'Dirigir veículo segurando ou manuseando telefone celular',
                'law_article' => 'Art. 252, VI',
                'base_amount' => 293.47,
                'points' => 7,
                'severity' => 'very_severe',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '545-21',
                'description' => 'Estacionar em local/horário proibido especificamente pela sinalização',
                'law_article' => 'Art. 181, XVI',
                'base_amount' => 195.23,
                'points' => 3,
                'severity' => 'light',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '574-64',
                'description' => 'Transitar em velocidade superior à máxima permitida em mais de 20% até 50%',
                'law_article' => 'Art. 218, II',
                'base_amount' => 195.23,
                'points' => 5,
                'severity' => 'medium',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '574-65',
                'description' => 'Transitar em velocidade superior à máxima permitida em mais de 50%',
                'law_article' => 'Art. 218, III',
                'base_amount' => 880.41,
                'points' => 7,
                'severity' => 'very_severe',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '545-11',
                'description' => 'Estacionar em fila dupla',
                'law_article' => 'Art. 181, XV',
                'base_amount' => 130.16,
                'points' => 3,
                'severity' => 'light',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '518-21',
                'description' => 'Deixar de guardar distância de segurança lateral e frontal',
                'law_article' => 'Art. 192',
                'base_amount' => 130.16,
                'points' => 4,
                'severity' => 'medium',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '605-03',
                'description' => 'Deixar de parar o veículo sempre que a respectiva marcha for interceptada por pedestres',
                'law_article' => 'Art. 214',
                'base_amount' => 130.16,
                'points' => 4,
                'severity' => 'medium',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove os tipos de infração adicionados
        DB::table('infraction_types')->whereIn('code', [
            '162-10',
            '545-21', 
            '574-64',
            '574-65',
            '545-11',
            '518-21',
            '605-03'
        ])->delete();
    }
};
