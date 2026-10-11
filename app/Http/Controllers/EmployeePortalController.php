<?php
namespace App\Http\Controllers;

use App\Models\EmployeeHoliday;
use App\Models\EmployeePermission;
use App\Models\Holiday;
use App\Services\ModelAuditLabels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class EmployeePortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function requests()
    {
        $employee      = Auth::user()->employee;
        $holidays      = $employee->employeeHoliday()->latest()->get();
        $permissions   = $employee->employeePermissions()->latest()->get();
        $holiday_types = Holiday::all();

        return view('employee_portal.requests', compact('employee', 'holidays', 'permissions', 'holiday_types'));
    }

    public function storeHoliday(Request $request)
    {
        $request->validate([
            'holiday_id' => 'required|exists:holidays,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string',
        ]);

        $employee = Auth::user()->employee;

        $section_director    = $employee->section?->director?->employee_id;
        $department_director = $employee->department?->director?->employee_id;

        $for_employee = $section_director;
        if ($section_director == $employee->id) {
            $for_employee = $department_director;
        }

        EmployeeHoliday::create([
            'employee_id'  => $employee->id,
            'holiday_id'   => $request->holiday_id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'reason'       => $request->reason,
            'status'       => 'pending',
            'for_employee' => $for_employee ?? $department_director ?? 1,
        ]);

        return redirect()->back()->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'type'       => 'required|string',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        $employee = Auth::user()->employee;

        $section_director    = $employee->section?->director?->employee_id;
        $department_director = $employee->department?->director?->employee_id;

        $to_employee = $section_director;
        if ($section_director == $employee->id) {
            $to_employee = $department_director;
        }

        EmployeePermission::create([
            'employee_id' => $employee->id,
            'name'        => $request->name,
            'type'        => $request->type,
            'date'        => $request->date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'status'      => 'pending',
            'to_employee' => $to_employee ?? $department_director ?? 1,
        ]);

        return redirect()->back()->with('success', 'تم تقديم طلب الإذن بنجاح');
    }

    public function index()
    {
        $user = Auth::user();
        if (! $user->employee) {
            return redirect()->route('home.index')->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        $employee = $user->employee->load('EmployeeJobInfo', 'job', 'department', 'employeeDependents');

        // إحصائيات شخصية
        $stats = [
            'total_holidays'     => $employee->employeeHoliday()->where('status', 'accepted')->count(),
            'total_permissions'  => $employee->employeePermissions()->where('status', 'accepted')->count(),
            'total_late_minutes' => $user->logs()->sum('late_minutes'),
            'today_attendance'   => $user->logs()->whereDate('arrival_time', today())->first(),
        ];

        // سجل الحضور الأخير
        $recent_attendance = $user->logs()->latest('arrival_time')->take(10)->get();

        // بيانات الرسم البياني للتأخير (آخر 15 يوم عمل)
        $chart_data = $user->logs()
            ->where('arrival_time', '>=', now()->subDays(30))
            ->orderBy('arrival_time')
            ->take(15)
            ->get()
            ->map(function ($log) {
                return [
                    'date' => Carbon::parse($log->arrival_time)->format('m-d'),
                    'late' => $log->late_minutes ?? 0,
                ];
            });

        // إحصائيات الدخول للنظام
        $login_stats = [
            'last_login'   => $user->logs()->latest('arrival_time')->first(),
            'month_logins' => $user->logs()
                ->whereMonth('arrival_time', now()->month)
                ->whereYear('arrival_time', now()->year)
                ->count(),
            'total_hours'  => $this->calculateTotalHours($user),
        ];

        // سجل النشاطات (من activity_log)
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($activity) {
                $event = $activity->event ?? 'updated';
                return [
                    'id'           => $activity->id,
                    'event'        => $event,
                    'description'  => $activity->description,
                    'subject_type' => $activity->subject_type,
                    'subject_id'   => $activity->subject_id,
                    'created_at'   => $activity->created_at,
                    'event_name'   => $this->getActivityEventName($event),
                    'color'        => $this->getActivityColor($event),
                    'icon'         => $this->getActivityIcon($event),
                    'subject_name' => $this->getSubjectName($activity),
                ];
            });

        return view('employee_portal.index', compact('employee', 'stats', 'recent_attendance', 'chart_data', 'login_stats', 'activities'));
    }

    /**
     * حساب إجمالي ساعات العمل
     */
    private function calculateTotalHours($user)
    {
        // جدول z_k_data لا يحتوي على departure_time، لذا نحسب ساعات العمل بناءً على عدد أيام الحضور
        $logs = $user->logs()
            ->whereNotNull('arrival_time')
            ->whereMonth('arrival_time', now()->month)
            ->whereYear('arrival_time', now()->year)
            ->get();

        // نفترض 8 ساعات عمل لكل يوم حضور
        $totalHours = $logs->count() * 8;

        return $totalHours;
    }

    /**
     * الحصول على لون النشاط
     */
    public function getActivityColor($event)
    {
        $colors = [
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'login'   => 'info',
            'logout'  => 'secondary',
        ];

        return $colors[$event] ?? 'primary';
    }

    /**
     * الحصول على أيقونة النشاط
     */
    public function getActivityIcon($event)
    {
        $icons = [
            'created' => 'fa-plus',
            'updated' => 'fa-edit',
            'deleted' => 'fa-trash',
            'login'   => 'fa-sign-in-alt',
            'logout'  => 'fa-sign-out-alt',
        ];

        return $icons[$event] ?? 'fa-circle';
    }

    /**
     * الحصول على اسم النشاط بالعربية
     */
    public function getActivityEventName($event)
    {
        $names = [
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            'login'   => 'دخول',
            'logout'  => 'خروج',
        ];

        return $names[$event] ?? $event;
    }

    /**
     * الحصول على اسم النموذج
     */
    public function getSubjectName($activity)
    {
        if (! $activity->subject_type) {
            return 'غير محدد';
        }

        $classNames = ModelAuditLabels::all();

        return $classNames[$activity->subject_type] ?? class_basename($activity->subject_type);
    }

    public function attendanceCalendar()
    {
        return view('employee_portal.calendar');
    }

    public function getAttendanceData(Request $request)
    {
        $user  = Auth::user();
        $start = $request->query('start');
        $end   = $request->query('end');

        $logs = $user->logs()
            ->whereBetween('arrival_time', [$start, $end])
            ->get();

        $events = $logs->map(function ($log) {
            return [
                'title'       => 'حضور: ' . Carbon::parse($log->arrival_time)->format('H:i'),
                'start'       => $log->arrival_time,
                'color'       => $log->late_minutes > 0 ? '#FFA800' : '#1BC5BD',
                'description' => $log->late_minutes > 0 ? "تأخير: {$log->late_minutes} دقيقة" : 'في الموعد',
            ];
        });

        return response()->json($events);
    }
}
