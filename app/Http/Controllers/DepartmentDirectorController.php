<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentDirector;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentDirectorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index departmentdirector', ['only' => ['index']]);
        $this->middleware('permission:store departmentdirector', ['only' => ['store']]);
        $this->middleware('permission:update departmentdirector', ['only' => ['update']]);
        $this->middleware('permission:delete departmentdirector', ['only' => ['destroy']]);
    }

    public function index()
    {
        $directors   = DepartmentDirector::with('department', 'employee.job')->paginate(10);
        $departments = Department::get(['id', 'name']);
        $employees   = Employee::get(['id', 'name']);
        return view('department.director',
            compact('directors', 'departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'department_id' => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('department_directors')->where(fn($query) => $query->where('department_id', $request->department_id)->whereNull('deleted_at'))],
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        DepartmentDirector::create($validatedData);
        return redirect()->route('departmentdirector.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $director      = DepartmentDirector::findOrfail($id);
        $validatedData = $request->validate([
            'department_id' => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('department_directors')->ignore($id)->where(fn($query) => $query->where('department_id', $request->department_id)->whereNull('deleted_at'))],
        ]);
        $director->update($validatedData);
        return redirect()->route('departmentdirector.index')
            ->with('success', __('updated successfully'));
    }

    public function destroy($id)
    {
        $director = DepartmentDirector::findOrfail($id);
        $director->delete();
        return redirect()->route('departmentdirector.index')
            ->with('success', __('deleted successfully'));

    }
}
