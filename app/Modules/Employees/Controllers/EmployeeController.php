<?php

namespace App\Modules\Employees\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;
use App\Modules\Employees\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends BaseModuleController
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'department_id', 'section_id', 'position', 'hire_from', 'hire_to']);
        $employees = $this->getService()->getAll($filters, 15);
        
        $statistics = $this->getService()->getStatistics();
        $departments = $this->getDepartments();
        $sections = $this->getSections();
        $statuses = $this->getStatuses();

        return $this->indexResponse([
            'employees' => $employees,
            'statistics' => $statistics,
            'departments' => $departments,
            'sections' => $sections,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = $this->getDepartments();
        $sections = $this->getSections();
        
        return $this->createResponse([
            'departments' => $departments,
            'sections' => $sections,
        ]);
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $employee = $this->getService()->create($data);
            
            return $this->redirectToIndex(
                'Employee created successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified employee
     */
    public function show($id)
    {
        $employee = $this->getService()->findById($id);
        
        if (!$employee) {
            return $this->errorResponse('Employee not found');
        }

        $this->authorize('view', $employee);
        
        $holidays = $this->getService()->getEmployeeHolidays($id);
        $permissions = $this->getService()->getEmployeePermissions($id);
        $attendance = $this->getService()->getEmployeeAttendance($id);

        return $this->showResponse([
            'employee' => $employee,
            'holidays' => $holidays,
            'permissions' => $permissions,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit($id)
    {
        $employee = $this->getService()->findById($id);
        
        if (!$employee) {
            return $this->errorResponse('Employee not found');
        }

        $this->authorize('update', $employee);
        
        $departments = $this->getDepartments();
        $sections = $this->getSections();
        $statuses = $this->getStatuses();

        return $this->editResponse([
            'employee' => $employee,
            'departments' => $departments,
            'sections' => $sections,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $employee = $this->getService()->update($id, $data);
            
            return $this->redirectToIndex(
                'Employee updated successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id)
    {
        try {
            $employee = $this->getService()->findById($id);
            
            if (!$employee) {
                return $this->errorResponse('Employee not found');
            }

            $this->authorize('delete', $employee);
            
            $this->getService()->delete($id);
            
            return $this->redirectToIndex(
                'Employee deleted successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update employee status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'status' => 'required|in:active,inactive,suspended,terminated',
                'notes' => 'nullable|string|max:1000',
            ]);

            $employee = $this->getService()->update($id, $data);
            
            return $this->successResponse('Employee status updated successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get employees by department
     */
    public function byDepartment($departmentId)
    {
        $employees = $this->getService()->getByDepartment($departmentId);
        
        return response()->json($employees);
    }

    /**
     * Get employees by section
     */
    public function bySection($sectionId)
    {
        $employees = $this->getService()->getBySection($sectionId);
        
        return response()->json($employees);
    }

    /**
     * Get active employees
     */
    public function active()
    {
        $employees = $this->getService()->getActiveEmployees();
        
        return response()->json($employees);
    }

    /**
     * Get employees on leave
     */
    public function onLeave()
    {
        $employees = $this->getService()->getEmployeesOnLeave();
        
        return response()->json($employees);
    }

    /**
     * Search employees
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $filters = $request->only(['status', 'department_id', 'section_id', 'position', 'hire_from', 'hire_to']);
        
        $employees = $this->getService()->searchEmployees($term, $filters);
        
        return response()->json($employees);
    }

    /**
     * Bulk update employee status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $data = $request->validate([
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id',
                'status' => 'required|in:active,inactive,suspended,terminated',
            ]);

            $count = $this->getService()->bulkUpdateStatus($data['employee_ids'], $data['status']);
            
            return response()->json([
                'success' => true,
                'message' => "Updated {$count} employees successfully",
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employees with upcoming holidays
     */
    public function upcomingHolidays(Request $request)
    {
        $days = $request->get('days', 7);
        $employees = $this->getService()->getEmployeesWithUpcomingHolidays($days);
        
        return response()->json($employees);
    }

    /**
     * Get employees with expiring documents
     */
    public function expiringDocuments(Request $request)
    {
        $days = $request->get('days', 30);
        $employees = $this->getService()->getEmployeesWithExpiringDocuments($days);
        
        return response()->json($employees);
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules(): array
    {
        return [
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
    }

    /**
     * Get departments
     */
    private function getDepartments(): \Illuminate\Support\Collection
    {
        return \App\Models\Department::orderBy('name')->get();
    }

    /**
     * Get sections
     */
    private function getSections(): \Illuminate\Support\Collection
    {
        return \App\Models\Section::orderBy('name')->get();
    }

    /**
     * Get statuses
     */
    private function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'terminated' => 'Terminated',
        ];
    }
}
