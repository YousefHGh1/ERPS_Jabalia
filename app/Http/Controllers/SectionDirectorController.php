<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Section;
use App\Models\SectionDirector;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionDirectorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index sectiondirector', ['only' => ['index']]);
        $this->middleware('permission:store sectiondirector', ['only' => ['store']]);
        $this->middleware('permission:update sectiondirector', ['only' => ['update']]);
        $this->middleware('permission:delete sectiondirector', ['only' => ['destroy']]);
    }
    public function index()
    {
        $directors = SectionDirector::with('section', 'employee.job')->paginate(10);
        $sections  = Section::get(['id', 'name']);
        $employees = Employee::get(['id', 'name']);
        return view('section.director',
            compact('directors', 'sections', 'employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'section_id'    => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('section_directors')->where(fn($query) => $query->where('section_id', $request->section_id)->whereNull('deleted_at'))],
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
            'unique'        => __('unique'),
        ]);

        SectionDirector::create($validatedData);
        return redirect()->route('sectiondirector.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $director      = SectionDirector::findOrfail($id);
        $validatedData = $request->validate([
            'section_id'    => 'required',
            'employee_id'   => 'required|exists:employees,id',
            'director_type' => ['required', Rule::in(['permanent', 'acting']), Rule::unique('section_directors')->ignore($id)->where(fn($query) => $query->where('section_id', $request->section_id)->whereNull('deleted_at'))],
        ]);
        $director->update($validatedData);

        return redirect()->route('sectiondirector.index')
            ->with('success', __('updated successfully'));
    }
    public function destroy($id)
    {
        $director = SectionDirector::findOrfail($id);
        $director->delete();
        return redirect()->route('sectiondirector.index')
            ->with('success', __('deleted successfully'));

    }
}
