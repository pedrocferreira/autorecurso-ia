<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Adicionar novos campos
            $table->date('birth_date')->after('email')->nullable();
            $table->date('cnh_expiration')->after('cnh_category')->nullable();
            $table->string('vehicle_brand', 100)->after('vehicle_renavam')->nullable();
            $table->string('vehicle_owner')->after('vehicle_color')->nullable();
            $table->string('infraction_location')->after('infraction_date')->nullable();
            $table->string('infraction_agent', 100)->after('infraction_location')->nullable();
            $table->string('infraction_equipment', 100)->after('infraction_agent')->nullable();
            $table->string('infraction_observation')->after('infraction_equipment')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Remover campos adicionados
            $table->dropColumn([
                'birth_date',
                'cnh_expiration',
                'vehicle_brand',
                'vehicle_owner',
                'infraction_location',
                'infraction_agent',
                'infraction_equipment',
                'infraction_observation'
            ]);
        });
    }
}; 