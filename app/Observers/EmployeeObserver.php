<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\CacheService;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // مسح كاش الموظفين عند إنشاء موظف جديد
        CacheService::clearEmployeesCache();
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // مسح كاش الموظفين عند تحديث موظف
        CacheService::clearEmployeesCache();
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // مسح كاش الموظفين عند حذف موظف
        CacheService::clearEmployeesCache();
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        CacheService::clearEmployeesCache();
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        CacheService::clearEmployeesCache();
    }
}
