<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscription_active')) {
                $table->boolean('subscription_active')->default(false)->after('blocked');
            }
            if (!Schema::hasColumn('users', 'subscription_ends_at')) {
                $table->dateTime('subscription_ends_at')->nullable()->after('subscription_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscription_active')) {
                $table->dropColumn('subscription_active');
            }
            if (Schema::hasColumn('users', 'subscription_ends_at')) {
                $table->dropColumn('subscription_ends_at');
            }
        });
    }
};


