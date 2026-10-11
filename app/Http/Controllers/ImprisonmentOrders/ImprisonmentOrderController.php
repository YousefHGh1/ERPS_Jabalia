<?php

namespace App\Http\Controllers\ImprisonmentOrders;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImprisonmentOrderRequest;
use App\Http\Requests\UpdateImprisonmentOrderRequest;
use App\Models\ImprisonmentOrder;
use App\Models\ImprisonmentOrderAttachment;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImprisonmentOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index imprisonment_orders', ['only' => ['index', 'show']]);
        $this->middleware('permission:store imprisonment_orders', ['only' => ['store']]);
        $this->middleware('permission:update imprisonment_orders', ['only' => ['update']]);
        $this->middleware('permission:delete imprisonment_orders', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orders = ImprisonmentOrder::with(['creator', 'notice'])
            ->when($search, function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%')
                      ->orWhere('court_name', 'like', '%' . $search . '%')
                      ->orWhere('judge_name', 'like', '%' . $search . '%')
                      ->orWhere('defendant_name', 'like', '%' . $search . '%')
                      ->orWhere('defendant_identity', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('order_date', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $notices = Notice::orderBy('id', 'desc')->limit(500)->get(['id', 'notice_number', 'year', 'notice_id']);

        return view('imprisonment_orders.index', compact('orders', 'notices'));
    }

    public function create()
    {
        $query = request()->only(['defendant_identity', 'defendant_name', 'identity_number', 'name']);
        return redirect()->route('imprisonment_orders.index', array_filter($query))->with('openAddModal', true);
    }

    public function store(StoreImprisonmentOrderRequest $request)
    {
        if ($request->has('notice_id') && $request->has('order_number')) {
            return $this->storeAudiencePenFormat($request);
        }

        try {
            DB::beginTransaction();

            $orderData = [
                'order_number' => $request->order_number,
                'order_date' => $request->order_date,
                'court_name' => $request->court_name,
                'judge_name' => $request->judge_name,
                'defendant_name' => $request->defendant_name,
                'defendant_identity' => $request->defendant_identity,
                'imprisonment_period' => $request->imprisonment_period,
                'imprisonment_type' => $request->imprisonment_type,
                'case_number' => $request->case_number,
                'case_year' => $request->case_year,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ];

            $order = ImprisonmentOrder::create($orderData);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('imprisonment_order_attachments', 'public');
                    ImprisonmentOrderAttachment::create([
                        'imprisonment_order_id' => $order->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة أمر الحبس بنجاح']);
            }
            return redirect()->route('imprisonment_orders.index')->with('success', 'تم إضافة أمر الحبس بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة أمر الحبس: ' . $e->getMessage()], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة أمر الحبس: ' . $e->getMessage());
        }
    }

    protected function storeAudiencePenFormat(Request $request)
    {
        try {
            DB::beginTransaction();

            $orderNumberUnique = 'IO-' . $request->notice_id . '-' . $request->order_number;
            if (ImprisonmentOrder::where('order_number', $orderNumberUnique)->exists()) {
                throw ValidationException::withMessages(['order_number' => ['رقم أمر الحبس لهذا الإخطار مستخدم مسبقاً.']]);
            }

            $orderDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->order_date);

            ImprisonmentOrder::create([
                'notice_id' => $request->notice_id,
                'order_number' => $orderNumberUnique,
                'order_date' => $orderDate->toDateString(),
                'court_name' => '—',
                'judge_name' => '—',
                'defendant_name' => '—',
                'defendant_identity' => '—',
                'imprisonment_period' => '—',
                'imprisonment_type' => '—',
                'case_number' => '—',
                'case_year' => $orderDate->year,
                'description' => null,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة أمر الحبس بنجاح']);
            }
            return redirect()->route('imprisonment_orders.index')->with('success', 'تم إضافة أمر الحبس بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة أمر الحبس: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة أمر الحبس: ' . $e->getMessage());
        }
    }

    public function show(ImprisonmentOrder $imprisonment_order)
    {
        return redirect()->route('imprisonment_orders.edit', $imprisonment_order);
    }

    public function edit(ImprisonmentOrder $imprisonment_order)
    {
        $imprisonment_order->load(['attachments']);
        if (request()->has('modal') && request()->get('modal') == '1') {
            return view('imprisonment_orders._edit_form', ['order' => $imprisonment_order]);
        }
        return view('imprisonment_orders.edit', ['order' => $imprisonment_order]);
    }

    public function update(UpdateImprisonmentOrderRequest $request, ImprisonmentOrder $imprisonment_order)
    {
        try {
            DB::beginTransaction();

            $imprisonment_order->update([
                'order_number' => $request->order_number,
                'order_date' => $request->order_date,
                'court_name' => $request->court_name,
                'judge_name' => $request->judge_name,
                'defendant_name' => $request->defendant_name,
                'defendant_identity' => $request->defendant_identity,
                'imprisonment_period' => $request->imprisonment_period,
                'imprisonment_type' => $request->imprisonment_type,
                'case_number' => $request->case_number,
                'case_year' => $request->case_year,
                'description' => $request->description,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('imprisonment_order_attachments', 'public');
                    ImprisonmentOrderAttachment::create([
                        'imprisonment_order_id' => $imprisonment_order->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم تحديث أمر الحبس بنجاح']);
            }
            return redirect()->route('imprisonment_orders.index')->with('success', 'تم تحديث أمر الحبس بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث أمر الحبس: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث أمر الحبس: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, ImprisonmentOrder $imprisonment_order)
    {
        try {
            $imprisonment_order->load('attachments');
            foreach ($imprisonment_order->attachments as $attachment) {
                try {
                    Storage::disk('public')->delete($attachment->file_path);
                } catch (\Throwable $e) {}
                $attachment->delete();
            }
            $imprisonment_order->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم حذف أمر الحبس بنجاح']);
            }
            return redirect()->route('imprisonment_orders.index')->with('success', 'تم حذف أمر الحبس بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف أمر الحبس: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->route('imprisonment_orders.index')->with('error', 'حدث خطأ أثناء حذف أمر الحبس: ' . $e->getMessage());
        }
    }

    public function downloadAttachment(ImprisonmentOrder $order, ImprisonmentOrderAttachment $attachment)
    {
        $filePath = storage_path('app/public/' . $attachment->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->file_name);
        }
        return back()->with('error', 'الملف غير موجود');
    }

    public function deleteAttachment(ImprisonmentOrder $order, ImprisonmentOrderAttachment $attachment)
    {
        try {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage());
        }
    }
}
