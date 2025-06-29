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
            // Adiciona apenas as colunas que faltam
            if (!Schema::hasColumn('tickets', 'citation_number')) {
                $table->string('citation_number')->nullable()->after('vehicle_renavam');
            }
            if (!Schema::hasColumn('tickets', 'time')) {
                $table->time('time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('tickets', 'location')) {
                $table->text('location')->nullable()->after('time');
            }
            if (!Schema::hasColumn('tickets', 'custom_details')) {
                $table->text('custom_details')->nullable()->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'citation_number')) {
                $table->dropColumn('citation_number');
            }
            if (Schema::hasColumn('tickets', 'time')) {
                $table->dropColumn('time');
            }
            if (Schema::hasColumn('tickets', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('tickets', 'custom_details')) {
                $table->dropColumn('custom_details');
            }
        });
    }
};
