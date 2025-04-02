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
            $table->string('payment_method')->nullable()->after('payment_id');
            $table->string('status')->default('pending')->after('payment_method');
            $table->decimal('price', 10, 2)->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'payment_method', 'status', 'price']);
        });
    }
}; 