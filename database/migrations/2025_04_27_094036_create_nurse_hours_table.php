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
        Schema::create('nurse_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurse_id')->constrained('nurses')->onDelete('cascade');
            $table->integer('hours');
            $table->integer('price');
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])->default('saturday');
            $table->string('additional_hours')->nullable();
            $table->string('additional_price')->nullable();
            $table->enum('time', ['morning', 'evening'])->default('morning');
            $table->enum('status', ['active', 'inactive'])->default('active');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_hours');
    }
};
