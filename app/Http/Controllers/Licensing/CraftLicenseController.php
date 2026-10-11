<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\CraftLicense;
use App\Models\CraftFeeHistory;
use App\Models\Payment;
use App\Models\CraftLicenseAttachment;
use App\Services\CraftLicenseService;
use App\Services\CentralTreasuryPaymentService;
use App\Http\Requests\StoreCraftLicenseRequest;
use App\Http\Requests\UpdateCraftLicenseRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CraftLicenseController extends Controller
{
    protected $service;
    protected CentralTreasuryPaymentService $centralTreasuryPaymentService;

    public function __construct(CraftLicenseService $service, CentralTreasuryPaymentService $centralTreasuryPaymentService)
    {
        $this->service = $service;
        $this->centralTreasuryPaymentService = $centralTreasuryPaymentService;
        $this->middleware('permission:index craft_licenses', ['only' => ['index', 'show', 'searchByFileNumber']]);
        $this->middleware('permission:create craft_licenses', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit craft_licenses', ['only' => ['edit', 'update', 'addPayment', 'addPartner', 'deletePartner']]);
        $this->middleware('permission:delete craft_licenses', ['only' => ['destroy']]);
        $this->middleware('permission:print craft_licenses', ['only' => ['print']]);
        $this->middleware('permission:cancel_payment craft_licenses', ['only' => ['cancelPayment']]);
        $this->middleware('permission:renew craft_licenses', ['only' => ['renew']]);
        $this->middleware('permission:renew craft_licenses', ['only' => ['accrueAnnualFees']]);
    }

    /**
     * ترصيد الرسوم السنوية لكل رخص الحرف للسنة الحالية (عبر زر من شاشة الـ index).
     */
    public function accrueAnnualFees()
    {
        $this->service->accrueAnnualFeesForCurrentYear();

        return redirect()
            ->route('craft_licenses.index')
            ->with('success', 'تم ترصيد رسوم السنة الحالية لجميع رخص الحرف النشطة التي تحتاج إلى ترصيد.');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $file_number = $request->input('file_number');
        $status = $request->input('status');
        $classification = $request->input('classification');
        $detailed_craft_type = $request->input('detailed_craft_type');

        $licenses = CraftLicense::with(['partners', 'attachments'])
            ->latest()
            ->when($search, function ($query) use ($search) {
                $query->where('commercial_name', 'like', '%' . $search . '%')
                    ->orWhere('owner_name', 'like', '%' . $search . '%')
                    ->orWhere('detailed_craft_type', 'like', '%' . $search . '%');
            })
            ->when($file_number, function ($query) use ($file_number) {
                $query->where('file_number', $file_number);
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 'unpaid') {
                    $query->where('remaining', '>', 0);
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($classification, function ($query) use ($classification) {
                $query->where('classification', $classification);
            })
            ->when($detailed_craft_type, function ($query) use ($detailed_craft_type) {
                $query->where('detailed_craft_type', $detailed_craft_type);
            })
            ->paginate(20)->appends(request()->all());

        $next_file_number = $this->getNextFileNumber();
        return view('craft_licenses.index', compact('licenses', 'next_file_number'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $file_number = $request->input('file_number');
        $status = $request->input('status');

        $licenses = CraftLicense::with(['partners', 'attachments'])
            ->latest()
            ->when($search, function ($query) use ($search) {
                $query->where('commercial_name', 'like', '%' . $search . '%')
                    ->orWhere('owner_name', 'like', '%' . $search . '%');
            })
            ->when($file_number, function ($query) use ($file_number) {
                $query->where('file_number', $file_number);
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 'unpaid') {
                    $query->where('remaining', '>', 0);
                } else {
                    $query->where('status', $status);
                }
            })
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=craft_licenses_export_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($licenses) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                __('File Number'),
                __('Commercial Name'),
                __('Owner Name'),
                __('Neighborhood'),
                __('Final Price'),
                __('Remaining Amount'),
                __('Status'),
                __('Registration Date')
            ]);

            foreach ($licenses as $item) {
                fputcsv($file, [
                    $item->file_number,
                    $item->commercial_name,
                    $item->owner_name,
                    $item->neighborhood,
                    $item->final_price,
                    $item->remaining,
                    $item->status,
                    $item->registration_date
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $license = new CraftLicense();
        $next_file_number = $this->getNextFileNumber();
        return view('craft_licenses.create', compact('license', 'next_file_number'));
    }

    private function getNextFileNumber()
    {
        $max = CraftLicense::max('file_number');
        if (!$max) {
            return '100000';
        }
        return strval(intval($max) + 1);
    }

    public function store(StoreCraftLicenseRequest $request)
    {
        $data = $request->validated();
        $baseTotal = ($data['total_debts'] ?? 0) + ($data['craft_fees'] ?? 0) + ($data['signboard_fees'] ?? 0);
        $discountPercent = (float)($data['discount_percent'] ?? 0);
        $discountAmount = $discountPercent > 0 ? $baseTotal * ($discountPercent / 100) : 0;

        $data['final_price'] = $baseTotal - $discountAmount;
        $data['remaining'] = $data['final_price'] - ($data['payment_amount'] ?? 0);

        $license = CraftLicense::create($data);

        // Log activity
        activity()
            ->performedOn($license)
            ->causedBy(auth()->user())
            ->log('تم إنشاء رخصة حرفة: ' . $license->file_number);

        // حفظ الشركاء إن وجِدوا
        $partnerIdentities = $request->input('partner_identity', []);
        $partnerNames = $request->input('partner_name', []);
        if (! empty($partnerIdentities)) {
            $this->service->syncPartners($license, $partnerIdentities, $partnerNames);
        }

        // معالجة مرفقات رخصة الحرفة
        if ($request->hasFile('attachments')) {
            $this->service->handleAttachments($license, $request->file('attachments'));
        }

        if ($request->payment_amount > 0) {
            $this->centralTreasuryPaymentService->createCraftPayment([
                'craft_license_id' => $license->id,
                'amount'           => $request->payment_amount,
                'balance_after'    => $data['remaining'],
                'payment_method'   => 'cash',
                'payment_date'     => now()->toDateString(),
                'notes'            => 'دفعة عند التأسيس',
            ]);
        }

        $this->service->logHistory($license, 'إضافة');

        return redirect()->route('craft_licenses.edit', $license->id)->with('success', 'تم إضافة الملف بنجاح');
    }

    public function show(CraftLicense $craft_license)
    {
        $this->authorize('view', $craft_license);
        $license = $craft_license->load([
            'payments' => function ($q) {
                $q->with('user')->latest();
            },
            'attachments',
            'partners',
        ]);
        $histories = CraftFeeHistory::where('file_number', $license->file_number)->latest()->get();
        $this->service->calculateFinancials($license);

        return view('craft_licenses.show', compact('license', 'histories'));
    }

    public function edit(CraftLicense $craft_license)
    {
        $this->authorize('update', $craft_license);
        $license = $craft_license->load([
            'payments' => function($q) {
                $q->with('user')->latest();
            },
            'attachments',
            'partners',
        ]);
        // ضبط الرصيد والمبلغ المدفوع من مجموع الدفعات الفعلية ليتطابق العرض في كل الصفحة
        $this->service->calculateFinancials($license);
        $histories = CraftFeeHistory::where('file_number', $license->file_number)->latest()->get();
        return view('craft_licenses.edit', compact('license', 'histories'));
    }

    public function update(UpdateCraftLicenseRequest $request, CraftLicense $craft_license)
    {
        $this->authorize('update', $craft_license);
        $license = $craft_license;
        $data = $request->validated();

        // نحتفظ بالقيم الأصلية قبل التعديل لبناء سجل التغييرات
        $original = $license->replicate();

        // Don't update total_debts if it's already set and we want to prevent manual editing
        if ($license->total_debts > 0) {
            unset($data['total_debts']);
        }
        // الرصيد والمبلغ المدفوع يُحسبان دائماً من مجموع الدفعات في calculateFinancials
        unset($data['payment_amount'], $data['remaining']);

        $license->update($data);

        // Log activity
        activity()
            ->performedOn($license)
            ->causedBy(auth()->user())
            ->log('تم تعديل رخصة حرفة: ' . $license->file_number);

        // حفظ الشركاء إن وُجدوا
        $partnerIdentities = $request->input('partner_identity', []);
        $partnerNames = $request->input('partner_name', []);
        if (!empty($partnerIdentities)) {
            $this->service->syncPartners($license, $partnerIdentities, $partnerNames);
        } else {
            // إذا أزيلت جميع الحقول أو أزيلت علامة وجود شركاء، نفرّغ الشركاء
            $license->partners()->delete();
        }

        // معالجة المرفقات الجديدة إن وُجدت
        if ($request->hasFile('attachments')) {
            $this->service->handleAttachments($license, $request->file('attachments'));
        }

        $this->service->calculateFinancials($license);

        // بناء وصف تفصيلي لما تم تغييره (أهم الحقول)
        $fieldsMap = [
            'craft_fees' => 'رسوم الحرفة',
            'signboard_fees' => 'رسوم اللوحة',
            'total_debts' => 'إجمالي الديون',
            'discount_percent' => 'نسبة الخصم',
            'final_price' => 'الإجمالي بعد الخصم',
            'remaining' => 'الرصيد المتبقي',
            'status' => 'حالة الملف',
            'classification' => 'تصنيف الحرفة',
            'craft_type' => 'نوع الحرفة',
        ];

        $changes = [];
        foreach ($fieldsMap as $field => $label) {
            $old = $original->{$field};
            $new = $license->{$field};
            if ((string) $old !== (string) $new) {
                $changes[] = sprintf('%s: %s → %s', $label, $old === null || $old === '' ? '-' : $old, $new === null || $new === '' ? '-' : $new);
            }
        }

        $details = $changes ? implode(' | ', $changes) : null;

        $this->service->logHistory($license, 'تعديل', $details);

        return redirect()->back()->with('success', 'تم تحديث الملف بنجاح');
    }

    public function addPayment(Request $request, CraftLicense $craft_license)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank,check',
            'reference_number' => 'nullable|string',
            'payment_date' => 'required|date|after_or_equal:today|before_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'payment_date.after_or_equal' => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
            'payment_date.before_or_equal' => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
        ]);

        $this->service->addPayment($craft_license, $validated);

        return redirect()->back()->with('success', 'تم إضافة الدفعة بنجاح');
    }

    public function cancelPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:5'
        ]);

        $this->service->cancelPayment($payment, $request->cancellation_reason);

        return redirect()->back()->with('success', 'تم إلغاء الدفعة بنجاح');
    }

    public function addPartner(Request $request, CraftLicense $craft_license)
    {
        $this->authorize('update', $craft_license);

        $validated = $request->validate([
            'identity_number' => 'required|string|min:3|max:20',
            'name' => 'required|string|max:255',
        ]);

        $craft_license->partners()->create([
            'identity_number' => $validated['identity_number'],
            'name' => $validated['name'] ?? null,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الشريك بنجاح');
    }

    public function deletePartner(Request $request, CraftLicense $craft_license, \App\Models\CraftLicensePartner $partner)
    {
        $this->authorize('update', $craft_license);

        if ($partner->craft_license_id !== $craft_license->id) {
            abort(403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|min:5',
        ]);

        $partner->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->back()->with('success', 'تم إلغاء الشريك بنجاح');
    }

    public function print(CraftLicense $craft_license)
    {
        $license = $craft_license;
        return view('craft_licenses.print', compact('license'));
    }

    public function downloadAttachment(CraftLicense $craft_license, CraftLicenseAttachment $attachment)
    {
        if ($attachment->craft_license_id !== $craft_license->id) {
            abort(404);
        }

        if (! $attachment->file_path) {
            abort(404);
        }

        return response()->download(storage_path('app/public/' . $attachment->file_path), $attachment->file_name);
    }

    public function destroy(CraftLicense $craft_license)
    {
        $this->authorize('delete', $craft_license);
        // التحقق من عدم وجود مدفوعات مرتبطة بالترخيص
        if ($craft_license->payments()->count() > 0) {
            return redirect()->back()->with('warning', 'لا يمكن حذف الترخيص لأنه يحتوي على مدفوعات مرتبطة');
        }

        // Log activity before delete
        activity()
            ->performedOn($craft_license)
            ->causedBy(auth()->user())
            ->log('تم حذف رخصة حرفة: ' . $craft_license->file_number);

        $craft_license->delete();
        return redirect()->route('craft_licenses.index')->with('success', 'تم حذف الحرفة بنجاح');
    }

    public function deleteAttachment(CraftLicense $craft_license, CraftLicenseAttachment $attachment)
    {
        $this->authorize('update', $craft_license);

        if ($attachment->craft_license_id !== $craft_license->id) {
            abort(404);
        }

        if ($attachment->file_path && \Storage::disk('public')->exists($attachment->file_path)) {
            \Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json(['status' => 'ok']);
    }

    public function searchByFileNumber(Request $request)
    {
        $license = CraftLicense::where('file_number', $request->file_number)->first();
        if ($license) {
            return response()->json($license);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function renew(CraftLicense $craft_license)
    {
        $license = $craft_license;

        // إضافة رسوم الحرفة الجديدة
        $license->update([
            'craft_fees' => $license->craft_fees,
            'final_price' => ($license->total_debts ?? 0) + $license->craft_fees + ($license->signboard_fees ?? 0),
            'remaining' => ($license->total_debts ?? 0) + $license->craft_fees + ($license->signboard_fees ?? 0) - $license->payment_amount,
        ]);

        // تسجيل العملية في السجل
        $this->service->logHistory($license, 'تجديد سنوي');

        // التوجيه إلى قسم المالية مباشرة
        return redirect()->route('craft_licenses.edit', $license->id)
            ->with('success', 'تم تجديد الرخصة بنجاح. يرجى إكمال الدفع في قسم المالية')
            ->with('scroll_to_payments', true);
    }
}