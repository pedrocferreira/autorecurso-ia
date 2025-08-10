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
        Schema::create('infraction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // Código da infração (exemplo: "23452-0")
            $table->string('description');              // Descrição da infração
            $table->string('law_article');                // Artigo da lei/regulamento
            $table->decimal('base_amount', 10, 2);      // Valor padrão da multa
            $table->integer('points')->default(0);      // Pontos na carteira
            $table->enum('severity', ['light', 'medium', 'severe', 'very_severe']); // Gravidade
            $table->boolean('active')->default(true);   // Se a infração está ativa no catálogo
            $table->timestamps();
        });

        // Inserir tipos comuns de infrações
        DB::table('infraction_types')->insert([
            [
                'code' => '501-00',
                'description' => 'Dirigir sem CNH/PPD/ACC',
                'law_article' => 'Art. 162, I',
                'base_amount' => 880.41,
                'points' => 7,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '518-51',
                'description' => 'Dirigir sem atenção ou sem os cuidados indispensáveis à segurança',
                'law_article' => 'Art. 169',
                'base_amount' => 130.16,
                'points' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '574-63',
                'description' => 'Transitar em velocidade superior à máxima permitida em até 20%',
                'law_article' => 'Art. 218, I',
                'base_amount' => 130.16,
                'points' => 4,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '605-01',
                'description' => 'Avançar o sinal vermelho do semáforo',
                'law_article' => 'Art. 208',
                'base_amount' => 293.47,
                'points' => 7,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '736-62',
                'description' => 'Dirigir veículo utilizando-se de telefone celular',
                'law_article' => 'Art. 252, VI',
                'base_amount' => 293.47,
                'points' => 7,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '518-52',
                'description' => 'Deixar de usar o cinto de segurança',
                'law_article' => 'Art. 167',
                'base_amount' => 195.23,
                'points' => 5,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '545-22',
                'description' => 'Estacionar em desacordo com a regulamentação',
                'law_article' => 'Art. 181, XVII',
                'base_amount' => 195.23,
                'points' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '704-81',
                'description' => 'Deixar de indicar com antecedência, mediante gesto regulamentar de braço ou luz indicadora de direção',
                'law_article' => 'Art. 196',
                'base_amount' => 130.16,
                'points' => 3,
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
        Schema::dropIfExists('infraction_types');
    }
};
