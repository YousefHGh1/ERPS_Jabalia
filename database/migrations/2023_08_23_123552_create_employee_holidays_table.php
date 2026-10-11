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
        Schema::create('employee_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees') ;
            $table->foreignId('holiday_id')->constrained('holidays') ;
            $table->date('start_date');
            $table->date('end_date');

            $table->integer('vacation_days');
            $table->integer('spent_balance');
            $table->integer('remaining_balance');

            $table->string('note')->nullable();
            $table->text('file')->nullable()->change();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_holidays');
    }
};
