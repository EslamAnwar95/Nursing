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
        Schema::create('nurse_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->text('patient_condition')->nullable();

            $table->foreignId('nurse_hours_id')->constrained('nurse_hours')->cascadeOnDelete();

            $table->dateTime('visit_date')->nullable();
          
            $table->enum('visit_time', ['morning', 'evening'])->default('morning');

            $table->text('other_nurse_notes')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_order_details');
    }
};
