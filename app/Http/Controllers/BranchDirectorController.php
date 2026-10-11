<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchDirector;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchDirectorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index branchdirector', ['only' => ['index']]);
        $this->middleware('permission:store branchdirector', ['only' => ['store']]);
        $this->middleware('permission:update branchdirector', ['only' => ['update']]);
        $this->middleware('permission:delete branchdirector', ['only' => ['destroy']]);
    }
    public function index()
    {
        $directors = BranchDirector::with('branch', 'employee.job')->paginate(10);
        $branches  = Branch::get(['id', 'name']);
        $employees = Employee::get(['id', 'name']);
        return view('branch.director',
            compact('directors', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'branch_id'     => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('branch_directors')->where(fn($query) => $query->where('branch_id', $request->branch_id)->whereNull('deleted_at'))],
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        BranchDirector::create($validatedData);
        return redirect()->route('branchdirector.index')
            ->with('success', __('saved successfully'));
    }
    public function update(Request $request, $id)
    {
        $director      = BranchDirector::findOrfail($id);
        $validatedData = $request->validate([
            'branch_id'     => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('branch_directors')->ignore($id)->where(fn($query) => $query->where('branch_id', $request->branch_id)->whereNull('deleted_at'))],
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        $director->update($validatedData);
        return redirect()->route('branchdirector.index')
            ->with('success', __('updated successfully'));
    }
    public function destroy($id)
    {
        $director = BranchDirector::findOrfail($id);
        $director->delete();
        return redirect()->route('branchdirector.index')
            ->with('success', __('deleted successfully'));
    }
}
