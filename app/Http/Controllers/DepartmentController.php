<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index departments', ['only' => ['index']]);
        $this->middleware('permission:store departments', ['only' => ['store']]);
        $this->middleware('permission:update departments', ['only' => ['update']]);
        $this->middleware('permission:delete departments', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name = $request->input('name');

        $departments = Department::with(['director.employee'])
            ->withCount('sections')
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->paginate(10)
            ->appends(request()->all());

        return view('department.index', compact('departments'));
    }
    
    public function create()
    {
        return view('department.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string||regex:/^[\p{L}\s]+$/u|max:255',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric')
        ]);
        $department = Department::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($department)
            ->causedBy(auth()->user())
            ->log('تم إضافة إدارة: ' . $department->name);
        
        return redirect()->route('departments.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => 'required|string||regex:/^[\p{L}\s]+$/u|max:255',
        ]);

        $department->update($validatedData);
        
        // Log activity
        activity()
            ->performedOn($department)
            ->causedBy(auth()->user())
            ->log('تم تعديل إدارة: ' . $department->name);

        return redirect()->route('departments.index')
            ->with('success', __('updated successfully'));
    }

    public function destroy(Department $department)
    {
        // Set department_id to null for all employees in this department
        $department->employees()->update(['department_id' => null]);
        
        // Log activity before delete
        activity()
            ->performedOn($department)
            ->causedBy(auth()->user())
            ->log('تم حذف إدارة: ' . $department->name);
        
        $department->delete();
        return redirect()->route('departments.index')
            ->with('success', __('deleted successfully'));
    }
}