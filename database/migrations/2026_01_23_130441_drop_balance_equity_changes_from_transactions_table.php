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
            $table->dropColumn([
                'account_balance',
                'equity',
                'account_change',
                'cummulative_account_change',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('account_balance')->nullable()->after('hit_id');
            $table->string('equity')->nullable()->after('rugi');
            $table->string('account_change')->nullable()->after('equity');
            $table->string('cummulative_account_change')->nullable()->after('account_change');
        });
    }
};
