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
        Schema::table('employee_job_infos', function (Blueprint $table) {
            $table->string('periodic_bonus')->default(0);  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_job_infos', function (Blueprint $table) {
            $table->dropColumn('periodic_bonus');
        });
    }
};
