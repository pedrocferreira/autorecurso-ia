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
        Schema::create('infraction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // Código da infração (exemplo: "23452-0")
            $table->string('description');              // Descrição da infração
            $table->text('law_article');                // Artigo da lei/regulamento
            $table->decimal('base_amount', 10, 2);      // Valor padrão da multa
            $table->integer('points')->default(0);      // Pontos na carteira
            $table->enum('severity', ['light', 'medium', 'severe', 'very_severe']); // Gravidade
            $table->boolean('active')->default(true);   // Se a infração está ativa no catálogo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infraction_types');
    }
};
