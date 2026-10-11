<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CacheService
{
    /**
     * مدة التخزين المؤقت بالدقائق
     */
    const DURATION_SHORT = 5;      // 5 دقائق
    const DURATION_MEDIUM = 30;     // 30 دقيقة  
    const DURATION_LONG = 120;      // 2 ساعة
    const DURATION_DAILY = 1440;    // 24 ساعة

    /**
     * الحصول على بيانات الموظفين مع التخزين المؤقت
     */
    public static function getEmployees(array $filters = []): array
    {
        $cacheKey = 'employees_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::DURATION_MEDIUM, function () use ($filters) {
            $query = DB::table('employees')
                ->select([
                    'employees.id',
                    'employees.name',
                    'employees.email',
                    'employees.mobile',
                    'employees.status',
                    'employees.czn_id',
                    'departments.name as department_name',
                    'sections.name as section_name',
                    'branches.name as branch_name',
                    'jobs.name as job_name'
                ])
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->leftJoin('sections', 'employees.section_id', '=', 'sections.id')
                ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->leftJoin('jobs', 'employees.job_id', '=', 'jobs.id');

            // تطبيق الفلاتر
            if (!empty($filters['department_id'])) {
                $query->where('employees.department_id', $filters['department_id']);
            }
            
            if (!empty($filters['section_id'])) {
                $query->where('employees.section_id', $filters['section_id']);
            }
            
            if (!empty($filters['status'])) {
                $query->where('employees.status', $filters['status']);
            }
            
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('employees.name', 'like', "%{$search}%")
                      ->orWhere('employees.email', 'like', "%{$search}%")
                      ->orWhere('employees.czn_id', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('employees.name')->get()->toArray();
        });
    }

    /**
     * الحصول على بيانات المكلفين مع التخزين المؤقت
     */
    public static function getMokalafs(array $filters = []): array
    {
        $cacheKey = 'mokalafs_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::DURATION_MEDIUM, function () use ($filters) {
            $query = DB::table('mokalafs')
                ->select([
                    'mokalafs.id',
                    'mokalafs.name',
                    'mokalafs.identity_number',
                    'mokalafs.license_number',
                    'mokalafs.mobile',
                    'mokalafs.address',
                    'mokalafs.created_at'
                ]);

            // تطبيق الفلاتر
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('mokalafs.name', 'like', "%{$search}%")
                      ->orWhere('mokalafs.identity_number', 'like', "%{$search}%")
                      ->orWhere('mokalafs.license_number', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('mokalafs.name')->get()->toArray();
        });
    }

    /**
     * الحصول على إحصائيات الطلبات مع التخزين المؤقت
     */
    public static function getRequestStats(): array
    {
        return Cache::remember('request_stats', self::DURATION_SHORT, function () {
            return [
                'total' => DB::table('requests')->count(),
                'pending' => DB::table('requests')
                    ->join('request_statuses', 'requests.status_id', '=', 'request_statuses.id')
                    ->where('request_statuses.name', 'قيد الانتظار')
                    ->count(),
                'urgent' => DB::table('requests')->where('is_urgent', 1)->count(),
                'overdue' => DB::table('requests')
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', now())
                    ->whereDoesntHave('status', function ($q) {
                        $q->where('name', 'مكتمل');
                    })
                    ->count(),
                'today' => DB::table('requests')
                    ->whereDate('created_at', today())
                    ->count(),
                'this_month' => DB::table('requests')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
        });
    }

    /**
     * الحصول على قائمة الطلبات مع التخزين المؤقت
     */
    public static function getRequests(array $filters = []): array
    {
        $cacheKey = 'requests_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::DURATION_SHORT, function () use ($filters) {
            $query = DB::table('requests')
                ->select([
                    'requests.id',
                    'requests.text',
                    'requests.deadline',
                    'requests.is_urgent',
                    'requests.created_at',
                    'request_types.name as request_type_name',
                    'request_statuses.name as status_name',
                    'mokalafs.name as mokalaf_name',
                    'users.name as creator_name'
                ])
                ->leftJoin('request_types', 'requests.request_type_id', '=', 'request_types.id')
                ->leftJoin('request_statuses', 'requests.status_id', '=', 'request_statuses.id')
                ->leftJoin('mokalafs', 'requests.mokalaf_id', '=', 'mokalafs.id')
                ->leftJoin('users', 'requests.created_by', '=', 'users.id');

            // تطبيق الفلاتر
            if (!empty($filters['status_id'])) {
                $query->where('requests.status_id', $filters['status_id']);
            }
            
            if (!empty($filters['request_type_id'])) {
                $query->where('requests.request_type_id', $filters['request_type_id']);
            }
            
            if (!empty($filters['is_urgent'])) {
                $query->where('requests.is_urgent', $filters['is_urgent']);
            }
            
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('requests.text', 'like', "%{$search}%")
                      ->orWhere('mokalafs.name', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('requests.created_at', '>=', $filters['date_from']);
            }
            
            if (!empty($filters['date_to'])) {
                $query->whereDate('requests.created_at', '<=', $filters['date_to']);
            }

            return $query->orderBy('requests.created_at', 'desc')->get()->toArray();
        });
    }

    /**
     * الحصول على التراخيص مع التخزين المؤقت
     */
    public static function getCraftLicenses(array $filters = []): array
    {
        $cacheKey = 'craft_licenses_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::DURATION_MEDIUM, function () use ($filters) {
            $query = DB::table('craft_licenses')
                ->select([
                    'craft_licenses.id',
                    'craft_licenses.file_number',
                    'craft_licenses.status',
                    'craft_licenses.owner_name',
                    'craft_licenses.craft_type',
                    'craft_licenses.registration_date',
                    'craft_licenses.total_debts',
                    'craft_licenses.remaining'
                ]);

            // تطبيق الفلاتر
            if (!empty($filters['status'])) {
                $query->where('craft_licenses.status', $filters['status']);
            }
            
            if (!empty($filters['craft_type'])) {
                $query->where('craft_licenses.craft_type', $filters['craft_type']);
            }
            
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('craft_licenses.file_number', 'like', "%{$search}%")
                      ->orWhere('craft_licenses.owner_name', 'like', "%{$search}%")
                      ->orWhere('craft_licenses.craft_type', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('craft_licenses.registration_date', 'desc')->get()->toArray();
        });
    }

    /**
     * مسح الكاش الخاص بالموظفين
     */
    public static function clearEmployeesCache(): void
    {
        $pattern = 'employees_*';
        $redis = Cache::store('redis');
        
        if (method_exists($redis, 'connection')) {
            $keys = $redis->connection()->keys($pattern);
            if (!empty($keys)) {
                $redis->connection()->del($keys);
            }
        }
    }

    /**
     * مسح الكاش الخاص بالطلبات
     */
    public static function clearRequestsCache(): void
    {
        $patterns = ['requests_*', 'request_stats'];
        $redis = Cache::store('redis');
        
        if (method_exists($redis, 'connection')) {
            foreach ($patterns as $pattern) {
                $keys = $redis->connection()->keys($pattern);
                if (!empty($keys)) {
                    $redis->connection()->del($keys);
                }
            }
        }
    }

    /**
     * مسح جميع الكاشات المتعلقة بالنظام
     */
    public static function clearAllCache(): void
    {
        $patterns = [
            'employees_*',
            'requests_*', 
            'request_stats',
            'mokalafs_*',
            'craft_licenses_*'
        ];
        
        $redis = Cache::store('redis');
        
        if (method_exists($redis, 'connection')) {
            foreach ($patterns as $pattern) {
                $keys = $redis->connection()->keys($pattern);
                if (!empty($keys)) {
                    $redis->connection()->del($keys);
                }
            }
        }
    }

    /**
     * تحديث إحصائيات الطلبات
     */
    public static function refreshRequestStats(): void
    {
        Cache::forget('request_stats');
        self::getRequestStats();
    }
}
