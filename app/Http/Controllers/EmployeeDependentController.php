<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EmployeeDependentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index employee_dependent', ['only' => ['index']]);
        $this->middleware('permission:store employee_dependent', ['only' => ['store']]);
        $this->middleware('permission:update employee_dependent', ['only' => ['update']]);
        $this->middleware('permission:delete employee_dependent', ['only' => ['destroy']]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'czn_id' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth' => 'required|date',
            'relation' => 'required|string|max:255',
            'status' => 'required|in:Active,InActive',
            'attachment' => 'required|file|mimes:pdf', 
            'employee_id' =>'required'
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        if ($request->hasFile('attachment')) {
            $validatedData['attachment'] = $request->attachment->store('employee_dependents', 'public');
        } 
        $dependent = EmployeeDependent::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($dependent)
            ->causedBy(auth()->user())
            ->log('تم إضافة معال: ' . $validatedData['name']);
        
        return redirect()->back()
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {   
        $employeeDependent = EmployeeDependent::findOrfail($id);
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'czn_id'    => 'required|string|max:255',
            'gender'    => 'required|in:male,female',
            'birth'     => 'required|date',
            'relation'  => 'required|string|max:255',
            'status'    => 'required|in:Active,InActive',
            'employee_id' =>'required'
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        if ($request->hasFile('attachment')) {
            File::delete('storage/'.$employeeDependent->attachment);
            $validatedData['attachment'] = $request->attachment->store('employee_dependents', 'public');
        } 
       $employeeDependent->update($validatedData);
       
       // Log activity
       activity()
            ->performedOn($employeeDependent)
            ->causedBy(auth()->user())
            ->log('تم تعديل معال: ' . $validatedData['name']);

        return redirect()->back()
            ->with('success', __('updated successfully'));
    }
    public function destroy($id)
    {
        $EmployeeDependent = EmployeeDependent::findOrFail($id);
        
        // Log activity before delete
        activity()
            ->performedOn($EmployeeDependent)
            ->causedBy(auth()->user())
            ->log('تم حذف معال: ' . $EmployeeDependent->name);
        
        $EmployeeDependent->delete();
        File::delete('storage/'.$EmployeeDependent->attachment);
        return redirect()->back()->with('success', 'deleted successfully');
    }
}
