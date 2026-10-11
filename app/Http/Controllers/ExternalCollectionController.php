<?php

namespace App\Http\Controllers;

use App\Models\ExternalCollection;
use App\Models\ExternalCollectionPayment;
use App\Models\Mokalaf;
use App\Models\Section;
use App\Models\RevenueBatch;
use App\Repositories\RevenueBatchRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ExternalCollectionController extends Controller
{
    public function __construct(
        protected RevenueBatchRepository $batchRepo,
    ) {
        $this->middleware('permission:view external_collections')->only(['index', 'show']);
        $this->middleware('permission:create external_collections')->only(['create', 'store']);
        $this->middleware('permission:edit external_collections')->only(['edit', 'update']);
        $this->middleware('permission:delete external_collections')->only('destroy');
        $this->middleware('permission:payment external_collections')->only(['paymentForm', 'processPayment', 'cancelPayment']);
    }

    /**
     * عرض قائمة التحصيل الخارجي
     */
    public function index()
    {
        $collections = ExternalCollection::with(['mokalaf', 'section', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $sections = Section::with('department')->get();
        $paymentMethods = ExternalCollectionPayment::getPaymentMethods();
        $openBatch = RevenueBatch::getOpenBatch();

        return view('external-collections.index', compact('collections', 'sections', 'paymentMethods', 'openBatch'));
    }

    /**
     * عرض صفحة إنشاء تحصيل خارجي جديد
     */
    public function create()
    {
        $sections = Section::with('department')->get();
        return view('external-collections.create', compact('sections'));
    }

    /**
     * تخزين تحصيل خارجي جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'identity_number' => 'required|string',
            'section_id' => 'required|exists:sections,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // البحث عن المكلف حسب رقم الهوية
        $mokalaf = Mokalaf::where('identity_number', $request->identity_number)->first();

        $collection = ExternalCollection::create([
            'mokalaf_id' => $mokalaf?->id,
            'taxpayer_name' => $mokalaf?->name ?? null,
            'identity_number' => $request->identity_number,
            'section_id' => $request->section_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Log activity
        activity()
            ->performedOn($collection)
            ->causedBy(Auth::user())
            ->log('تم إضافة تحصيل خارجي: ' . $collection->taxpayer_name . ' - المبلغ: ' . $request->amount);

        Alert::success('نجاح', 'تم إضافة التحصيل الخارجي بنجاح');
        return redirect()->route('external-collections.index');
    }

    /**
     * عرض تفاصيل التحصيل الخارجي
     */
    public function show(ExternalCollection $externalCollection)
    {
        $externalCollection->load(['mokalaf', 'section', 'creator', 'updater', 'payments' => function($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }]);
        return view('external-collections.show', compact('externalCollection'));
    }

    /**
     * عرض صفحة تعديل التحصيل الخارجي
     */
    public function edit(ExternalCollection $externalCollection)
    {
        $sections = Section::with('department')->get();
        return view('external-collections.edit', compact('externalCollection', 'sections'));
    }

    /**
     * تحديث التحصيل الخارجي
     */
    public function update(Request $request, ExternalCollection $externalCollection)
    {
        $request->validate([
            'identity_number' => 'required|string',
            'section_id' => 'required|exists:sections,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // البحث عن المكلف حسب رقم الهوية
        $mokalaf = Mokalaf::where('identity_number', $request->identity_number)->first();

        $externalCollection->update([
            'mokalaf_id' => $mokalaf?->id,
            'taxpayer_name' => $mokalaf?->name ?? null,
            'identity_number' => $request->identity_number,
            'section_id' => $request->section_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Log activity
        activity()
            ->performedOn($externalCollection)
            ->causedBy(Auth::user())
            ->log('تم تعديل تحصيل خارجي: ' . $externalCollection->taxpayer_name . ' - المبلغ: ' . $request->amount);

        Alert::success('نجاح', 'تم تحديث التحصيل الخارجي بنجاح');
        return redirect()->route('external-collections.index');
    }

    /**
     * حذف التحصيل الخارجي
     */
    public function destroy(ExternalCollection $externalCollection)
    {
        // Log activity before delete
        activity()
            ->performedOn($externalCollection)
            ->causedBy(Auth::user())
            ->log('تم حذف تحصيل خارجي: ' . $externalCollection->taxpayer_name);
        
        $externalCollection->delete();
        Alert::success('نجاح', 'تم حذف التحصيل الخارجي بنجاح');
        return redirect()->route('external-collections.index');
    }

    /**
     * البحث عن المكلف حسب رقم الهوية (AJAX)
     */
    public function searchTaxpayer(Request $request): JsonResponse
    {
        $identityNumber = $request->input('identity_number');
        
        if (empty($identityNumber)) {
            return response()->json([
                'found' => false,
                'message' => 'يرجى إدخال رقم الهوية'
            ]);
        }

        $mokalaf = Mokalaf::where('identity_number', $identityNumber)->first();

        if ($mokalaf) {
            return response()->json([
                'found' => true,
                'mokalaf' => [
                    'id' => $mokalaf->id,
                    'name' => $mokalaf->name,
                    'identity_number' => $mokalaf->identity_number,
                    'license_number' => $mokalaf->license_number,
                    'address' => $mokalaf->address,
                    'mobile' => $mokalaf->mobile,
                ]
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'لم يتم العثور على مكلف برقم الهوية المحدد'
        ]);
    }

    /**
     * عرض نموذج الدفع للتحصيل الخارجي
     */
    public function paymentForm(ExternalCollection $externalCollection)
    {
        if ($externalCollection->isFullyPaid()) {
            Alert::error('خطأ', 'هذا التحصيل مدفوع بالكامل بالفعل');
            return redirect()->route('external-collections.show', $externalCollection);
        }

        $paymentMethods = ExternalCollectionPayment::getPaymentMethods();
        $openBatch = RevenueBatch::getOpenBatch();
        
        // إذا لم توجد رزمة مفتوحة، قم بإنشاء رزمة موحدة جديدة
        if (!$openBatch) {
            $openBatch = $this->createDailyBatch();
        }
        
        return view('external-collections.payment', compact('externalCollection', 'paymentMethods', 'openBatch'));
    }

    /**
     * معالجة دفع التحصيل الخارجي
     */
    public function processPayment(Request $request, ExternalCollection $externalCollection)
    {
        if ($externalCollection->isFullyPaid()) {
            Alert::error('خطأ', 'هذا التحصيل مدفوع بالكامل بالفعل');
            return redirect()->route('external-collections.show', $externalCollection);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $externalCollection->remaining_amount,
            'payment_method' => 'required|in:' . implode(',', array_keys(ExternalCollectionPayment::getPaymentMethods())),
            'payment_date' => 'required|date|before_or_equal:today',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $externalCollection) {
                $openBatch = RevenueBatch::getOpenBatch();
                
                // إذا لم توجد رزمة مفتوحة، قم بإنشاء رزمة موحدة جديدة
                if (!$openBatch) {
                    $openBatch = $this->createDailyBatch();
                }
                
                $payment = ExternalCollectionPayment::create([
                    'external_collection_id' => $externalCollection->id,
                    'amount' => $request->amount,
                    'balance_after' => $externalCollection->remaining_amount - $request->amount,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'receipt_number' => $request->receipt_number,
                    'notes' => $request->notes,
                    'status' => 'paid',
                    'created_by' => Auth::id(),
                    'revenue_batch_id' => $openBatch?->id,
                    'payment_number' => $this->generatePaymentNumber(),
                ]);

                // Log activity
                activity()
                    ->performedOn($payment)
                    ->causedBy(Auth::user())
                    ->log('تم إضافة دفعة تحصيل خارجي: ' . $externalCollection->taxpayer_name . ' - المبلغ: ' . $request->amount);

                // تحديث حالة الدفع في التحصيل الخارجي
                $externalCollection->updatePaymentStatus();

                // تحديث إجماليات الرزمة اليومية
                if ($openBatch) {
                    $this->batchRepo->updateTotals($openBatch, (float) $payment->amount);
                }
            });

            Alert::success('نجاح', 'تم تسجيل الدفعة بنجاح');
            return redirect()->route('external-collections.show', $externalCollection);
            
        } catch (\Exception $e) {
            Alert::error('خطأ', 'حدث خطأ أثناء معالجة الدفعة: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * إلغاء دفعة التحصيل الخارجي
     */
    public function cancelPayment(Request $request, ExternalCollectionPayment $payment)
    {
        // Debug information
        \Log::info('Cancel Payment Attempt:', [
            'payment_id' => $payment->id,
            'payment_status' => $payment->status,
            'revenue_batch_id' => $payment->revenue_batch_id,
            'revenue_batch_status' => $payment->revenueBatch?->status,
            'is_cancellable' => $payment->isCancellable()
        ]);

        if (!$payment->isCancellable()) {
            Alert::error('خطأ', 'لا يمكن إلغاء هذه الدفعة. الحالة: ' . $payment->status . ', الرزمة: ' . ($payment->revenueBatch?->status ?? 'لا توجد'));
            return redirect()->back();
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            // Log activity before cancel
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->log('تم إلغاء دفعة تحصيل خارجي: ' . $payment->externalCollection->taxpayer_name . ' - السبب: ' . $request->cancellation_reason);
            
            $payment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // تحديث حالة الدفع في التحصيل الخارجي
            $payment->externalCollection->updatePaymentStatus();

            Alert::success('نجاح', 'تم إلغاء الدفعة بنجاح');
            return redirect()->route('external-collections.show', $payment->externalCollection);
            
        } catch (\Exception $e) {
            Alert::error('خطأ', 'حدث خطأ أثناء إلغاء الدفعة: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * توليد رقم دفعة فريد
     */
    protected function generatePaymentNumber(): string
    {
        $date = now()->format('Y-m-d');
        $count = ExternalCollectionPayment::whereDate('created_at', now())->count() + 1;
        return "EC-{$date}-" . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * إصلاح حسابات الدفع للسجلات الموجودة (للاستخدام الداخلي)
     */
    public function fixPaymentCalculations(ExternalCollection $externalCollection)
    {
        try {
            $externalCollection->recalculatePaymentStatus();
            
            Alert::success('نجاح', 'تم إصلاح حسابات الدفع بنجاح');
            return redirect()->back();
            
        } catch (\Exception $e) {
            Alert::error('خطأ', 'حدث خطأ أثناء إصلاح الحسابات: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * إنشاء رزمة يومية جديدة
     */
    private function createDailyBatch(): RevenueBatch
    {
        // الحصول على الرزمة الموحدة لليوم
        $today = now()->format('Y-m-d');
        $existingBatch = RevenueBatch::whereDate('date', $today)
            ->where('batch_kind', RevenueBatch::KIND_UNIFIED)
            ->first();

        if ($existingBatch) {
            return $existingBatch;
        }

        // الحصول على أكبر رقم تسلسلي للرزم اليومية
        $maxSequence = RevenueBatch::whereDate('date', $today)
            ->max('sequence_number') ?? 0;

        // إنشاء رزمة موحدة جديدة
        return RevenueBatch::create([
            'date' => $today,
            'status' => 'open',
            'batch_kind' => RevenueBatch::KIND_UNIFIED,
            'opened_by_user_id' => Auth::id(),
            'opened_at' => now(),
            'sequence_number' => $maxSequence + 1,
            'total_amount' => 0,
            'payments_count' => 0,
        ]);
    }
}