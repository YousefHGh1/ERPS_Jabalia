<?php

namespace App\Modules\Employees\Services;

use App\Modules\Shared\Services\BaseModuleService;
use App\Models\Employee;
use App\Models\EmployeeHoliday;
use App\Models\EmployeePermission;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeService extends BaseModuleService
{
    /**
     * Initialize model
     */
    protected function initializeModel(): void
    {
        $this->modelClass = Employee::class;
    }

    /**
     * Validation rules
     */
    protected array $validationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'phone' => 'required|string|max:20',
        'identity_number' => 'required|string|max:50|unique:employees,identity_number',
        'employee_number' => 'required|string|max:50|unique:employees,employee_number',
        'department_id' => 'nullable|exists:departments,id',
        'section_id' => 'nullable|exists:sections,id',
        'position' => 'required|string|max:255',
        'hire_date' => 'required|date',
        'salary' => 'nullable|numeric|min:0',
        'status' => 'required|in:active,inactive,suspended,terminated',
        'address' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:1000',
    ];

    /**
     * Create new employee with workflow
     */
    public function create(array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            // Generate employee number if not provided
            if (!isset($data['employee_number'])) {
                $data['employee_number'] = $this->generateEmployeeNumber();
            }
            
            // Set default status
            $data['status'] = $data['status'] ?? 'active';
            
            $employee = parent::create($data);
            
            // Create initial attendance record if hire date is today or past
            if (Carbon::parse($data['hire_date'])->lte(now())) {
                $this->createInitialAttendanceRecord($employee);
            }
            
            // Send notifications
            $this->sendCreationNotifications($employee);
            
            DB::commit();
            
            return $employee;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update employee with status change handling
     */
    public function update(int $id, array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            $employee = $this->findById($id);
            
            if (!$employee) {
                throw new \Exception("Employee not found");
            }
            
            $oldStatus = $employee->status;
            
            // Handle status change
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->handleStatusChange($employee, $oldStatus, $data['status']);
            }
            
            $updatedEmployee = parent::update($id, $data);
            
            // Send notifications for status change
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->sendStatusChangeNotifications($updatedEmployee, $oldStatus, $data['status']);
            }
            
            DB::commit();
            
            return $updatedEmployee;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle employee status change
     */
    private function handleStatusChange(Employee $employee, string $oldStatus, string $newStatus): void
    {
        switch ($newStatus) {
            case 'suspended':
                $this->suspendEmployeeAccess($employee);
                break;
            case 'terminated':
                $this->terminateEmployeeAccess($employee);
                break;
            case 'active':
                if ($oldStatus === 'suspended') {
                    $this->reactivateEmployeeAccess($employee);
                }
                break;
        }
    }

    /**
     * Suspend employee access
     */
    private function suspendEmployeeAccess(Employee $employee): void
    {
        // Deactivate user account if exists
        if ($employee->user) {
            $employee->user->update(['active' => false]);
        }
        
        // Cancel pending holidays and permissions
        $this->cancelPendingRequests($employee->id);
    }

    /**
     * Terminate employee access
     */
    private function terminateEmployeeAccess(Employee $employee): void
    {
        // Deactivate user account if exists
        if ($employee->user) {
            $employee->user->update(['active' => false]);
        }
        
        // Cancel all pending requests
        $this->cancelPendingRequests($employee->id);
        
        // Update termination date
        $employee->update(['termination_date' => now()]);
    }

    /**
     * Reactivate employee access
     */
    private function reactivateEmployeeAccess(Employee $employee): void
    {
        // Reactivate user account if exists
        if ($employee->user) {
            $employee->user->update(['active' => true]);
        }
    }

    /**
     * Cancel pending requests
     */
    private function cancelPendingRequests(int $employeeId): void
    {
        // Cancel pending holidays
        EmployeeHoliday::where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
        
        // Cancel pending permissions
        EmployeePermission::where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
    }

    /**
     * Get employees by department
     */
    public function getByDepartment(int $departmentId): Collection
    {
        return $this->modelClass::where('department_id', $departmentId)
            ->with(['department', 'section'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get employees by section
     */
    public function getBySection(int $sectionId): Collection
    {
        return $this->modelClass::where('section_id', $sectionId)
            ->with(['department', 'section'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active employees
     */
    public function getActiveEmployees(): Collection
    {
        return $this->modelClass::where('status', 'active')
            ->with(['department', 'section'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get employees on leave
     */
    public function getEmployeesOnLeave(): Collection
    {
        return $this->modelClass::whereHas('holidays', function ($query) {
                $query->where('status', 'accepted')
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            })
            ->with(['holidays', 'department', 'section'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Search employees by multiple criteria
     */
    public function searchEmployees(string $term, array $filters = []): Collection
    {
        $query = $this->modelClass::with(['department', 'section']);
        
        // Search term
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('employee_number', 'like', "%{$term}%")
              ->orWhere('identity_number', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
        
        // Apply filters
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                switch ($key) {
                    case 'status':
                        $query->where('status', $value);
                        break;
                    case 'department_id':
                        $query->where('department_id', $value);
                        break;
                    case 'section_id':
                        $query->where('section_id', $value);
                        break;
                    case 'position':
                        $query->where('position', 'like', "%{$value}%");
                        break;
                    case 'hire_from':
                        $query->whereDate('hire_date', '>=', $value);
                        break;
                    case 'hire_to':
                        $query->whereDate('hire_date', '<=', $value);
                        break;
                }
            }
        }
        
        return $query->limit(50)->get();
    }

    /**
     * Get employee statistics
     */
    public function getStatistics(): array
    {
        $stats = parent::getStatistics();
        
        // Add employee specific statistics
        $stats['by_status'] = $this->modelClass::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $stats['by_department'] = $this->modelClass::join('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department_name, COUNT(*) as count')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('count', 'desc')
            ->pluck('count', 'department_name')
            ->toArray();
            
        $stats['by_section'] = $this->modelClass::join('sections', 'employees.section_id', '=', 'sections.id')
            ->selectRaw('sections.name as section_name, COUNT(*) as count')
            ->groupBy('sections.id', 'sections.name')
            ->orderBy('count', 'desc')
            ->pluck('count', 'section_name')
            ->toArray();
            
        $stats['on_leave_today'] = $this->getEmployeesOnLeave()->count();
        $stats['new_this_month'] = $this->modelClass::whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();
        
        // Financial statistics
        $stats['total_salary_cost'] = $this->modelClass::where('status', 'active')
            ->sum('salary');
        $stats['avg_salary'] = $this->modelClass::where('status', 'active')
            ->avg('salary');
        
        // Performance metrics
        $stats['attendance_rate'] = $this->calculateAttendanceRate();
        $stats['turnover_rate'] = $this->calculateTurnoverRate();
        
        return $stats;
    }

    /**
     * Calculate attendance rate
     */
    private function calculateAttendanceRate(): float
    {
        $totalEmployees = $this->modelClass::where('status', 'active')->count();
        
        if ($totalEmployees === 0) {
            return 0;
        }
        
        $presentToday = DB::table('z_k_data')
            ->whereDate('arrival_time', Carbon::today())
            ->selectRaw('COUNT(DISTINCT emp_id) as present_count')
            ->value('present_count');
        
        return round(($presentToday / $totalEmployees) * 100, 1);
    }

    /**
     * Calculate turnover rate
     */
    private function calculateTurnoverRate(): float
    {
        $totalEmployees = $this->modelClass::count();
        
        if ($totalEmployees === 0) {
            return 0;
        }
        
        $terminatedThisYear = $this->modelClass::where('status', 'terminated')
            ->whereYear('termination_date', now()->year)
            ->count();
        
        return round(($terminatedThisYear / $totalEmployees) * 100, 1);
    }

    /**
     * Generate employee number
     */
    private function generateEmployeeNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $count = $this->modelClass::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        
        return "EMP-{$year}{$month}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create initial attendance record
     */
    private function createInitialAttendanceRecord(Employee $employee): void
    {
        // Implementation would depend on attendance system
        // This is a placeholder for attendance record creation
    }

    /**
     * Send creation notifications
     */
    private function sendCreationNotifications(Employee $employee): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Send status change notifications
     */
    private function sendStatusChangeNotifications(Employee $employee, string $oldStatus, string $newStatus): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $term): void
    {
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('employee_number', 'like', "%{$term}%")
              ->orWhere('identity_number', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    /**
     * Apply filter to query
     */
    protected function applyFilter($query, string $key, $value): void
    {
        switch ($key) {
            case 'status':
                $query->where('status', $value);
                break;
            case 'department_id':
                $query->where('department_id', $value);
                break;
            case 'section_id':
                $query->where('section_id', $value);
                break;
            case 'position':
                $query->where('position', 'like', "%{$value}%");
                break;
            case 'name':
                $query->where('name', 'like', "%{$value}%");
                break;
            case 'hire_from':
                $query->whereDate('hire_date', '>=', $value);
                break;
            case 'hire_to':
                $query->whereDate('hire_date', '<=', $value);
                break;
            default:
                parent::applyFilter($query, $key, $value);
        }
    }

    /**
     * Bulk update employee status
     */
    public function bulkUpdateStatus(array $employeeIds, string $status): int
    {
        $count = 0;
        
        foreach ($employeeIds as $employeeId) {
            $employee = $this->findById($employeeId);
            if ($employee) {
                $this->update($employeeId, ['status' => $status]);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get employees with upcoming holidays
     */
    public function getEmployeesWithUpcomingHolidays(int $days = 7): Collection
    {
        return $this->modelClass::whereHas('holidays', function ($query) use ($days) {
                $query->where('status', 'accepted')
                      ->where('start_date', '>=', now())
                      ->where('start_date', '<=', now()->addDays($days));
            })
            ->with(['holidays' => function ($query) use ($days) {
                $query->where('status', 'accepted')
                      ->where('start_date', '>=', now())
                      ->where('start_date', '<=', now()->addDays($days));
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get employees with expiring documents
     */
    public function getEmployeesWithExpiringDocuments(int $days = 30): Collection
    {
        // This would check for expiring contracts, certifications, etc.
        // Implementation depends on specific document tracking requirements
        return collect([]);
    }
}
