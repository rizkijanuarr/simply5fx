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
        Schema::disableForeignKeyConstraints();

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_trade_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('market_id')->constrained();
            $table->foreignId('risk_to_reward_id')->constrained();
            $table->foreignId('lot_size_id')->constrained();
            $table->foreignId('hit_id')->constrained();
            $table->decimal('harga_entry', 15, 5);
            $table->decimal('harga_sl', 15, 5);
            $table->decimal('harga_tp', 15, 5);
            $table->decimal('account_balance', 15, 2);
            $table->decimal('profit_or_loss', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->decimal('account_change', 15, 2);
            $table->decimal('cummulative_account_change', 15, 2);
            $table->string('screenshot_before')->nullable();
            $table->string('screenshot_after')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
