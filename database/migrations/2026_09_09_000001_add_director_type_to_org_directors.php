<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['department_directors', 'section_directors', 'branch_directors'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('director_type', 20)->default('permanent')->after('employee_id');
                $table->index(['director_type']);
            });
        }
    }

    public function down(): void
    {
        foreach (['department_directors', 'section_directors', 'branch_directors'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex([$tableName . '_director_type_index']);
                $table->dropColumn('director_type');
            });
        }
    }
};
