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
        Schema::table('employee_holidays', function (Blueprint $table) {
            //
            $table->foreignId('for_employee')->default(1)->constrained('employees') ;

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_holidays', function (Blueprint $table) {
            //
            $table->dropColumn('for_employee');
        });
    }
};