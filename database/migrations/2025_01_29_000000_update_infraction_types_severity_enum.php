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
        // SQLite não suporta MODIFY, então vamos recrear a tabela
        Schema::table('infraction_types', function (Blueprint $table) {
            // No SQLite, vamos apenas aceitar qualquer string no campo severity
            // O validation será feito no model/controller
            $table->string('severity')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há necessidade de rollback específico para SQLite
        // O campo já será string
    }
}; 