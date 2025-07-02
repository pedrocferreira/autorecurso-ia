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
        Schema::table('credit_transactions', function (Blueprint $table) {
            // Status da transação
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'expired'])
                  ->default('completed')
                  ->after('type');
            
            // Método de pagamento
            $table->enum('payment_method', ['stripe', 'pix', 'boleto', 'admin'])
                  ->nullable()
                  ->after('status');
            
            // Referência externa (ID do Stripe, AbacatePay, etc.)
            $table->string('reference')->nullable()->after('payment_method');
            
            // Data/hora do pagamento
            $table->timestamp('paid_at')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_method', 'reference', 'paid_at']);
        });
    }
};
