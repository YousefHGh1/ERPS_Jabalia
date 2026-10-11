<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index holidays', ['only' => ['index']]);
        $this->middleware('permission:store holidays', ['only' => ['store']]);
        $this->middleware('permission:update holidays', ['only' => ['update']]);
        $this->middleware('permission:delete holidays', ['only' => ['destroy']]);
    }
    public function index()
    {
        $holidays = Holiday::paginate(10);
        return view('holiday.index',
            compact('holidays'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'num_of_days' => 'required|numeric',
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        $holiday = Holiday::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($holiday)
            ->causedBy(auth()->user())
            ->log('تم إضافة عطلة: ' . $holiday->name);

        return redirect()->route('holidays.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrfail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'num_of_days' => 'required|numeric',
        ],[
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string'=> __('string'),
            'regex'=> __('regex'),
            'numeric' => __('numeric')
        ]);
        $holiday->update($validatedData);
        
        // Log activity
        activity()
            ->performedOn($holiday)
            ->causedBy(auth()->user())
            ->log('تم تعديل عطلة: ' . $holiday->name);

        return redirect()->route('holidays.index')
            ->with('success', __('updated successfully'));
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrfail($id);
        
        // Log activity before delete
        activity()
            ->performedOn($holiday)
            ->causedBy(auth()->user())
            ->log('تم حذف عطلة: ' . $holiday->name);
        
        $holiday->delete();
        return redirect()->route('holidays.index')
            ->with('success', __('deleted successfully'));

    }
}