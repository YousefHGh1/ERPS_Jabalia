<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index sections', ['only' => ['index']]);
        $this->middleware('permission:store sections', ['only' => ['store']]);
        $this->middleware('permission:update sections', ['only' => ['update']]);
        $this->middleware('permission:delete sections', ['only' => ['destroy']]);
    }


    public function index(Request $request)
    {
        $name = $request->input('name');
        $departmentId = $request->input('department_id');


        $sections = Section::with(['department', 'director.employee'])
            ->withCount('branches')
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->paginate(10)
            ->appends(request()->all());

        $departments = Department::get(['id', 'name']);
        return view('section.index', compact('sections', 'departments'));
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string||regex:/^[\p{L}\s]+$/u|max:255',
            'department_id' => 'required',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric')
        ]);
        $section = Section::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($section)
            ->causedBy(auth()->user())
            ->log('تم إضافة قسم: ' . $section->name);
        
        return redirect()->route('sections.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, Section $section)
    {
        $validatedData = $request->validate([
            'name' => 'required|string||regex:/^[\p{L}\s]+$/u|max:255',
            'department_id' => 'required',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric')
        ]);

        $section->update($validatedData);
        
        // Log activity
        activity()
            ->performedOn($section)
            ->causedBy(auth()->user())
            ->log('تم تعديل قسم: ' . $section->name);

        return redirect()->route('sections.index')
            ->with('success', __('updated successfully'));
    }
    public function destroy(Section $section)
    {
        // Set section_id to null for all employees in this section
        $section->employees()->update(['section_id' => null]);
        
        // Log activity before delete
        activity()
            ->performedOn($section)
            ->causedBy(auth()->user())
            ->log('تم حذف قسم: ' . $section->name);
        
        $section->delete();
        return redirect()->route('sections.index')
            ->with('success', __('deleted successfully'));
    }
}