<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Section;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index branches', ['only' => ['index']]);
        $this->middleware('permission:store branches', ['only' => ['store']]);
        $this->middleware('permission:update branches', ['only' => ['update']]);
        $this->middleware('permission:delete branches', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $sectionId = $request->input('section_id');


        $branches = Branch::with(['section', 'director.employee'])
            ->withCount('employees')
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($sectionId, function ($query) use ($sectionId) {
                $query->where('section_id', $sectionId);
            })
            ->paginate(10)
            ->appends(request()->all());

        $sections = Section::get(['id', 'name']);
        return view('branch.index', compact('branches', 'sections'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
            'section_id' => 'required',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric')
        ]);
        Branch::create($validatedData);
        return redirect()->route('branches.index')
            ->with('success', __('saved successfully'));

    }

    public function update(Request $request, Branch $branch)
    {
        $validatedData = $request->validate([
            'name' => 'required|string||regex:/^[\p{L}\s]+$/u|max:255',
            'section_id' => 'required',

        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric')
        ]);
        $branch->update($validatedData);
        return redirect()->route('branches.index')
            ->with('success', __('updated successfully'));
    }

    public function destroy(Branch $branch)
    {
        // Set branch_id to null for all employees in this branch
        $branch->employees()->update(['branch_id' => null]);
        
        $branch->delete();
        return redirect()->route('branches.index')
            ->with('success', __('deleted successfully'));
    }
}
