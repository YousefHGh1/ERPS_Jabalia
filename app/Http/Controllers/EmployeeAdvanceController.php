<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeAdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index employee_advance', ['only' => ['index']]);
        $this->middleware('permission:store employee_advance', ['only' => ['store']]);
        $this->middleware('permission:update employee_advance', ['only' => ['update']]);
        $this->middleware('permission:delete employee_advance', ['only' => ['destroy']]);
    }

    /**
     * Display advances pending for the logged-in user's approval,
     * and advances requested by the logged-in user.
     */
    public function index(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        if (! $employee_id) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        $employee = Employee::findOrFail($employee_id);

        // Advances that I requested
        $myAdvances = EmployeeAdvance::where('employee_id', $employee_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'my_page');

        // Advances pending my approval (as section director)
        $pendingSection = EmployeeAdvance::where('section_director_id', $employee_id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Advances pending my approval (as mayor)
        $pendingMayor = EmployeeAdvance::where('mayor_id', $employee_id)
            ->where('status', 'section_approved')
            ->orderBy('created_at', 'desc')
            ->get();

        // Advances pending my approval (as finance officer)
        $pendingFinance = EmployeeAdvance::where('finance_officer_id', $employee_id)
            ->where('status', 'mayor_approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee_advance.index', compact(
            'myAdvances',
            'pendingSection',
            'pendingMayor',
            'pendingFinance',
            'employee'
        ));
    }

    /**
     * Show the form for creating a new advance request.
     */
    public function create()
    {
        $employee = Auth::user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        return view('employee_advance.create', compact('employee'));
    }

    /**
     * Store a newly created advance request.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount'                  => 'required|numeric|min:1',
            'reason'                  => 'required|string|max:2000',
            'request_date'            => 'required|date',
            'expected_repayment_date' => 'nullable|date|after_or_equal:request_date',
        ], [
            'required' => __('required'),
            'numeric'  => __('numeric'),
            'string'   => __('string'),
        ]);

        $employee = Auth::user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        // Determine who should approve at each level
        $sectionDirector    = $employee->section?->director?->employee_id;
        $departmentDirector = $employee->department?->director?->employee_id;

        // First level: Section director (or department director if employee is section director)
        $sectionDirectorId = $sectionDirector;
        if ($employee->id == $sectionDirector) {
            $sectionDirectorId = $departmentDirector;
        }
        if ($employee->id == $departmentDirector) {
            $sectionDirectorId = 1; // Admin
        }

        // Second level: Mayor (رئيس البلدية) - we need to get this from settings or a config
        // For now, we'll use the employee with a specific role or a config value
        $mayorId = $this->getMayorId();

        // Third level: Finance officer
        $financeOfficerId = $this->getFinanceOfficerId();

        DB::beginTransaction();
        try {
            $advance = EmployeeAdvance::create([
                'employee_id'             => $employee->id,
                'amount'                  => $validatedData['amount'],
                'reason'                  => $validatedData['reason'],
                'request_date'            => $validatedData['request_date'],
                'expected_repayment_date' => $validatedData['expected_repayment_date'] ?? null,
                'section_director_id'     => $sectionDirectorId ?? 1,
                'mayor_id'                => $mayorId,
                'finance_officer_id'      => $financeOfficerId,
                'status'                  => 'pending',
                'created_by'              => Auth::id(),
            ]);

            // Notify section director
            $supervisor = Employee::find($sectionDirectorId ?? 1);
            if ($supervisor && $supervisor->user) {
                NotificationService::send($supervisor->user, [
                    'type'     => 'advance',
                    'title'    => __('New Advance Request'),
                    'message'  => __('Employee :name has requested an advance of :amount', [
                        'name'   => $employee->name,
                        'amount' => number_format($validatedData['amount'], 2),
                    ]),
                    'link'     => route('employee-advances.index'),
                    'icon'     => 'fa-money-bill-wave',
                    'priority' => 'normal',
                ]);
            }

            DB::commit();

            return redirect()->route('employee-advances.index')
                ->with('success', 'تم تقديم طلب السلفة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تقديم الطلب: ' . $e->getMessage());
        }
    }

    /**
     * Show advance details
     */
    public function show($id)
    {
        $advance = EmployeeAdvance::with(['employee', 'sectionDirector', 'mayor', 'financeOfficer', 'creator'])->findOrFail($id);

        $employee = Auth::user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        // Only allow the request owner or relevant approvers to view
        $canView = $advance->employee_id == $employee->id
        || $advance->section_director_id == $employee->id
        || $advance->mayor_id == $employee->id
        || $advance->finance_officer_id == $employee->id
        || Auth::user()->hasRole('admin')
        || Auth::user()->hasRole('super-admin');

        if (! $canView) {
            return redirect()->back()->with('error', 'غير مصرح لك بمشاهدة هذا الطلب');
        }

        return view('employee_advance.show', compact('advance'));
    }

    /**
     * Section director approves the advance
     */
    public function approveBySection(Request $request, $id)
    {
        $advance  = EmployeeAdvance::findOrFail($id);
        $employee = Auth::user()->employee;

        if ($advance->section_director_id != $employee->id) {
            return redirect()->back()->with('error', 'غير مصرح لك بتنفيذ هذا الإجراء');
        }

        if ($advance->status != 'pending') {
            return redirect()->back()->with('error', 'الطلب ليس في حالة انتظار');
        }

        $request->validate([
            'section_notes' => 'nullable|string|max:1000',
        ]);

        $advance->update([
            'status'              => 'section_approved',
            'section_approved_at' => now(),
            'section_notes'       => $request->section_notes,
        ]);

        // Notify mayor
        $mayor = Employee::find($advance->mayor_id);
        if ($mayor && $mayor->user) {
            NotificationService::send($mayor->user, [
                'type'     => 'advance',
                'title'    => __('Advance Request - Section Approved'),
                'message'  => __('Advance request from :name for :amount has been approved by section director. Pending your approval.', [
                    'name'   => $advance->employee->name,
                    'amount' => number_format($advance->amount, 2),
                ]),
                'link'     => route('employee-advances.index'),
                'icon'     => 'fa-money-bill-wave',
                'priority' => 'high',
            ]);
        }

        return redirect()->back()->with('success', 'تمت الموافقة على طلب السلفة من قبل مدير الدائرة');
    }

    /**
     * Mayor approves the advance
     */
    public function approveByMayor(Request $request, $id)
    {
        $advance  = EmployeeAdvance::findOrFail($id);
        $employee = Auth::user()->employee;

        if ($advance->mayor_id != $employee->id) {
            return redirect()->back()->with('error', 'غير مصرح لك بتنفيذ هذا الإجراء');
        }

        if ($advance->status != 'section_approved') {
            return redirect()->back()->with('error', 'الطلب لم يتم الموافقة عليه من مدير الدائرة بعد');
        }

        $request->validate([
            'mayor_notes' => 'nullable|string|max:1000',
        ]);

        $advance->update([
            'status'            => 'mayor_approved',
            'mayor_approved_at' => now(),
            'mayor_notes'       => $request->mayor_notes,
        ]);

        // Notify finance officer
        $financeOfficer = Employee::find($advance->finance_officer_id);
        if ($financeOfficer && $financeOfficer->user) {
            NotificationService::send($financeOfficer->user, [
                'type'     => 'advance',
                'title'    => __('Advance Request - Mayor Approved'),
                'message'  => __('Advance request from :name for :amount has been approved by the mayor. Pending your approval.', [
                    'name'   => $advance->employee->name,
                    'amount' => number_format($advance->amount, 2),
                ]),
                'link'     => route('employee-advances.index'),
                'icon'     => 'fa-money-bill-wave',
                'priority' => 'high',
            ]);
        }

        return redirect()->back()->with('success', 'تمت الموافقة على طلب السلفة من قبل رئيس البلدية');
    }

    /**
     * Finance officer approves the advance
     */
    public function approveByFinance(Request $request, $id)
    {
        $advance  = EmployeeAdvance::findOrFail($id);
        $employee = Auth::user()->employee;

        if ($advance->finance_officer_id != $employee->id) {
            return redirect()->back()->with('error', 'غير مصرح لك بتنفيذ هذا الإجراء');
        }

        if ($advance->status != 'mayor_approved') {
            return redirect()->back()->with('error', 'الطلب لم يتم الموافقة عليه من رئيس البلدية بعد');
        }

        $request->validate([
            'finance_notes' => 'nullable|string|max:1000',
        ]);

        $advance->update([
            'status'              => 'finance_approved',
            'finance_approved_at' => now(),
            'finance_notes'       => $request->finance_notes,
        ]);

        // Notify the employee that the advance is fully approved
        $employeeRequester = $advance->employee;
        if ($employeeRequester && $employeeRequester->user) {
            NotificationService::send($employeeRequester->user, [
                'type'     => 'advance',
                'title'    => __('Advance Fully Approved'),
                'message'  => __('Your advance request for :amount has been fully approved by all parties.', [
                    'amount' => number_format($advance->amount, 2),
                ]),
                'link'     => route('employee-advances.index'),
                'icon'     => 'fa-check-circle',
                'priority' => 'high',
            ]);
        }

        return redirect()->back()->with('success', 'تمت الموافقة على طلب السلفة من قبل دائرة المالية');
    }

    /**
     * Reject the advance at any level
     */
    public function reject(Request $request, $id)
    {
        $advance  = EmployeeAdvance::findOrFail($id);
        $employee = Auth::user()->employee;

        // Check if the user is authorized to reject
        $isAuthorized = $advance->section_director_id == $employee->id
        || $advance->mayor_id == $employee->id
        || $advance->finance_officer_id == $employee->id
        || Auth::user()->hasRole('admin')
        || Auth::user()->hasRole('super-admin');

        if (! $isAuthorized) {
            return redirect()->back()->with('error', 'غير مصرح لك بتنفيذ هذا الإجراء');
        }

        if (in_array($advance->status, ['finance_approved', 'rejected'])) {
            return redirect()->back()->with('error', 'لا يمكن رفض الطلب في هذه المرحلة');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $advance->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify the employee
        $employeeRequester = $advance->employee;
        if ($employeeRequester && $employeeRequester->user) {
            NotificationService::send($employeeRequester->user, [
                'type'     => 'advance',
                'title'    => __('Advance Request Rejected'),
                'message'  => __('Your advance request for :amount has been rejected. Reason: :reason', [
                    'amount' => number_format($advance->amount, 2),
                    'reason' => $request->rejection_reason,
                ]),
                'link'     => route('employee-advances.index'),
                'icon'     => 'fa-times-circle',
                'priority' => 'high',
            ]);
        }

        return redirect()->back()->with('success', 'تم رفض طلب السلفة');
    }

    /**
     * Get the mayor/head of municipality employee ID
     * This can be configured via settings, hardcoded, or based on role
     */
    private function getMayorId(): int
    {
        // Try to find from a config setting
        $mayorId = config('app.mayor_employee_id');
        if ($mayorId) {
            return $mayorId;
        }

        // Try to find employee who has "رئيس البلدية" role or job
        $mayor = Employee::whereHas('job', function ($q) {
            $q->where('name', 'like', '%رئيس بلدية%');
        })->first();

        if ($mayor) {
            return $mayor->id;
        }

        // Fallback: Look for admin user
        $adminUser = \App\Models\User::role('admin')->first();
        if ($adminUser && $adminUser->employee_id) {
            return $adminUser->employee_id;
        }

        return 1; // Default fallback
    }

    /**
     * Get the finance department officer employee ID
     */
    private function getFinanceOfficerId(): int
    {
        // Try to find from a config setting
        $financeId = config('app.finance_officer_employee_id');
        if ($financeId) {
            return $financeId;
        }

        // Try to find employee in department named "المالية"
        $finance = Employee::whereHas('department', function ($q) {
            $q->where('name', 'like', '%المالية%');
        })->whereHas('job', function ($q) {
            $q->where('name', 'like', '%مدير%');
        })->first();

        if ($finance) {
            return $finance->id;
        }

        // Fallback: Look for admin user
        $adminUser = \App\Models\User::role('admin')->first();
        if ($adminUser && $adminUser->employee_id) {
            return $adminUser->employee_id;
        }

        return 1; // Default fallback
    }
}
