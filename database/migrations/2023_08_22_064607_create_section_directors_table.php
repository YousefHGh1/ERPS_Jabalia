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
        Schema::create('section_directors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees') ;
            $table->foreignId('section_id')->nullable()->constrained('sections') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_directors');
    }
};