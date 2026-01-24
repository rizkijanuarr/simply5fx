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
            $table->string('harga_entry')->change();
            $table->string('harga_sl')->change();
            $table->string('harga_tp')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('harga_entry', 15, 5)->change();
            $table->decimal('harga_sl', 15, 5)->change();
            $table->decimal('harga_tp', 15, 5)->change();
        });
    }
};
