<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index Shift', ['only' => ['index']]);
        $this->middleware('permission:store Shift', ['only' => ['store']]);
        $this->middleware('permission:update Shift', ['only' => ['update']]);
        $this->middleware('permission:delete Shift', ['only' => ['destroy']]);
    }
    public function index()
    {
        $Shifts = Shift::paginate(10);

        return view('Shift.index', compact('Shifts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'days' => 'required|array',
            'start_late' => 'required|integer',
            'end_late' => 'required|integer',
            'start_shift' => 'required|date',
            'end_shift' => 'required|date|after:start_shift',
        ], [
            'required' => __('required'),
            'string' => __('string'),
            'numeric' => __('numeric'),
        ]);
        Shift::create($validatedData);

        return redirect()->back()
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days' => 'required|array',
            'start_late' => 'required|integer',
            'end_late' => 'required|integer',
            'start_shift' => 'required|date',
            'end_shift' => 'required|date|after:start_shift',
        ], [
            'required' => __('required'),
            'string' => __('string'),
            'numeric' => __('numeric'),
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update($validatedData);
        return redirect()->back()->with('success', 'updated successfully');
    }

    public function destroy($id)
    {

        $shift = Shift::findOrfail($id);
        if ($shift->employeeJobInfos()->count() > 0) {
            session()->flash('warning', 'المصدر مرتبط ... لا يمكن حذفه');
            return redirect()->back();
        }
        $shift->delete();
        return redirect()->back()
            ->with('success', __('deleted successfully'));
    }
}
