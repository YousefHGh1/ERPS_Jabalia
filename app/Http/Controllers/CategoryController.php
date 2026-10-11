<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
        $this->middleware('permission:index categories', ['only' => ['index']]);
        $this->middleware('permission:store categories', ['only' => ['store']]);
        $this->middleware('permission:update categories', ['only' => ['update']]);
        $this->middleware('permission:delete categories', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name       = $request->input('name');
        $categories = Category::with('degrees')->when($name, function ($query) use ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        })->paginate(10)->appends(request()->all());

        return view('categories.index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validator = $request->validate(
            [
                'name'             => 'required|string|max:255',
                'start_degree'     => 'required|string|max:50',
                'end_degree'       => 'required|string|max:50',
                'basic_salary'     => 'nullable|numeric|min:0',
                'work_nature'      => 'nullable|string|max:255',
                'included_degrees' => 'nullable|string|max:500',
            ],
            [
                "required" => __("validation.required"),
                "string"   => __("validation.string"),
                "max"      => __("validation.max"),
                "numeric"  => __("validation.numeric"),
                "min"      => __("validation.min.numeric"),
            ]
        );
        $this->categoryService->create($validator);
        return redirect()->route('categories.index')->with('success', __('saved successfully'));
    }

    public function update(Request $request, $category)
    {
        $category  = Category::findOrFail($category);
        $validator = $request->validate([
            'name'             => 'required|string|max:255',
            'start_degree'     => 'required|string|max:50',
            'end_degree'       => 'required|string|max:50',
            'basic_salary'     => 'nullable|numeric|min:0',
            'work_nature'      => 'nullable|string|max:255',
            'included_degrees' => 'nullable|string|max:500',
        ], [
            "required" => __("validation.required"),
            "string"   => __("validation.string"),
            "max"      => __("validation.max"),
            "numeric"  => __("validation.numeric"),
            "min"      => __("validation.min.numeric"),
        ]);
        $this->categoryService->update($category, $validator);
        return redirect()->route('categories.index')->with('success', __('updated successfully'));
    }

    public function destroy(Category $category)
    {
        if (! $this->categoryService->deleteIfNotUsed($category)) {
            return redirect()->back()->with('warning', __('المصدر مرتبط ... لا يمكن حذفه'));
        }
        return redirect()->route('categories.index')->with('success', __('deleted successfully'));
    }
}
