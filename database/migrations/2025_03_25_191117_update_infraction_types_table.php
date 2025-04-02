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
        // Desativar todas as infrações antigas
        DB::table('infraction_types')->update(['active' => false]);

        // Inserir todas as infrações do CTB
        $infractions = [
            // Artigo 162
            ['162-I', 'Art. 162, I', 'Dirigir veículo sem possuir CNH', 880.41, 7, 'very_serious', false],
            ['162-II', 'Art. 162, II', 'Dirigir veículo com CNH cassada ou suspensa', 880.41, 7, 'very_serious', false],
            ['162-III', 'Art. 162, III', 'Dirigir com CNH de categoria errada', 586.94, 7, 'very_serious', false],
            ['162-VI', 'Art. 162, VI', 'Dirigir sem usar lentes corretoras de visão', 293.47, 7, 'very_serious', false],
            ['162-V', 'Art. 162, V', 'Dirigir com a CNH vencida (+30 dias)', 293.47, 7, 'very_serious', false],

            // Artigos 163-170
            ['163', 'Art. 163', 'Entregar a direção a pessoa nas condições do artigo 162', 293.47, 7, 'very_serious', false],
            ['164', 'Art. 164', 'Permitir que pessoa nas condições do art. 162 dirija', 293.47, 7, 'very_serious', false],
            ['165', 'Art. 165', 'Dirigir sob a influência de álcool', 2934.70, 7, 'very_serious', true],
            ['165-A', 'Art. 165-A', 'Recusar o teste do bafômetro', 2934.70, 7, 'very_serious', true],
            ['166', 'Art. 166', 'Entregar a direção a pessoa habilitada sem condições de dirigir', 293.47, 7, 'very_serious', false],
            ['167', 'Art. 167', 'Deixar o condutor ou passageiro de usar o cinto de segurança', 195.23, 5, 'serious', false],
            ['168', 'Art. 168', 'Transportar crianças de forma irregular', 293.47, 7, 'very_serious', false],
            ['169', 'Art. 169', 'Dirigir sem atenção ou sem os cuidados indispensáveis à segurança', 88.38, 3, 'light', false],
            ['170', 'Art. 170', 'Dirigir ameaçando os pedestres ou os demais veículos', 293.00, 7, 'very_serious', true],

            // Artigos 171-180
            ['171', 'Art. 171', 'Jogar água sobre os pedestres ou veículos', 130.16, 4, 'medium', false],
            ['172', 'Art. 172', 'Atirar do veículo ou abandonar na via objetos ou substâncias', 130.16, 4, 'medium', false],
            ['173', 'Art. 173', 'Disputar corrida', 2934.70, 7, 'very_serious', true],
            ['174', 'Art. 174', 'Promover racha', 2934.70, 7, 'very_serious', true],
            ['175', 'Art. 175', 'Realizar manobra perigosa', 2934.70, 7, 'very_serious', true],
            ['176-I', 'Art. 176, I', 'Condutor envolvido em acidente deixar de prestar socorro', 1467.35, 7, 'very_serious', true],
            ['176-II', 'Art. 176, II', 'Condutor envolvido em acidente não adotar medidas de segurança no local', 1467.35, 7, 'very_serious', true],
            ['176-III', 'Art. 176, III', 'Condutor envolvido em acidente não facilitar o trabalho da perícia', 1467.35, 7, 'very_serious', true],
            ['176-IV', 'Art. 176, IV', 'Condutor envolvido em acidente se recusar a mover o veículo do local', 1467.35, 7, 'very_serious', true],
            ['176-V', 'Art. 176, V', 'Condutor envolvido em acidente não prestar informações p/ B.O.', 1467.35, 7, 'very_serious', true],

            // Artigos 181-190
            ['181-I', 'Art. 181, I', 'Estacionar o veículo nas esquinas', 130.16, 4, 'medium', false],
            ['181-II', 'Art. 181, II', 'Estacionar o veículo afastado da guia da calçada (50cm – 1m)', 88.38, 3, 'light', false],
            ['181-III', 'Art. 181, III', 'Estacionar o veículo afastado da guia da calçada (+ 1m)', 195.23, 5, 'serious', false],
            ['181-V', 'Art. 181, V', 'Estacionar o veículo na pista', 293.47, 7, 'very_serious', false],
            ['181-VIII', 'Art. 181, VIII', 'Estacionar o veículo no passeio, faixa de pedestre, ciclovia ou ciclofaixa', 195.23, 5, 'serious', false],
            ['181-XVII', 'Art. 181, XVII', 'Estacionar o veículo em desacordo com a sinalização', 195.23, 5, 'serious', false],
            ['181-XX', 'Art. 181, XX', 'Estacionar nas vagas reservadas às pessoas com deficiência ou idosos', 293.47, 7, 'very_serious', false],

            // Velocidade
            ['218-I', 'Art. 218, I', 'Transitar em velocidade superior à máxima permitida em até 20%', 130.16, 4, 'medium', false],
            ['218-II', 'Art. 218, II', 'Transitar em velocidade superior à máxima permitida em 20% até 50%', 195.23, 5, 'serious', false],
            ['218-III', 'Art. 218, III', 'Transitar em velocidade superior a 50% da máxima permitida', 880.41, 7, 'very_serious', true],

            // Outras infrações graves
            ['244-I', 'Art. 244, I', 'Conduzir motocicleta sem capacete', 293.47, 7, 'very_serious', false],
            ['252-VI', 'Art. 252, VI', 'Dirigir utilizando telefone celular', 293.47, 7, 'very_serious', false],
            ['203-V', 'Art. 203, V', 'Ultrapassar em faixa amarela contínua', 1467.35, 7, 'very_serious', false],
            ['208', 'Art. 208', 'Avançar o sinal vermelho do semáforo ou o de parada obrigatória', 293.47, 7, 'very_serious', false],
            ['210', 'Art. 210', 'Transpor, sem autorização, bloqueio viário policial', 293.47, 7, 'very_serious', true],
            ['214-I', 'Art. 214, I', 'Deixar de dar preferência a pedestre que se encontre na faixa', 293.47, 7, 'very_serious', false],
            ['220-I', 'Art. 220, I', 'Deixar de reduzir a velocidade próximo a passeatas, aglomerações', 293.47, 7, 'very_serious', false]
        ];

        foreach ($infractions as $infraction) {
            // Verificar se já existe uma infração com o mesmo código
            $existing = DB::table('infraction_types')
                ->where('code', $infraction[0])
                ->first();

            if ($existing) {
                // Atualizar a infração existente
                DB::table('infraction_types')
                    ->where('code', $infraction[0])
                    ->update([
                        'law_article' => $infraction[1],
                        'description' => $infraction[2],
                        'base_amount' => $infraction[3],
                        'points' => $infraction[4],
                        'severity' => $infraction[5],
                        'active' => true,
                        'suspends_license' => $infraction[6],
                        'updated_at' => now()
                    ]);
            } else {
                // Inserir nova infração
                DB::table('infraction_types')->insert([
                    'code' => $infraction[0],
                    'law_article' => $infraction[1],
                    'description' => $infraction[2],
                    'base_amount' => $infraction[3],
                    'points' => $infraction[4],
                    'severity' => $infraction[5],
                    'active' => true,
                    'suspends_license' => $infraction[6],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não faz nada no down para preservar os dados
    }
};
