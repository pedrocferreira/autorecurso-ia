<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('credits');
            $table->decimal('price', 10, 2);
            $table->integer('discount')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('recommended')->default(false);
            $table->timestamps();
        });

        // Inserir pacotes padrão
        DB::table('credit_packages')->insert([
            [
                'name' => '10 Créditos',
                'credits' => 10,
                'price' => 35.00,
                'discount' => 0,
                'active' => true,
                'recommended' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '30 Créditos',
                'credits' => 30,
                'price' => 95.00,
                'discount' => 10,
                'active' => true,
                'recommended' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '50 Créditos',
                'credits' => 50,
                'price' => 150.00,
                'discount' => 15,
                'active' => true,
                'recommended' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '100 Créditos',
                'credits' => 100,
                'price' => 280.00,
                'discount' => 20,
                'active' => true,
                'recommended' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('credit_packages');
    }
}; 