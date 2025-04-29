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
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->enum('settlement_status', ['pending', 'settled', 'failed'])->default('pending')->after('status');
            $table->timestamp('settled_at')->nullable()->after('settlement_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->dropColumn('settlement_status');
            $table->dropColumn('settled_at');
        });
    }
};
