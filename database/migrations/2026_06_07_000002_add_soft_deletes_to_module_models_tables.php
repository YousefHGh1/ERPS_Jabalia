<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * الصندوق المركزي، قلم الجمهور، الشؤون القانونية، الشؤون الإدارية
     */
    private array $tables = [
        // الصندوق المركزي
        'revenue_batches',
        'payments',

        // قلم الجمهور
        'request_attachments',
        'request_types',
        'request_workflows',

        // الشؤون القانونية (مرفقات)
        'notice_attachments',
        'imprisonment_order_attachments',
        'reservation_attachments',

        // الشؤون الإدارية
        'employees',
        'employee_dependents',
        'employee_bonuses',
        'employee_holidays',
        'employee_permissions',
        'employee_job_infos',
        'employee_job_hestories',
        'employee_main_holidays',
        'employee_job_infos_shift',
        'employee_over_times',
        'departments',
        'sections',
        'branches',
        'jobs',
        'categories',
        'bonuses',
        'shifts',
        'holidays',
        'main_holidays',
        'attachments',
        'vehicles',
        'department_directors',
        'section_directors',
        'branch_directors',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
