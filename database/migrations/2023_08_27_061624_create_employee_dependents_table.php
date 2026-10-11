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
        Schema::create('employee_dependents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('czn_id');
            $table->string('gender');
            $table->date('birth');
            $table->string('relation');
            $table->string('status');
            $table->string('attachment')->nullable();
            $table->foreignId('employee_id')->constrained('employees') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_dependents');
    }
};
