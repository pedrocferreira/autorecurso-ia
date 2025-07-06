<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->json('form_data');
            $table->unsignedBigInteger('transaction_id')->nullable(); // referência à credit_transactions
            $table->string('status')->default('pending'); // pending | consumed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_drafts');
    }
}; 