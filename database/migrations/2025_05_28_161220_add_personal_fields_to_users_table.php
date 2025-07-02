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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->unique()->nullable()->after('email'); // CPF único, após o email
            $table->string('cnh_category')->nullable()->after('cpf');      // Categoria da CNH
            $table->text('cnh_address')->nullable()->after('cnh_category'); // Endereço da CNH (campo de texto longo)
            $table->string('phone')->nullable()->after('cnh_address');        // Telefone
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'cnh_category', 'cnh_address', 'phone']);
        });
    }
};
