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
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->longText('generated_text'); // Texto gerado pela IA
            $table->string('pdf_path'); // Caminho para o arquivo PDF gerado
            $table->enum('status', ['pending', 'sent', 'successful', 'rejected'])->default('pending'); // Status do recurso
            $table->text('notes')->nullable(); // Notas adicionais sobre o recurso
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
