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
        // 1. Remover a chave estrangeira da tabela tickets se existir
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'infraction_type_id')) {
                $table->dropForeign(['infraction_type_id']);
                $table->dropColumn('infraction_type_id');
            }
        });

        // 2. Dropar a tabela infraction_types se existir
        Schema::dropIfExists('infraction_types');

        // 3. Criar a tabela infraction_types com a estrutura correta
        Schema::create('infraction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('law_article');
            $table->string('description');
            $table->decimal('base_amount', 10, 2);
            $table->integer('points');
            $table->enum('severity', ['light', 'medium', 'serious', 'very_serious']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 4. Adicionar a coluna infraction_type_id na tabela tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('infraction_type_id')
                  ->after('user_id')
                  ->constrained()
                  ->onDelete('restrict');
        });

        // 5. Inserir todas as infrações
        $infractions = [
            // Infrações Gravíssimas (7 pontos)
            ['165', 'Art. 165', 'Dirigir sob influência de álcool', 293.47 * 10, 7, 'very_serious'],
            ['165-A', 'Art. 165-A', 'Recusar-se a realizar teste de alcoolemia', 293.47 * 10, 7, 'very_serious'],
            ['173', 'Art. 173', 'Disputar corrida por espírito de emulação', 293.47 * 10, 7, 'very_serious'],
            ['174', 'Art. 174', 'Promover, na via, competição esportiva sem autorização', 293.47 * 10, 7, 'very_serious'],
            ['175', 'Art. 175', 'Utilizar veículo para exibição de manobra perigosa', 293.47 * 10, 7, 'very_serious'],
            ['218-III', 'Art. 218 III', 'Transitar em velocidade superior à máxima em mais de 50%', 293.47 * 3, 7, 'very_serious'],
            ['244-I', 'Art. 244 I', 'Conduzir motocicleta sem capacete', 293.47, 7, 'very_serious'],
            ['252-VI', 'Art. 252 VI', 'Dirigir utilizando telefone celular', 293.47, 7, 'very_serious'],
            
            // Infrações Graves (5 pontos)
            ['162-I', 'Art. 162 I', 'Dirigir sem habilitação', 195.23, 5, 'serious'],
            ['167', 'Art. 167', 'Deixar de usar cinto de segurança', 195.23, 5, 'serious'],
            ['181-XVII', 'Art. 181 XVII', 'Estacionar em desacordo com condições regulamentadas - vaga idoso/deficiente', 195.23, 5, 'serious'],
            ['182-I', 'Art. 182 I', 'Parar sobre faixa de pedestres na mudança de sinal', 195.23, 5, 'serious'],
            ['185-I', 'Art. 185 I', 'Quando o veículo estiver em movimento, deixar de conservá-lo na faixa a ele destinada', 195.23, 5, 'serious'],
            ['202-I', 'Art. 202 I', 'Ultrapassar pela contramão em curvas', 195.23, 5, 'serious'],
            ['203-V', 'Art. 203 V', 'Ultrapassar pela contramão outro veículo onde houver linha contínua', 195.23, 5, 'serious'],
            
            // Infrações Médias (4 pontos)
            ['169', 'Art. 169', 'Dirigir sem atenção ou cuidados indispensáveis', 130.16, 4, 'medium'],
            ['181-VIII', 'Art. 181 VIII', 'Estacionar no passeio ou sobre faixa de pedestres', 130.16, 4, 'medium'],
            ['181-IX', 'Art. 181 IX', 'Estacionar em guia de calçada rebaixada', 130.16, 4, 'medium'],
            ['181-XIX', 'Art. 181 XIX', 'Estacionar em local proibido por placa', 130.16, 4, 'medium'],
            ['184-I', 'Art. 184 I', 'Transitar com o veículo na faixa/pista da esquerda', 130.16, 4, 'medium'],
            ['208', 'Art. 208', 'Avançar sinal vermelho ou de parada obrigatória', 130.16, 4, 'medium'],
            
            // Infrações Leves (3 pontos)
            ['169-I', 'Art. 169 I', 'Dirigir sem os cuidados indispensáveis à segurança', 88.38, 3, 'light'],
            ['181-IV', 'Art. 181 IV', 'Estacionar afastado da guia da calçada (meio-fio) a mais de 1m', 88.38, 3, 'light'],
            ['181-V', 'Art. 181 V', 'Estacionar afastado da guia da calçada (meio-fio) a mais de 50cm', 88.38, 3, 'light'],
            ['227-I', 'Art. 227 I', 'Usar buzina prolongada e sucessivamente a qualquer pretexto', 88.38, 3, 'light'],
            ['230-IX', 'Art. 230 IX', 'Transitar com o veículo produzindo fumaça', 88.38, 3, 'light'],
            ['230-X', 'Art. 230 X', 'Transitar com o veículo com defeito no sistema de iluminação', 88.38, 3, 'light'],
            ['230-XI', 'Art. 230 XI', 'Transitar com o veículo com defeito no sistema de sinalização', 88.38, 3, 'light']
        ];

        foreach ($infractions as $infraction) {
            DB::table('infraction_types')->insert([
                'code' => $infraction[0],
                'law_article' => $infraction[1],
                'description' => $infraction[2],
                'base_amount' => $infraction[3],
                'points' => $infraction[4],
                'severity' => $infraction[5],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['infraction_type_id']);
            $table->dropColumn('infraction_type_id');
        });

        Schema::dropIfExists('infraction_types');
    }
}; 