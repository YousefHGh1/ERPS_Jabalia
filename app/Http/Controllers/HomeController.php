<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeJobInfo;
use App\Models\Job;
use App\Models\Section;
use App\Models\User;
use App\Models\ZKData;
use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use GuzzleHttp\Client;

use App\Models\EmployeeHoliday;
use App\Models\EmployeePermission;
use App\Models\CraftLicense;
use App\Models\Vehicle;
use App\Models\Engineering;
use App\Models\PaymentEngineering;
use App\Models\Request as RequestModel;
use App\Models\Reservation;
use App\Models\Notice;
use App\Models\ImprisonmentOrder;

use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 3600, function () {
            $pendingStatusId = \App\Models\RequestStatus::where('name', 'قيد الانتظار')->first()?->id;
            return [
                'employees_count' => Employee::count(),
                'departments_count' => Department::count(),
                'sections_count' => Section::count(),
                'jobs_count' => Job::count(),
                'craft_licenses_count' => CraftLicense::count(),
                'vehicles_count' => Vehicle::count(),
                'engineering_count' => Engineering::count(),
                'engineering_payments_count' => PaymentEngineering::where('status', 'active')->count(),
                'engineering_revenue' => PaymentEngineering::where('status', 'active')->sum('amount'),
                'pending_payments' => Engineering::where('remaining_amount', '>', 0)->count(),
                'craft_payments_count' => \App\Models\Payment::count(),
                'craft_revenue' => \App\Models\Payment::sum('amount'),
                'unpaid_licenses' => CraftLicense::where('remaining', '>', 0)->count(),
                'requests_count' => RequestModel::count(),
                'pending_requests_count' => $pendingStatusId ? RequestModel::where('status_id', $pendingStatusId)->count() : 0,
                'reservations_count' => Reservation::count(),
                'notices_count' => Notice::count(),
                'imprisonment_orders_count' => ImprisonmentOrder::count(),
            ];
        });

        // Today's specific data (not cached or cached for shorter time)
        $stats['today_attendance'] = ZKData::whereDate('arrival_time', today())->count();
        $stats['pending_holidays'] = EmployeeHoliday::where('status', 'pending')->count();
        $stats['pending_permissions'] = EmployeePermission::where('status', 'pending')->count();

        // بيانات الرسم البياني للحضور والتأخير (آخر 7 أيام)
        $attendance_data = Cache::remember('attendance_chart_data', 1800, function () {
            return ZKData::selectRaw('DATE(arrival_time) as date, COUNT(*) as count, SUM(late_minutes) as total_late')
                ->where('arrival_time', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        // توزيع الموظفين حسب الأقسام
        $dept_distribution = Cache::remember('dept_distribution', 3600, function () {
            return Department::withCount('employees')->get();
        });

        // آخر الحركات (أحدث الموظفين المضافين)
        $latest_employees = Employee::latest()->take(5)->get();

        // آخر الإجازات المطلوبة
        $latest_holidays = EmployeeHoliday::with('employee')->latest()->take(5)->get();

        // آخر الملفات الهندسية المضافة
        $latest_engineering_files = Engineering::latest()->take(5)->get();

        // آخر تراخيص الحرف المضافة
        $latest_craft_licenses = CraftLicense::latest()->take(5)->get();

        // آخر الطلبات
        $latest_requests = RequestModel::with(['creator', 'status', 'requestType'])->latest()->take(5)->get();

        return view('home', compact('stats', 'attendance_data', 'dept_distribution', 'latest_employees', 'latest_holidays', 'latest_engineering_files', 'latest_craft_licenses', 'latest_requests'));
    }

    public function zk(){
        $logs = ZKData::with('employee.employee')->latest('arrival_time')->paginate(10);
        return view('attendance.index', compact('logs'));   
    }

    public function getSectionsList($id) {
        $department = Department::findOrFail($id);
        $sections = $department->sections;
    
        $options = '<option value="">إختر</option>';
        foreach ($sections as $section) {
            $options .= '<option value="' . $section->id . '">' . $section->name . '</option>';
        }
    
        return response()->json(['options' => $options]);
    }
    public function get_branches_list($id) {
        $section = Section::findOrFail($id);
        $branches = $section->branches;
    
        $options = '<option value="">إختر</option>';
        foreach ($branches as $branch) {
            $options .= '<option value="' . $branch->id . '">' . $branch->name . '</option>';
        }
    
        return response()->json(['options' => $options]);
    }
}
