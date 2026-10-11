<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeMainHoliday;
use App\Models\MainHoliday;
use Illuminate\Http\Request;

class MainHolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index main_holiday', ['only' => ['index']]);
        $this->middleware('permission:store main_holiday', ['only' => ['store']]);
        $this->middleware('permission:update main_holiday', ['only' => ['update']]);
        $this->middleware('permission:delete main_holiday', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('holiday.EmployeeMainHolidays',[
            "holidaies" => MainHoliday::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'holiday_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'required' => __('required'),
            'string' => __('string'),
        ]);
       $MainHoliday = MainHoliday::create([
            'holiday_name' => $request->holiday_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        $employees = Employee::get(['id','name']);
        foreach($employees as $employee){
            EmployeeMainHoliday::create([
                'employee_id'      => $employee->id,
                'main_holiday_id'  => $MainHoliday->id,
            ]);
        }
        return redirect()->back()->with('success', __('saved successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MainHoliday $mainHoliday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MainHoliday $mainHoliday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MainHoliday $mainHoliday)
    {
        $validatedData = $request->validate([
            'holiday_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'required' => __('required'),
            'string' => __('string'),
        ]);

        $mainHoliday->update([
            'holiday_name' => $request->holiday_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->back()->with('success', __('updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MainHoliday $mainHoliday)
    {
        // حذف جميع سجلات الموظفين المرتبطة بهذه العطلة
        EmployeeMainHoliday::where('main_holiday_id', $mainHoliday->id)->delete();

        // حذف العطلة الرئيسية
        $mainHoliday->delete();

        return redirect()->back()->with('success', __('deleted successfully'));
    }
}
