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
            if (!Schema::hasColumn('tickets', 'points')) {
                $table->integer('points')->after('amount');
            }
            if (!Schema::hasColumn('tickets', 'status')) {
                $table->string('status')->default('pending')->after('points');
            }
            if (!Schema::hasColumn('tickets', 'vehicle_color')) {
                $table->string('vehicle_color')->after('vehicle_year');
            }
            if (!Schema::hasColumn('tickets', 'vehicle_chassi')) {
                $table->string('vehicle_chassi', 17)->after('vehicle_color');
            }
            if (!Schema::hasColumn('tickets', 'vehicle_renavam')) {
                $table->string('vehicle_renavam', 11)->after('vehicle_chassi');
            }
            if (!Schema::hasColumn('tickets', 'driver_license_category')) {
                $table->string('driver_license_category')->after('driver_license');
            }
            if (!Schema::hasColumn('tickets', 'phone')) {
                $table->string('phone')->after('address');
            }
            if (!Schema::hasColumn('tickets', 'email')) {
                $table->string('email')->after('phone');
            }
            if (!Schema::hasColumn('tickets', 'cpf')) {
                $table->string('cpf', 14)->after('name');
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
                'points',
                'status',
                'vehicle_color',
                'vehicle_chassi',
                'vehicle_renavam',
                'driver_license_category',
                'phone',
                'email',
                'cpf'
            ]);
        });
    }
};
