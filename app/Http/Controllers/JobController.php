<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index jobs', ['only' => ['index', 'show']]);
        $this->middleware('permission:store jobs', ['only' => ['store']]);
        $this->middleware('permission:update jobs', ['only' => ['update']]);
        $this->middleware('permission:delete jobs', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name       = $request->input('name');
        $categoryId = $request->input('category_id');

        $jobs = Job::with('category')
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->paginate(10)->appends(request()->all());

        $categories = Category::get(['id', 'name']);
        return view('jobs.index', [
            'jobs'       => $jobs,
            'categories' => $categories,
        ]);
    }

    public function show(Job $job)
    {
        $job->load('category');

        return view('jobs.show', [
            'job' => $job,
        ]);
    }

    public function store(Request $request)
    {
        $validator = $request->validate(
            [
                'name'        => 'required|string|max:255|regex:/^[\p{L}\s]+$/u',
                'category_id' => 'required|numeric',
            ],
            [
                "required" => __('required'),
                "string"   => __('string'),
            ]
        );
        $job = Job::create($validator);
        
        // Log activity
        activity()
            ->performedOn($job)
            ->causedBy(auth()->user())
            ->log('تم إضافة وظيفة: ' . $job->name);
        
        return redirect()->route('jobs.index')
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, Job $job)
    {
        $validator = $request->validate(
            [
                'name'        => 'required|string|max:255|regex:/^[\p{L}\s]+$/u',
                'category_id' => 'required|numeric',
            ],
            [
                "required" => "__('validation.required')",
                "string"   => "__('validation.string')",
            ]
        );
        $job->update($validator);
        
        // Log activity
        activity()
            ->performedOn($job)
            ->causedBy(auth()->user())
            ->log('تم تعديل وظيفة: ' . $job->name);
        
        return redirect()->route('jobs.index')->with('success', __('updated successfully'));
    }

    public function destroy(Job $job)
    {
        if ($job->employees()->count() > 0) {
            session()->flash('warning', 'المصدر مرتبط ... لا يمكن حذفه');
            return redirect()->back();
        }
        
        // Log activity before delete
        activity()
            ->performedOn($job)
            ->causedBy(auth()->user())
            ->log('تم حذف وظيفة: ' . $job->name);
        
        Job::findOrFail($job->id)->delete();
        return redirect()->route('jobs.index')->with('success', 'deleted successfully');
    }
}
