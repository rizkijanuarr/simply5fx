<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_trades', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('risk_to_rewards', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('lot_sizes', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('hits', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('account_trades', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('risk_to_rewards', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('lot_sizes', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('hits', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }
};
