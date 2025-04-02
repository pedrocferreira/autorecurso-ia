<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn('payment_id');
        });
    }
}; 