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
        Schema::table('nurses', function (Blueprint $table) {
            $table->text('description_ar')->nullable()->after('is_verified');
            $table->text('description_en')->nullable()->after('description_ar');
            $table->string('work_hours_ar')->nullable()->after('description_en');
            $table->string('work_hours_en')->nullable()->after('work_hours_ar');
            $table->integer('experience_years')->nullable()->after('work_hours_en');
            
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurses', function (Blueprint $table) {
            $table->dropColumn(['description_ar', 'description_en',
             'work_hours_ar', 'work_hours_en', 'experience_years']);
        });
    }
};
