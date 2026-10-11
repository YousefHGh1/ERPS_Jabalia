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
        Schema::create('employee_job_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_number');
            $table->foreignId('employee_id')->constrained('employees') ;
            $table->date('work_start_date');
            $table->date('ministry_date_appointment');
            $table->string('type');
            $table->date('ministry_installation_date')->nullable();
            $table->integer('vacation_days')->nullable();
            $table->string('current_degree')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_job_infos');
    }
};