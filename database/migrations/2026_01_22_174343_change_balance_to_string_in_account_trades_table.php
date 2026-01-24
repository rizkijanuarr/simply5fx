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
        Schema::table('account_trades', function (Blueprint $table) {
            // Change balance from decimal to string
            $table->string('balance')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_trades', function (Blueprint $table) {
            // Revert back to decimal
            $table->decimal('balance', 15, 2)->change();
        });
    }
};
