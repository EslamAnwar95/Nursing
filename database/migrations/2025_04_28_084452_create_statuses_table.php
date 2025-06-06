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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->boolean('is_active')->default(true);  
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();     
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();     
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
