<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Employee;
use App\Models\EmployeeBonus;
use Illuminate\Http\Request;

class EmployeeBonusController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index employee_bonus', ['only' => ['index']]);
        $this->middleware('permission:store employee_bonus', ['only' => ['store']]);
        $this->middleware('permission:update employee_bonus', ['only' => ['update']]);
        $this->middleware('permission:delete employee_bonus', ['only' => ['destroy']]);
    }

    public function index()
    {
        $bonus_employees = EmployeeBonus::with('bonus', 'employee')->paginate(10);
        $bonuses = Bonus::get(['id', 'name']);
        $employees = Employee::get(['id', 'name']);

        return view('bonus.employee',
            compact('employees', 'bonuses', 'bonus_employees'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bonus_id' => 'required',
            'employee_id' => 'required',
            'total_amount' =>'required|numeric'
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        $employeeBonus = EmployeeBonus::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($employeeBonus)
            ->causedBy(auth()->user())
            ->log('تم إضافة علاوة للموظف');
        
        return redirect()->back()
            ->with('success', __('saved successfully'));
    }
    public function update(Request $request,  $id)
    {
        $EmployeeBonus = EmployeeBonus::findOrfail($id);
        $validatedData = $request->validate([
            'bonus_id' => 'required',
            'employee_id' => 'required',
            'total_amount' =>'required|numeric'
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        $EmployeeBonus->update($validatedData);
        
        // Log activity
        activity()
            ->performedOn($EmployeeBonus)
            ->causedBy(auth()->user())
            ->log('تم تعديل علاوة الموظف');

        return redirect()->back()
            ->with('success', __('updated successfully'));
    }
    public function destroy($id)
    {
        $director = EmployeeBonus::findOrfail($id);
        
        // Log activity before delete
        activity()
            ->performedOn($director)
            ->causedBy(auth()->user())
            ->log('تم حذف علاوة الموظف');
        
        $director->delete();
        return redirect()->back()
            ->with('success', __('deleted successfully'));

    }
}
