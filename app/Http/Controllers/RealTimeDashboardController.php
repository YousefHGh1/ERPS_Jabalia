<?php

namespace App\Http\Controllers;

use App\Models\CraftLicense;
use App\Models\Employee;
use App\Models\EmployeeHoliday;
use App\Models\EmployeePermission;
use App\Models\Engineering;
use App\Models\ImprisonmentOrder;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\facades\Cache;
use Illuminate\Support\facades\DB;
use Illuminate\Support\facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RealTimeDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:index real_time_dashboards')->only(['index', 'getRealTimeStats', 'getLiveUpdates']);
    }

    public function index()
    {
        return view('dashboard.realtime');
    }

    public function getRealTimeStats()
    {
        // Prevent cache stampede under high traffic right after cache expiry.
        $cacheKey = 'dashboard_stats';
        $lockKey = 'dashboard_stats_lock';
        $ttlSeconds = 300;
        $lockSeconds = 15;

        // Fast path
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // If someone else is recomputing, avoid forcing all requests to do heavy work.
        if (Cache::has($lockKey)) {
            return response()->json($this->getRealTimeStatsFallback());
        }

        Cache::put($lockKey, 1, $lockSeconds);

        try {
            // Recompute once, cache for the usual TTL
            $stats = Cache::remember($cacheKey, $ttlSeconds, function () {
                $helpers = [
                    'employees' => fn () => $this->getEmployeeStats(),
                    'requests' => fn () => $this->getRequestStats(),
                    'crafts' => fn () => $this->getCraftStats(),
                    'engineering' => fn () => $this->getEngineeringStats(),
                    'holidays' => fn () => $this->getHolidayStats(),
                    'notifications' => fn () => $this->getNotificationStats(),
                    'reservations' => fn () => $this->getReservationStats(),
                    'notices' => fn () => $this->getNoticeStats(),
                    'imprisonment' => fn () => $this->getImprisonmentStats(),
                    'heatmap' => fn () => $this->getHeatmapData(),
                    'timeline' => fn () => $this->getTimelineData(),
                    'system_alerts' => fn () => $this->getSystemAlerts(),
                ];

                $stats = [];
                $emptyCounts = [
                    'total' => 0,
                    'pending' => 0,
                    'active' => 0,
                    'new_today' => 0,
                    'unread' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'present_today' => 0,
                    'attendance_rate' => 0,
                    'completion_rate' => 0,
                    'revenue' => 0,
                    'expired' => 0,
                    'renewal_pending' => 0,
                    'pending_holidays' => 0,
                    'pending_permissions' => 0,
                    'approved_today' => 0,
                    'completed' => 0,
                    'pending_payments' => 0,
                    'total_revenue' => 0,
                    'new_this_month' => 0,
                    'urgent' => 0,
                ];

                foreach ($helpers as $key => $callable) {
                    try {
                        $stats[$key] = $callable();
                    } catch (\Throwable $e) {
                        Log::warning('RealTimeDashboard stats failed: ' . $key, ['message' => $e->getMessage()]);
                        $stats[$key] = in_array($key, ['heatmap', 'timeline', 'system_alerts'], true) ? [] : $emptyCounts;
                    }
                }

                return $stats;
            });

            return response()->json($stats);
        } finally {
            // Let the TTL do its job; also attempt to release lock early.
            Cache::forget($lockKey);
        }
    }

    private function getRealTimeStatsFallback(): array
    {
        return [
            'employees' => ['total' => 0, 'present_today' => 0, 'active' => 0, 'new_this_month' => 0, 'attendance_rate' => 0],
            'requests' => ['total' => 0, 'pending' => 0, 'urgent' => 0, 'new_today' => 0, 'completion_rate' => 0],
            'crafts' => ['total' => 0, 'active' => 0, 'expired' => 0, 'renewal_pending' => 0, 'revenue' => 0],
            'engineering' => ['total' => 0, 'active' => 0, 'completed' => 0, 'pending_payments' => 0, 'total_revenue' => 0],
            'holidays' => ['pending_holidays' => 0, 'pending_permissions' => 0, 'approved_today' => 0],
            'notifications' => ['total' => 0, 'unread' => 0, 'new_today' => 0],
            'reservations' => ['total' => 0, 'today' => 0, 'this_week' => 0],
            'notices' => ['total' => 0, 'new_today' => 0],
            'imprisonment' => ['total' => 0, 'new_today' => 0],
            'heatmap' => [],
            'timeline' => [],
            'system_alerts' => [],
        ];
    }

    private function getEmployeeStats()
    {
        return Cache::remember('dashboard_employee_stats', 300, function () {
            $totalEmployees = Employee::count();

            $presentToday = (int) DB::table('z_k_data')
                ->whereDate('arrival_time', Carbon::today())
                ->selectRaw('count(distinct emp_id) as c')
                ->value('c');

            $activeEmployees = Employee::where('status', 'active')->count();
            $newEmployeesThisMonth = Employee::whereMonth('created_at', Carbon::now()->month)->count();

            return [
                'total' => $totalEmployees,
                'present_today' => $presentToday,
                'active' => $activeEmployees,
                'new_this_month' => $newEmployeesThisMonth,
                'attendance_rate' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0,
            ];
        });
    }

    private function getRequestStats()
    {
        return Cache::remember('dashboard_request_stats', 300, function () {
            $totalRequests = RequestModel::count();

            $pendingStatusId = \App\Models\RequestStatus::where('name', 'قيد الانتظار')->first()?->id;
            $pendingRequests = $pendingStatusId ? RequestModel::where('status_id', $pendingStatusId)->count() : 0;
            $urgentRequests = RequestModel::where('is_urgent', true)->count();
            $newToday = RequestModel::whereDate('created_at', Carbon::today())->count();

            return [
                'total' => $totalRequests,
                'pending' => $pendingRequests,
                'urgent' => $urgentRequests,
                'new_today' => $newToday,
                'completion_rate' => $totalRequests > 0 ? round((($totalRequests - $pendingRequests) / $totalRequests) * 100, 1) : 0,
            ];
        });
    }

    private function getCraftStats()
    {
        return Cache::remember('dashboard_craft_stats', 300, function () {
            $totalLicenses = CraftLicense::count();
            $activeLicenses = CraftLicense::where('status', 'active')->count();

            return [
                'total' => $totalLicenses,
                'active' => $activeLicenses,
                'expired' => 0,
                'renewal_pending' => 0,
                'revenue' => (float) (DB::table('payments')->sum('amount') ?? 0),
            ];
        });
    }

    private function getEngineeringStats()
    {
        return Cache::remember('dashboard_engineering_stats', 300, function () {
            $totalFiles = Engineering::count();
            $pendingPayments = Engineering::where('remaining_amount', '>', 0)->count();
            $completedFiles = Engineering::where('remaining_amount', '<=', 0)->count();

            return [
                'total' => $totalFiles,
                'active' => $totalFiles,
                'completed' => $completedFiles,
                'pending_payments' => $pendingPayments,
                'total_revenue' => (float) (DB::table('payment_engineerings')->sum('amount') ?? 0),
            ];
        });
    }

    private function getHolidayStats()
    {
        return Cache::remember('dashboard_holiday_stats', 300, function () {
            $pendingHolidays = EmployeeHoliday::where('status', 'pending')->count();
            $pendingPermissions = EmployeePermission::where('status', 'pending')->count();
            $approvedToday = EmployeeHoliday::where('status', 'accepted')
                ->whereDate('updated_at', Carbon::today())
                ->count();

            return [
                'pending_holidays' => $pendingHolidays,
                'pending_permissions' => $pendingPermissions,
                'approved_today' => $approvedToday,
            ];
        });
    }

    private function getNotificationStats()
    {
        return Cache::remember('dashboard_notification_stats', 300, function () {
            $totalNotifications = Notification::count();
            $unreadNotifications = Notification::whereNull('read_at')->count();
            $newToday = Notification::whereDate('created_at', Carbon::today())->count();

            return [
                'total' => $totalNotifications,
                'unread' => $unreadNotifications,
                'new_today' => $newToday,
            ];
        });
    }

    private function getReservationStats()
    {
        return Cache::remember('dashboard_reservation_stats', 300, function () {
            $total = Reservation::count();
            $today = Reservation::whereDate('reservation_date', Carbon::today())->count();
            $thisWeek = Reservation::whereBetween('reservation_date', [Carbon::today(), Carbon::today()->endOfWeek()])->count();

            return [
                'total' => $total,
                'today' => $today,
                'this_week' => $thisWeek,
            ];
        });
    }

    private function getNoticeStats()
    {
        return Cache::remember('dashboard_notice_stats', 300, function () {
            $total = Notice::count();
            $newToday = Notice::whereDate('created_at', Carbon::today())->count();

            return [
                'total' => $total,
                'new_today' => $newToday,
            ];
        });
    }

    private function getImprisonmentStats()
    {
        return Cache::remember('dashboard_imprisonment_stats', 300, function () {
            $total = ImprisonmentOrder::count();
            $newToday = ImprisonmentOrder::whereDate('created_at', Carbon::today())->count();

            return [
                'total' => $total,
                'new_today' => $newToday,
            ];
        });
    }

    private function getHeatmapData()
    {
        return Cache::remember('dashboard_heatmap_data', 300, function () {
            $departments = DB::table('departments')
                ->leftJoin('employees', 'departments.id', '=', 'employees.department_id')
                ->select('departments.name as area', DB::raw('COUNT(employees.id) as count'))
                ->groupBy('departments.id', 'departments.name')
                ->get();

            return $departments->map(function ($dept) {
                return [
                    'area' => $dept->area ?? '---',
                    'count' => (int) $dept->count,
                    'intensity' => min((int) $dept->count * 10, 100),
                ];
            });
        });
    }

    private function getTimelineData()
    {
        // Keep timeline relatively cheap.
        // (This is called only after cache miss for the whole payload.)
        $items = collect();

        RequestModel::with(['creator:id,name'])
            ->select(['id', 'text', 'created_at', 'creator_id', 'is_urgent'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($request) use ($items) {
                $items->push([
                    'type' => 'request',
                    'title' => 'طلب جديد',
                    'description' => Str::limit($request->text ?? '', 50),
                    'user' => $request->creator->name ?? 'النظام',
                    'time' => $request->created_at?->diffForHumans(),
                    'sort_ts' => $request->created_at?->timestamp ?? 0,
                    'priority' => $request->is_urgent ? 'high' : 'normal',
                ]);
            });

        EmployeeHoliday::with(['employee:id,name'])
            ->select(['id', 'employee_id', 'start_date', 'end_date', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($holiday) use ($items) {
                $items->push([
                    'type' => 'holiday',
                    'title' => 'طلب إجازة',
                    'description' => ($holiday->employee->name ?? '') . ' - ' . ($holiday->start_date ?? '') . ' إلى ' . ($holiday->end_date ?? ''),
                    'user' => $holiday->employee->name ?? '---',
                    'time' => $holiday->created_at?->diffForHumans(),
                    'sort_ts' => $holiday->created_at?->timestamp ?? 0,
                    'priority' => 'normal',
                ]);
            });

        Reservation::select(['id', 'reservation_number', 'subject', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($reservation) use ($items) {
                $items->push([
                    'type' => 'reservation',
                    'title' => 'حجز جديد',
                    'description' => ($reservation->reservation_number ?? '') . ' - ' . ($reservation->subject ?? ''),
                    'user' => 'النظام',
                    'time' => $reservation->created_at?->diffForHumans(),
                    'sort_ts' => $reservation->created_at?->timestamp ?? 0,
                    'priority' => 'normal',
                ]);
            });

        Notice::select(['id', 'notice_number', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($notice) use ($items) {
                $items->push([
                    'type' => 'notice',
                    'title' => 'إخطار جديد',
                    'description' => $notice->notice_number ?? '---',
                    'user' => 'النظام',
                    'time' => $notice->created_at?->diffForHumans(),
                    'sort_ts' => $notice->created_at?->timestamp ?? 0,
                    'priority' => 'normal',
                ]);
            });

        return $items->sortByDesc('sort_ts')->values();
    }

    public function getLiveUpdates()
    {
        return response()->json([
            'timestamp' => now()->toISOString(),
            'updates' => [
                'new_requests' => RequestModel::whereDate('created_at', Carbon::today())->count(),
                'pending_tasks' => EmployeeHoliday::where('status', 'pending')->count() +
                    EmployeePermission::where('status', 'pending')->count(),
                'system_alerts' => $this->getSystemAlerts(),
            ],
        ]);
    }

    private function getSystemAlerts()
    {
        $alerts = collect();

        $expiredCount = CraftLicense::where('expiry_date', '<', Carbon::today())->count();
        if ($expiredCount > 0) {
            $alerts->push([
                'type' => 'warning',
                'message' => "يوجد {$expiredCount} رخصة منتهية الصلاحية",
                'action_url' => route('craft_licenses.index'),
            ]);
        }

        $pendingStatusId = \App\Models\RequestStatus::where('name', 'قيد الانتظار')->first()?->id;
        $urgentCount = $pendingStatusId
            ? RequestModel::where('is_urgent', true)->where('status_id', $pendingStatusId)->count()
            : 0;

        if ($urgentCount > 0) {
            $alerts->push([
                'type' => 'danger',
                'message' => "يوجد {$urgentCount} طلب عاجل في انتظار المعالجة",
                'action_url' => route('requests.index'),
            ]);
        }

        return $alerts;
    }
}