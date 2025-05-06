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
            $table->dropForeign(['order_id']);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->change();

            $table->string('type')->default('order')->after('provider_type');

            $table->string('gateway_reference')->nullable()->after('provider_earning');

            $table->string('currency')->default('EGP')->after('gateway_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->dropColumn(['type', 'gateway_reference', 'currency']);
            $table->unsignedBigInteger('order_id')->nullable(false)->change();

            
        });
    }
};
