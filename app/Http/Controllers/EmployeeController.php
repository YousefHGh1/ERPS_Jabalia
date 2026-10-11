<?php
namespace App\Http\Controllers;

use App\Actions\CreateEmployee;
use App\Actions\DeleteEmployee;
use App\Actions\UpdateEmployee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Bonus;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Shift;
use App\Repositories\EmployeeRepository;
use App\Services\AuditTrailService;
use App\Services\EmployeeImportService;
use App\Services\StaticDataCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    private EmployeeRepository $employeeRepository;
    private StaticDataCacheService $cacheService;
    private CreateEmployee $createEmployeeAction;
    private UpdateEmployee $updateEmployeeAction;
    private DeleteEmployee $deleteEmployeeAction;
    private AuditTrailService $auditTrailService;
    private EmployeeImportService $employeeImportService;

    public function __construct(
        EmployeeRepository $employeeRepository,
        StaticDataCacheService $cacheService,
        CreateEmployee $createEmployeeAction,
        UpdateEmployee $updateEmployeeAction,
        DeleteEmployee $deleteEmployeeAction,
        AuditTrailService $auditTrailService,
        EmployeeImportService $employeeImportService
    ) {
        $this->employeeRepository    = $employeeRepository;
        $this->cacheService          = $cacheService;
        $this->createEmployeeAction  = $createEmployeeAction;
        $this->updateEmployeeAction  = $updateEmployeeAction;
        $this->deleteEmployeeAction  = $deleteEmployeeAction;
        $this->auditTrailService     = $auditTrailService;
        $this->employeeImportService = $employeeImportService;

        $this->middleware('permission:index employees', ['only' => ['index']]);
        $this->middleware('permission:store employees', ['only' => ['store', 'importEmployee']]);
        $this->middleware('permission:update employees', ['only' => ['update']]);
        $this->middleware('permission:delete employees', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name         = $request->input('name');
        $cznId        = $request->input('czn_id');
        $departmentId = $request->input('department_id');
        $sectionId    = $request->input('section_id');
        $branchId     = $request->input('branch_id');
        $jobId        = $request->input('job_id');

        $employees = $this->employeeRepository->with(['department', 'section', 'branch', 'job', 'EmployeeJobInfo'])
            ->visibleTo($request->user())
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($cznId, function ($query) use ($cznId) {
                $query->where('czn_id', 'like', '%' . $cznId . '%');
            })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($sectionId, function ($query) use ($sectionId) {
                $query->where('section_id', $sectionId);
            })
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($jobId, function ($query) use ($jobId) {
                $query->where('job_id', $jobId);
            })
            ->paginate(12)->appends(request()->all());

        if ($request->ajax()) {
            $tableHtml = view('employee.partials._cards', compact('employees'))->render();

            $editModalsHtml = view('employee.partials._edit_modals', [
                'employees'   => $employees,
                // these are referenced inside the edit modal partial
                'departments' => $this->cacheService->getDepartments(),
                'sections'    => $this->cacheService->getSections(),
                'branches'    => $this->cacheService->getBranches(),
                'jobs'        => $this->cacheService->getJobs(),
            ])->render();

            return response()->json([
                'table'       => $tableHtml,
                'edit_modals' => $editModalsHtml,
            ]);
        }

        $departments = $this->cacheService->getDepartments();
        $sections    = $this->cacheService->getSections();
        $branches    = $this->cacheService->getBranches();
        $jobs        = $this->cacheService->getJobs();

        return view('employee.index', compact('employees', 'departments', 'sections', 'branches', 'jobs'));
    }

    public function export(Request $request)
    {
        $employees = $this->getEmployeesForExport($request);

        if ($request->input('format') === 'xlsx') {
            return $this->exportExcel($employees);
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=employees_export_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8 (Excel friendly)

            // Header
            fputcsv($file, [
                __('id'),
                __('name'),
                __('czn_id'),
                __('department'),
                __('section'),
                __('branch'),
                __('job'),
                __('mobile'),
                __('salary'),
                __('created_at'),
            ]);

            foreach ($employees as $emp) {
                fputcsv($file, [
                    $emp->id,
                    $emp->name,
                    $emp->czn_id,
                    $emp->department?->name,
                    $emp->section?->name,
                    $emp->branch?->name,
                    $emp->job?->name,
                    $emp->mobile,
                    $emp->salary,
                    $emp->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getEmployeesForExport(Request $request)
    {
        $filters = [
            'name'          => $request->input('name'),
            'czn_id'        => $request->input('czn_id'),
            'department_id' => $request->input('department_id'),
            'section_id'    => $request->input('section_id'),
            'branch_id'     => $request->input('branch_id'),
            'job_id'        => $request->input('job_id'),
        ];

        return $this->employeeRepository->with(['department', 'section', 'branch', 'job'])
            ->visibleTo($request->user())
            ->when($filters['name'], function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            })
            ->when($filters['czn_id'], function ($query) use ($filters) {
                $query->where('czn_id', 'like', '%' . $filters['czn_id'] . '%');
            })
            ->when($filters['department_id'], function ($query) use ($filters) {
                $query->where('department_id', $filters['department_id']);
            })
            ->when($filters['section_id'], function ($query) use ($filters) {
                $query->where('section_id', $filters['section_id']);
            })
            ->when($filters['branch_id'], function ($query) use ($filters) {
                $query->where('branch_id', $filters['branch_id']);
            })
            ->when($filters['job_id'], function ($query) use ($filters) {
                $query->where('job_id', $filters['job_id']);
            })
            ->get();
    }

    private function exportExcel($employees)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $headers = [
            'اسم الموظف',
            'هوية',
            'الدائرة',
            'القسم',
            'المسمى الوظيفي المعتمد',
            'تاريخ الميلاد',
            'جنس',
            'رقم الهوية',
            'رقم الجوال',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowIndex = 2;
        foreach ($employees as $employee) {
            $gender    = $employee->gender === 'female' ? 'انثى' : 'ذكر';
            $birthdate = $employee->birthdate
                ? \Carbon\Carbon::parse($employee->birthdate)->format('d/m/Y')
                : '';

            $sheet->fromArray([
                $employee->name,
                $employee->czn_id,
                $employee->department?->name,
                $employee->section?->name,
                $employee->job?->name,
                $birthdate,
                $gender,
                $employee->czn_id,
                $employee->mobile,
            ], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'employees_export_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function edit(Employee $employee)
    {
        return redirect()->route('employees.show', $employee->id);
    }

    public function show(Employee $employee)
    {
        $have_info = $employee->EmployeeJobInfo()->count();
        $employee  = $employee->load(
            'user',
            'attachments',
            'employeeBonus',
            'employeeDependents',
            'employeeJobHistories',
            'employeeMainHoliday',
            'employeePermissions'
        )->load(['employeeDependents' => function ($query) {
            $query->orderBy('relation');
        }]);

        $attendanceRecords = collect();
        if ($employee->user) {
            $attendanceRecords = DB::table('z_k_data')

                ->where('emp_id', $employee->user->employee_number ?? $employee->id)
                ->orderByDesc('arrival_time')
                ->limit(30)
                ->get()
                ->map(function ($record) {
                    $arrival = $record->arrival_time ? \Carbon\Carbon::parse($record->arrival_time) : null;

                    return (object) [
                        'date'       => $arrival?->format('Y-m-d') ?? '-',
                        'entry_time' => $arrival?->format('H:i:s') ?? '-',
                        'exit_time'  => '-',
                        'work_hours' => '-',
                    ];
                });
        }

        $auditLogs = $this->auditTrailService->getLogsForUser($employee->user);

        $bonuses  = Bonus::get(['id', 'name']);
        $holidays = Holiday::get(['id', 'name']);
        $shifts   = Shift::get(['id', 'name']);
        $departments = \App\Models\Department::get(['id', 'name']);
        $sections = \App\Models\Section::get(['id', 'name', 'department_id']);
        $branches = \App\Models\Branch::get(['id', 'name', 'section_id']);
        $jobs = \App\Models\Job::get(['id', 'name']);

        return view('employee.profile', compact('employee', 'have_info', 'bonuses', 'holidays', 'shifts', 'attendanceRecords', 'auditLogs', 'departments', 'sections', 'branches', 'jobs'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();

        // Get relatives data from citizen controller
        $relativesData = [];
        if ($request->has('czn_id')) {
            $citizenController = app(CitizenController::class);
            $relativesData     = $citizenController->getCitizenRelations($request);
        }

        $employee = $this->createEmployeeAction->execute($validatedData, $relativesData);

        // Dispatch event for logging
        event(new \App\Events\EmployeeCreated($employee));

        return redirect()->route('employees.index')
            ->with('success', __('saved successfully'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $validatedData = $request->validated();
        $employee      = $this->updateEmployeeAction->execute($employee, $validatedData);

        // Dispatch event for logging
        event(new \App\Events\EmployeeUpdated($employee));

        return redirect()->route('employees.index')
            ->with('success', __('updated successfully'));
    }

    public function destroy(Employee $employee)
    {
        try {
            $this->deleteEmployeeAction->execute($employee);

            // Dispatch event for logging
            event(new \App\Events\EmployeeDeleted($employee));

            return redirect()->route('employees.index')
                ->with('success', __('deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('warning', $e->getMessage());
        }
    }

    public function importEmployee(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'file.mimes' => 'يجب أن يكون الملف بصيغة CSV أو Excel (xlsx/xls)',
        ]);

        $file = $request->file('file');

        try {
            $results = $this->employeeImportService->importFromFile(
                $file->getPathname(),
                $file->getClientOriginalExtension()
            );

            $message = $results['imported'] > 0
                ? "تم استيراد {$results['imported']} موظف بنجاح"
                : 'لم يتم استيراد موظفين جدد';

            return response()->json([
                'success'  => true,
                'message'  => $message,
                'imported' => $results['imported'],
                'skipped'  => $results['skipped'],
                'errors'   => $results['errors'],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Import error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportTawjihiDependents(Request $request)
    {
        $birthYear = $request->input('birth_year');
        $sectionId = $request->input('section_id');
        $departmentId = $request->input('department_id');

        $filePath = storage_path('app/tawjihi_dependents.csv');

        $params = [
            '--output' => $filePath
        ];

        if ($birthYear) {
            $params['--birth-year'] = $birthYear;
        }

        if ($sectionId) {
            $params['--section-id'] = $sectionId;
        }

        if ($departmentId) {
            $params['--department-id'] = $departmentId;
        }

        $exitCode = Artisan::call('employee:export-tawjihi-dependents', $params);

        if ($exitCode !== 0) {
            return back()->with('error', 'فشل إنشاء التقرير');
        }

        if (!file_exists($filePath)) {
            return back()->with('error', 'فشل إنشاء ملف التقرير');
        }

        return response()->download($filePath, 'tawjihi_dependents_' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}
