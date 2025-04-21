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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('password');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('national_id')->nullable();
            $table->text('address')->nullable();
            $table->text('medical_history')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
