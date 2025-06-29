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
            // Adiciona campos específicos para cidade e estado
            if (!Schema::hasColumn('tickets', 'city')) {
                $table->string('city')->nullable()->after('location');
            }
            if (!Schema::hasColumn('tickets', 'state')) {
                $table->string('state', 2)->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('tickets', 'state')) {
                $table->dropColumn('state');
            }
        });
    }
}; 