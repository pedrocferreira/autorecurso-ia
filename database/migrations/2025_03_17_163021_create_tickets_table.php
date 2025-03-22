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
            // Dados pessoais
            $table->string('name');
            $table->string('cpf', 14);
            $table->string('driver_license');
            $table->string('driver_license_category');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            
            // Dados do veículo
            $table->string('plate');
            $table->string('vehicle_model');
            $table->integer('vehicle_year');
            $table->string('vehicle_color');
            $table->string('vehicle_chassi', 17);
            $table->string('vehicle_renavam', 11);
            
            // Dados da multa
            $table->string('date');
            $table->decimal('amount', 10, 2);
            $table->integer('points');
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
