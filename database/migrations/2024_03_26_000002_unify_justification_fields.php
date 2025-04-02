<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Primeiro, mover os dados do campo reason para client_justification
        DB::table('tickets')->whereNotNull('reason')->update([
            'client_justification' => DB::raw('reason')
        ]);

        // Depois, remover a coluna reason
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }

    public function down()
    {
        // Recriar a coluna reason
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('reason')->nullable();
        });

        // Restaurar os dados de client_justification para reason
        DB::table('tickets')->whereNotNull('client_justification')->update([
            'reason' => DB::raw('client_justification')
        ]);
    }
}; 