<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah SKU
            $table->string('sku')->unique()->after('id');

            // Ubah ke nullable
            $table->unsignedBigInteger('hit_id')->nullable()->change();
            $table->decimal('account_balance', 15, 2)->nullable()->change();
            $table->decimal('profit_or_loss', 15, 2)->nullable()->change();
            $table->decimal('equity', 15, 2)->nullable()->change();
            $table->decimal('account_change', 15, 2)->nullable()->change();
            $table->decimal('cummulative_account_change', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('sku');

            // Kembalikan ke NOT NULL (opsional)
            $table->unsignedBigInteger('hit_id')->nullable(false)->change();
            $table->decimal('account_balance', 15, 2)->nullable(false)->change();
            $table->decimal('profit_or_loss', 15, 2)->nullable(false)->change();
            $table->decimal('equity', 15, 2)->nullable(false)->change();
            $table->decimal('account_change', 15, 2)->nullable(false)->change();
            $table->decimal('cummulative_account_change', 15, 2)->nullable(false)->change();
        });
    }
};
