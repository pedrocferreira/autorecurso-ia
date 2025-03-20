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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plate', 10); // Placa do veículo
            $table->date('date'); // Data da multa
            $table->string('location'); // Local da infração
            $table->text('reason'); // Motivo/descrição da multa
            $table->decimal('amount', 10, 2); // Valor da multa
            $table->string('citation_number')->nullable(); // Número da autuação/multa
            $table->string('vehicle_model')->nullable(); // Modelo do veículo
            $table->string('vehicle_year')->nullable(); // Ano do veículo
            $table->string('driver_license')->nullable(); // CNH do motorista
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
