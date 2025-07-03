<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup dos dados existentes
        $existingData = \Illuminate\Support\Facades\DB::table('infraction_types')->get();
        
        // Dropar e recriar a tabela sem ENUM constraints
        Schema::dropIfExists('infraction_types');
        
        Schema::create('infraction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // Código da infração (exemplo: "23452-0")
            $table->text('description');                // Descrição da infração (text para textos longos)
            $table->string('law_article');             // Artigo da lei/regulamento
            $table->decimal('base_amount', 10, 2);      // Valor padrão da multa
            $table->integer('points')->default(0);      // Pontos na carteira
            $table->string('severity');                 // Gravidade como string sem constraints
            $table->boolean('active')->default(true);   // Se a infração está ativa no catálogo
            $table->timestamps();
        });
        
        // Restaurar dados se existiam
        if ($existingData->isNotEmpty()) {
            foreach ($existingData as $record) {
                \Illuminate\Support\Facades\DB::table('infraction_types')->insert([
                    'id' => $record->id,
                    'code' => $record->code,
                    'description' => $record->description,
                    'law_article' => $record->law_article,
                    'base_amount' => $record->base_amount,
                    'points' => $record->points,
                    'severity' => $record->severity,
                    'active' => $record->active,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migration não pode ser revertida facilmente
        // pois remove constraints do SQLite
    }
}; 