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
            if (!Schema::hasColumn('tickets', 'organ')) {
                $table->string('organ')->nullable()->after('vehicle_renavam');
            }
            if (!Schema::hasColumn('tickets', 'was_driver')) {
                $table->boolean('was_driver')->default(true)->after('organ');
            }
            if (!Schema::hasColumn('tickets', 'had_signage')) {
                $table->boolean('had_signage')->default(true)->after('was_driver');
            }
            if (!Schema::hasColumn('tickets', 'details')) {
                $table->text('details')->nullable()->after('had_signage');
            }
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
            $table->dropColumn([
                'organ',
                'was_driver',
                'had_signage',
                'details',
                'citation_number',
                'time',
                'location',
                'custom_details'
            ]);
        });
    }
};
