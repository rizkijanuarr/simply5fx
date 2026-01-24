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
            // Drop kolom profit_or_loss
            $table->dropColumn('profit_or_loss');

            // Tambah kolom profit dan rugi
            $table->string('profit')->nullable()->after('hit_id');
            $table->string('rugi')->nullable()->after('profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop kolom profit dan rugi
            $table->dropColumn(['profit', 'rugi']);

            // Restore kolom profit_or_loss
            $table->string('profit_or_loss')->nullable()->after('hit_id');
        });
    }
};
