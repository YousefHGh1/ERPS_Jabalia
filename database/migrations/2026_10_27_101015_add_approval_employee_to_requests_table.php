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
        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('approval_employee_id')->nullable()->after('updated_by')->constrained('employees')->onDelete('set null');
            $table->string('approval_employee_type', 20)->default('primary')->after('approval_employee_id')->comment('primary=أصلي, substitute=بديل');
            $table->text('approval_substitution_reason')->nullable()->after('approval_employee_type')->comment('سبب اختيار موظف بديل');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['approval_employee_id']);
            $table->dropColumn(['approval_employee_id', 'approval_employee_type', 'approval_substitution_reason']);
        });
    }
};
