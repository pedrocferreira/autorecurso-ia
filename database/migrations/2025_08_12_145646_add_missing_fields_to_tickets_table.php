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
        Schema::table('tickets', function (Blueprint $table) {
            // Campos para identificação da multa
            if (!Schema::hasColumn('tickets', 'citation_number')) {
                $table->string('citation_number', 50)->nullable()->after('id');
            }
            
            // Campo para fonte da multa (API, manual, etc.)
            if (!Schema::hasColumn('tickets', 'source')) {
                $table->string('source', 50)->default('manual')->after('citation_number');
            }
            
            // Campo para horário da infração
            if (!Schema::hasColumn('tickets', 'time')) {
                $table->time('time')->nullable()->after('date');
            }
            
            // Campos para informações legais
            if (!Schema::hasColumn('tickets', 'article')) {
                $table->string('article', 100)->nullable()->after('reason');
            }
            
            if (!Schema::hasColumn('tickets', 'infraction_code')) {
                $table->string('infraction_code', 20)->nullable()->after('article');
            }
            
            // Campos para localização
            if (!Schema::hasColumn('tickets', 'city')) {
                $table->string('city', 100)->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('tickets', 'state')) {
                $table->string('state', 2)->nullable()->after('city');
            }
            
            // Campo para órgão autuador
            if (!Schema::hasColumn('tickets', 'orgao_autuador')) {
                $table->string('orgao_autuador', 100)->nullable()->after('state');
            }
            
            // Campo para local da infração (se não existir)
            if (!Schema::hasColumn('tickets', 'location')) {
                $table->string('location', 255)->nullable()->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'citation_number',
                'source',
                'time',
                'article',
                'infraction_code',
                'city',
                'state',
                'orgao_autuador',
                'location'
            ]);
        });
    }
};
