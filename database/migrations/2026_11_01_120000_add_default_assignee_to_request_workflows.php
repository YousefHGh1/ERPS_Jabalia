<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('default_assigned_employee_id')
                ->nullable()
                ->after('department_id')
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_assigned_employee_id');
        });
    }
};
