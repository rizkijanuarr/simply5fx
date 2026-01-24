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
        Schema::table('transactions', function (Blueprint $table) {
            // Change calculated columns from decimal to string
            $table->string('account_balance')->nullable()->change();
            $table->string('profit_or_loss')->nullable()->change();
            $table->string('equity')->nullable()->change();
            $table->string('account_change')->nullable()->change();
            $table->string('cummulative_account_change')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert back to decimal
            $table->decimal('account_balance', 15, 2)->nullable()->change();
            $table->decimal('profit_or_loss', 15, 2)->nullable()->change();
            $table->decimal('equity', 15, 2)->nullable()->change();
            $table->decimal('account_change', 15, 2)->nullable()->change();
            $table->decimal('cummulative_account_change', 15, 2)->nullable()->change();
        });
    }
};
