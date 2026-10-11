<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDependent;
use App\Models\EmployeeJobHistory;
use App\Models\EmployeeJobInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeJobInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index job_info', ['only' => ['index']]);
        $this->middleware('permission:store job_info', ['only' => ['store', 'syncRelatedData']]);
        $this->middleware('permission:update job_info', ['only' => ['update']]);
        $this->middleware('permission:delete job_info', ['only' => ['destroy']]);
    }
    public function index()
    {
        $jobinfos  = EmployeeJobInfo::with('employee')->paginate(10);
        $employees = Employee::get(['id', 'name']);
        return view(
            'employee.jobinfo',
            compact('jobinfos', 'employees')
        );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emp_number'                 => 'required|unique:employee_job_infos,emp_number',
            'employee_id'                => 'required',
            'work_start_date'            => 'required',
            'ministry_date_appointment'  => 'required',
            'type'                       => 'required',
            'ministry_installation_date' => 'required',
            'current_job'                => 'required',
            'note'                       => 'nullable',
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        $inserted = EmployeeJobInfo::create($validatedData);

        $inserted->shifts()->attach($request->shift_ids);

        $job_info = EmployeeJobInfo::where('employee_id', $inserted->employee_id)
            ->first('ministry_installation_date');

        $employee = Employee::where('id', $inserted->employee_id)
            ->first();
        
        // Log activity
        activity()
            ->performedOn($inserted)
            ->causedBy(auth()->user())
            ->log('تم إضافة بيانات وظيفية للموظف: ' . $employee->name);

        $ministryInstallationDate = date('Y-m-d', strtotime($job_info->ministry_installation_date));
        $today                    = date('Y-m-d');
        $diff                     = abs(strtotime($today) - strtotime($ministryInstallationDate));
        $yearsOfService           = floor($diff / (60 * 60 * 24 * 365));

        $birthdate = date('Y-m-d', strtotime($employee->birthdate));
        $today     = date('Y-m-d');

        $diff = abs(strtotime($today) - strtotime($birthdate));
        $age  = floor($diff / (60 * 60 * 24 * 365));

        $vacationDays                   = ($age >= 50 && $yearsOfService >= 15) ? 35 : 30;
        $validatedData['vacation_days'] = $vacationDays;
        $inserted->update([
            'vacation_days'  => $vacationDays,
            "current_degree" => $employee->job->category->start_degree,
        ]);

        User::updateOrCreate(
            ['employee_id' => $inserted->employee_id],
            [
                'name'            => $employee->name,
                'email'           => $employee->email ?? $inserted->emp_number . '@jabalia.gov.ps',
                'employee_number' => $inserted->emp_number,
                'password'        => Hash::make($inserted->emp_number),
            ]
        );

        $this->createDependentsIfMissing($employee);
        $this->createJobHistoryIfMissing($employee);

        return redirect()->back()
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $jobinfo       = EmployeeJobInfo::findOrfail($id);
        $validatedData = $request->validate([
            'emp_number'                 => 'required|unique:employee_job_infos,emp_number,' . $id,
            'employee_id'                => 'required',
            'work_start_date'            => 'required',
            'ministry_date_appointment'  => 'required',
            'type'                       => 'required',
            'ministry_installation_date' => 'required',
            'current_job'                => 'required',
            'note'                       => 'nullable',
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);

        $jobinfo->update($validatedData);
        $jobinfo->shifts()->sync($request->shift_ids);

        $employee     = Employee::where('id', $jobinfo->employee_id)->first();
        
        // Log activity
        activity()
            ->performedOn($jobinfo)
            ->causedBy(auth()->user())
            ->log('تم تعديل بيانات وظيفية للموظف: ' . $employee->name);
        User::updateOrCreate(
            ['employee_id' => $jobinfo->employee_id],
            [
                'name'            => $employee->name,
                'email'           => $employee->email ?? $jobinfo->emp_number . '@jabalia.gov.ps',
                'employee_number' => $jobinfo->emp_number,
            ]
        );

        $this->createDependentsIfMissing($employee);
        $this->createJobHistoryIfMissing($employee);

        return redirect()->back()
            ->with('success', __('updated successfully'));
    }

    protected function createJobHistory(Employee $employee): void
    {
        $category = $employee->job->category;

        EmployeeJobHistory::create([
            'type'          => 'assigned',
            'status'        => 'Active',
            'employee_id'   => $employee->id,
            'job'           => $employee->job->name,
            'category'      => optional($category)->name,
            'degree'        => optional($category)->start_degree,
            'section'       => $employee->section?->name,
            'department'    => $employee->department?->name,
            'branch'        => $employee->branch?->name,
            'bonus_percent' => optional($category)->bonus_percent ?? '0',
            'date'          => now()->format('Y-m-d'),
        ]);
    }

    public function createDependentsIfMissing(Employee $employee): void
    {
        if (empty($employee->czn_id)) {
            return;
        }

        try {
            $relativesData = $this->getCitizenRelations($employee->czn_id);
        } catch (\Throwable $e) {
            Log::warning("EmployeeJobInfoController: failed to fetch citizen relations", [
                'employee_id' => $employee->id,
                'czn_id'      => $employee->czn_id,
                'error'       => $e->getMessage(),
            ]);
            return;
        }

        if (empty($relativesData) || ! is_array($relativesData)) {
            return;
        }

        foreach ($relativesData as $relative) {
            $relativeCzn = $relative['IDNO_RELATIVE'] ?? '';

            // Skip if czn_id is empty
            if (empty($relativeCzn)) {
                continue;
            }

            // Check if this dependent already exists
            $exists = $employee->employeeDependents()
                ->where('czn_id', $relativeCzn)
                ->exists();

            if ($exists) {
                continue;
            }

            $birthdate     = $relative['BIRTH_DT'] ?? null;
            $formattedDate = null;

            if ($birthdate) {
                $birthdateParts = explode('/', $birthdate);
                if (count($birthdateParts) === 3) {
                    $formattedDate = $birthdateParts[2] . '-' . $birthdateParts[1] . '-' . $birthdateParts[0];
                }
            }

            EmployeeDependent::create([
                'name'        => $relative['FULLNAME'] ?? '',
                'czn_id'      => $relativeCzn,
                'gender'      => $relative['SEX'] ?? '',
                'birth'       => $formattedDate,
                'relation'    => $relative['RELATIVE_DESC'] ?? '',
                'status'      => 'Active',
                'employee_id' => $employee->id,
            ]);
        }
    }

    protected function getCitizenRelations(string $cznId): array
    {
        $request           = Request::create('/', 'GET', ['czn_id' => $cznId]);
        $citizenController = app(CitizenController::class);
        $relations         = $citizenController->getCitizenRelations($request);

        return is_array($relations) ? $relations : [];
    }

    public function createUserIfMissing(Employee $employee): void
    {
        $jobInfo = $employee->EmployeeJobInfo;

        if (! $jobInfo) {
            return;
        }

        // Check if another user already holds this employee_number
        $existingWithSameNumber = User::where('employee_number', $jobInfo->emp_number)
            ->where('employee_id', '!=', $employee->id)
            ->exists();

        if ($existingWithSameNumber) {
            Log::warning('createUserIfMissing: duplicate employee_number, skipping', [
                'employee_id'     => $employee->id,
                'employee_number' => $jobInfo->emp_number,
            ]);
            return;
        }

        User::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name'            => $employee->name,
                'email'           => $employee->email ?? $jobInfo->emp_number . '@jabalia.gov.ps',
                'employee_number' => $jobInfo->emp_number,
                'password'        => \Illuminate\Support\Facades\Hash::make($jobInfo->emp_number),
            ]
        );
    }

    public function createJobHistoryIfMissing(Employee $employee): void
    {
        if ($employee->employeeJobHistories()->exists()) {
            return;
        }

        $employee->load(['job.category', 'section', 'department', 'branch']);
        $category = $employee->job->category;

        EmployeeJobHistory::create([
            'type'          => 'assigned',
            'status'        => 'Active',
            'employee_id'   => $employee->id,
            'job'           => $employee->job->name,
            'category'      => optional($category)->name,
            'degree'        => optional($category)->start_degree,
            'section'       => $employee->section?->name,
            'department'    => $employee->department?->name,
            'branch'        => $employee->branch?->name,
            'bonus_percent' => optional($category)->bonus_percent ?? '0',
            'date'          => now()->format('Y-m-d'),
        ]);
    }

    public function syncRelatedData(Employee $employee)
    {
        $this->createDependentsIfMissing($employee);
        $this->createJobHistoryIfMissing($employee);

        return redirect()->back()
            ->with('success', __('Related data synchronized successfully'));
    }

    public function destroy($id)
    {
        $jobinfo = EmployeeJobInfo::findOrfail($id);
        $employee = Employee::where('id', $jobinfo->employee_id)->first();
        
        // Log activity before delete
        activity()
            ->performedOn($jobinfo)
            ->causedBy(auth()->user())
            ->log('تم حذف بيانات وظيفية للموظف: ' . $employee->name);
        
        $jobinfo->delete();
        return redirect()->route('jobinfo.index')
            ->with('success', __('deleted successfully'));
    }
}