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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->morphs('provider'); 
            $table->enum('status', ['pending', 'accepted','in_progress', 'completed', 'canceled'])->default('pending');

            $table->timestamp('scheduled_at')->nullable(); 
            $table->decimal('price', 10, 2)->nullable(); 

            $table->text('notes')->nullable(); 

            $table->timestamps();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
