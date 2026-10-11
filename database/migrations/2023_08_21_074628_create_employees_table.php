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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('czn_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('gender');
            $table->string('status');
            $table->string('address');
            $table->integer('mobile');
            $table->date('birthdate');
            $table->string('birthplace');
            $table->integer('salary');
            $table->foreignId('department_id')->nullable()->constrained('departments') ;
            $table->foreignId('section_id')->nullable()->constrained('sections') ;
            $table->foreignId('branch_id')->nullable()->constrained('branches') ;
            $table->foreignId('job_id')->constrained('jobs') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};