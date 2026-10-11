<?php
namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\RequestType;
use App\Models\RequestWorkflow;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RequestTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index request_types', ['only' => ['index']]);
        $this->middleware('permission:store request_types', ['only' => ['store']]);
        $this->middleware('permission:update request_types', ['only' => ['update']]);
        $this->middleware('permission:delete request_types', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = RequestType::query()->withCount('workflows');

        // البحث بالاسم
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $requestTypes = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $page_title = 'أنواع الطلبات';
        return view('requests.types.index', compact('page_title', 'requestTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $defaultSection = Section::where('name', 'قسم قلم الجمهور')->first();

        if (! $defaultSection) {
            $message = 'لا يوجد قسم قلم الجمهور. يرجى إضافته أولاً قبل إنشاء نوع طلب جديد.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $message);
        }

        $type = RequestType::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        // إنشاء المسار الأول تلقائياً عند إنشاء نوع طلب جديد
        $firstWorkflow = RequestWorkflow::firstOrCreate(
            [
                'request_type_id'      => $type->id,
                'previous_workflow_id' => null,
            ],
            [
                'section_id'  => $defaultSection->id,
                'name'        => 'استلام الطلب',
                'description' => 'استلام وتسجيل الطلب الجديد',
                'step_order'  => 1,
                'is_active'   => true,
            ]
        );

        Cache::forget('request_types_all');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة نوع الطلب بنجاح',
                'data'    => [
                    'id'       => $type->id,
                    'name'     => $type->name,
                    'workflow' => $firstWorkflow,
                ],
            ]);
        }

        return redirect()->route('request-types.index')
            ->with('success', 'تم الحفظ بنجاح');
    }

    public function edit($id)
    {
        $type = RequestType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $type = RequestType::findOrFail($id);
        $type->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('request-types.index')
            ->with('success', 'تم التحديث بنجاح');
    }

    public function getRequestTypeData()
    {
        // This method is no longer needed but kept for backward compatibility
        return response()->json(['data' => []]);
    }

    public function destroy($id)
    {
        $type = RequestType::findOrFail($id);
        $type->delete();
        Cache::forget('request_types_all');
        return redirect()->route('request-types.index')
            ->with('success', 'تم الحذف بنجاح');
    }
}