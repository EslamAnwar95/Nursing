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
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('union_number')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->text('address')->nullable();
            $table->enum('is_active', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
