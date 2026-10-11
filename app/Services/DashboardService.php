<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * الحصول على إحصائيات الشؤون الإدارية
     */
    public function getAdministrativeStats(): array
    {
        return Cache::remember('dashboard_administrative_stats', 3600, function () {
            return [
                'employees_count' => DB::table('employees')->count(),
                'today_attendance' => DB::table('z_k_data')
                    ->whereDate('arrival_time', today())
                    ->where('state', 1)
                    ->count(),
                'pending_holidays' => DB::table('employee_holidays')
                    ->where('status', 'pending')
                    ->count(),
                'pending_permissions' => DB::table('employee_permissions')
                    ->where('status', 'pending')
                    ->count(),
                'jobs_count' => DB::table('jobs')->count(),
                'vehicles_count' => DB::table('vehicles')->count(),
                'departments_count' => DB::table('departments')->count(),
                'sections_count' => DB::table('sections')->count(),
            ];
        });
    }

    /**
     * الحصول على آخر الموظفين المسجلين
     */
    public function getLatestEmployees(int $limit = 5)
    {
        return Cache::remember('dashboard_latest_employees', 1800, function () use ($limit) {
            return DB::table('employees')
                ->join('jobs', 'employees.job_id', '=', 'jobs.id')
                ->select('employees.*', 'jobs.name as job_name')
                ->orderBy('employees.created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * الحصول على آخر طلبات الإجازات
     */
    public function getLatestHolidays(int $limit = 5)
    {
        return Cache::remember('dashboard_latest_holidays', 1800, function () use ($limit) {
            return DB::table('employee_holidays')
                ->join('employees', 'employee_holidays.employee_id', '=', 'employees.id')
                ->join('holidays', 'employee_holidays.holiday_id', '=', 'holidays.id')
                ->select('employee_holidays.*', 'employees.name as employee_name', 'employees.czn_id', 'holidays.name as holiday_name')
                ->orderBy('employee_holidays.created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * الحصول على بيانات الحضور للأيام السبعة الماضية
     */
    public function getAttendanceData(int $days = 7)
    {
        return Cache::remember("dashboard_attendance_data_{$days}", 1800, function () use ($days) {
            return DB::table('z_k_data')
                ->select(
                    DB::raw('DATE(arrival_time) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(late_minutes) as total_late')
                )
                ->where('arrival_time', '>=', now()->subDays($days))
                ->where('state', 1)
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });
    }

    /**
     * الحصول على توزيع الموظفين على الإدارات
     */
    public function getDepartmentDistribution()
    {
        return Cache::remember('dashboard_dept_distribution', 3600, function () {
            return DB::table('employees')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->select('departments.name', DB::raw('COUNT(*) as employees_count'))
                ->groupBy('departments.id', 'departments.name')
                ->get();
        });
    }

    /**
     * الحصول على جميع بيانات dashboard الإدارية
     */
    public function getAdministrativeDashboardData(): array
    {
        return [
            'stats' => $this->getAdministrativeStats(),
            'latest_employees' => $this->getLatestEmployees(),
            'latest_holidays' => $this->getLatestHolidays(),
            'attendance_data' => $this->getAttendanceData(),
            'dept_distribution' => $this->getDepartmentDistribution(),
        ];
    }

    /**
     * الحصول على إحصائيات الحرف والصناعات
     */
    public function getCraftsStats(): array
    {
        return Cache::remember('dashboard_crafts_stats', 3600, function () {
            return [
                'craft_licenses_count' => DB::table('craft_licenses')->count(),
                'craft_payments_count' => DB::table('payments')->where('status', 'active')->count(),
                'craft_revenue' => DB::table('payments')->where('status', 'active')->sum('amount'),
                'unpaid_licenses' => DB::table('craft_licenses')
                    ->where('remaining', '>', 0)
                    ->count(),
            ];
        });
    }

    /**
     * الحصول على آخر رخص الحرف والصناعات
     */
    public function getLatestCraftLicenses(int $limit = 5)
    {
        return Cache::remember('dashboard_latest_craft_licenses', 1800, function () use ($limit) {
            return DB::table('craft_licenses')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * الحصول على توزيع الرخص حسب الفئة
     */
    public function getCraftCategoryData()
    {
        return Cache::remember('dashboard_craft_category_data', 3600, function () {
            return DB::table('craft_licenses')
                ->select('craft_type', DB::raw('COUNT(*) as count'))
                ->groupBy('craft_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->craft_type,
                        'count' => $item->count
                    ];
                });
        });
    }

    /**
     * الحصول على توزيع الرخص حسب الحالة
     */
    public function getCraftStatusData()
    {
        return Cache::remember('dashboard_craft_status_data', 3600, function () {
            return DB::table('craft_licenses')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => __('craft.status_' . $item->status),
                        'count' => $item->count
                    ];
                });
        });
    }

    /**
     * الحصول على جميع بيانات dashboard الحرف والصناعات
     */
    public function getCraftsDashboardData(): array
    {
        return [
            'stats' => $this->getCraftsStats(),
            'latest_craft_licenses' => $this->getLatestCraftLicenses(),
            'craft_category_data' => $this->getCraftCategoryData(),
            'craft_status_data' => $this->getCraftStatusData(),
        ];
    }

    /**
     * الحصول على إحصائيات الهندسة والتنظيم
     */
    public function getEngineeringStats(): array
    {
        return Cache::remember('dashboard_engineering_stats', 3600, function () {
            return [
                'engineering_count' => DB::table('engineerings')->count(),
                'engineering_payments_count' => DB::table('payment_engineerings')->count(),
                'engineering_revenue' => DB::table('payment_engineerings')->sum('amount'),
                'pending_payments' => DB::table('engineerings')
                    ->where('remaining_amount', '>', 0)
                    ->count(),
            ];
        });
    }

    /**
     * الحصول على آخر ملفات الهندسة
     */
    public function getLatestEngineeringFiles(int $limit = 5)
    {
        return Cache::remember('dashboard_latest_engineering_files', 1800, function () use ($limit) {
            return DB::table('engineerings')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * الحصول على توزيع الملفات حسب الحالة
     */
    public function getEngineeringStatusData()
    {
        return Cache::remember('dashboard_engineering_status_data', 3600, function () {
            return DB::table('engineerings')
                ->select(
                    DB::raw('CASE 
                        WHEN remaining_amount = 0 THEN "paid"
                        WHEN paid_amount > 0 THEN "partial"
                        ELSE "pending"
                    END as status'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    return [
                        'status' => __('engineering.status_' . $item->status),
                        'count' => $item->count
                    ];
                });
        });
    }

    /**
     * الحصول على الإيرادات الشهرية
     */
    public function getEngineeringRevenueByMonth(int $months = 6)
    {
        return Cache::remember("dashboard_engineering_revenue_{$months}", 3600, function () use ($months) {
            return DB::table('payment_engineerings')
                ->select(
                    DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as amount')
                )
                ->where('payment_date', '>=', now()->subMonths($months))
                ->where('status', 'active')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => date('Y/m', strtotime($item->month)),
                        'amount' => $item->amount
                    ];
                });
        });
    }

    /**
     * الحصول على جميع بيانات dashboard الهندسة والتنظيم
     */
    public function getEngineeringDashboardData(): array
    {
        return [
            'stats' => $this->getEngineeringStats(),
            'latest_engineering_files' => $this->getLatestEngineeringFiles(),
            'engineering_status_data' => $this->getEngineeringStatusData(),
            'revenue_by_month' => $this->getEngineeringRevenueByMonth(),
        ];
    }

    /**
     * مسح جميع كاشات Dashboard
     */
    public function clearDashboardCache(): void
    {
        $cacheKeys = [
            'dashboard_administrative_stats',
            'dashboard_latest_employees',
            'dashboard_latest_holidays',
            'dashboard_attendance_data_7',
            'dashboard_dept_distribution',
            'dashboard_crafts_stats',
            'dashboard_latest_craft_licenses',
            'dashboard_craft_category_data',
            'dashboard_craft_status_data',
            'dashboard_engineering_stats',
            'dashboard_latest_engineering_files',
            'dashboard_engineering_status_data',
            'dashboard_engineering_revenue_6',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * مسح كاش محدد
     */
    public function clearSpecificCache(string $type): void
    {
        $cacheKeys = [
            'administrative' => [
                'dashboard_administrative_stats',
                'dashboard_latest_employees',
                'dashboard_latest_holidays',
                'dashboard_attendance_data_7',
                'dashboard_dept_distribution',
            ],
            'crafts' => [
                'dashboard_crafts_stats',
                'dashboard_latest_craft_licenses',
                'dashboard_craft_category_data',
                'dashboard_craft_status_data',
            ],
            'engineering' => [
                'dashboard_engineering_stats',
                'dashboard_latest_engineering_files',
                'dashboard_engineering_status_data',
                'dashboard_engineering_revenue_6',
            ],
        ];

        if (isset($cacheKeys[$type])) {
            foreach ($cacheKeys[$type] as $key) {
                Cache::forget($key);
            }
        }
    }
}
