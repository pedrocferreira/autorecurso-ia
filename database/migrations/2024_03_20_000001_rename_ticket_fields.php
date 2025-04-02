<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Renomear campos existentes
            $table->renameColumn('driver_license', 'cnh_number');
            $table->renameColumn('driver_license_category', 'cnh_category');
            $table->renameColumn('plate', 'vehicle_plate');
            $table->renameColumn('date', 'infraction_date');
            $table->renameColumn('amount', 'infraction_amount');
            $table->renameColumn('points', 'infraction_points');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Reverter renomeação dos campos
            $table->renameColumn('cnh_number', 'driver_license');
            $table->renameColumn('cnh_category', 'driver_license_category');
            $table->renameColumn('vehicle_plate', 'plate');
            $table->renameColumn('infraction_date', 'date');
            $table->renameColumn('infraction_amount', 'amount');
            $table->renameColumn('infraction_points', 'points');
        });
    }
}; 