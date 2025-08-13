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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar colunas apenas se não existirem
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'cnh_category')) {
                $table->string('cnh_category')->nullable()->after('cpf');
            }
            if (!Schema::hasColumn('users', 'cnh_address')) {
                $table->string('cnh_address')->nullable()->after('cnh_category');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('cnh_address');
            }
            if (!Schema::hasColumn('users', 'premium')) {
                $table->boolean('premium')->default(false)->after('phone');
            }
            if (!Schema::hasColumn('users', 'onboarded')) {
                $table->boolean('onboarded')->default(false)->after('premium');
            }
            if (!Schema::hasColumn('users', 'blocked')) {
                $table->boolean('blocked')->default(false)->after('onboarded');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'avatar',
                'cpf',
                'cnh_category',
                'cnh_address',
                'phone',
                'premium',
                'onboarded',
                'blocked'
            ]);
        });
    }
};

