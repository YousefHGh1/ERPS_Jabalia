<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering;
use App\Models\EngineeringsFeeHistory;
use App\Models\PaymentEngineering;
use App\Services\EngineeringService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EngineeringController extends Controller
{
    protected $service;

    public function __construct(EngineeringService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $fileNumber = $request->input('file_number');

        $files = Engineering::with(['attachments']);

        if ($search) {
            $files->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('grandfather_name', 'like', "%{$search}%")
                    ->orWhere('family_name', 'like', "%{$search}%");
            });
        }

        if ($fileNumber) {
            $files->where('file_number', 'like', "%{$fileNumber}%");
        }

        $engineerings = $files->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());

        return view('engineering.index', compact('engineerings'));
    }

    public function show(Engineering $engineering)
    {
        $engineering->load('attachments');
        return view('engineering.show', compact('engineering'));
    }

    public function create()
    {
        $engineering = new Engineering();
        return view('engineering.wizard', compact('engineering'));
    }

    public function store(Request $request)
    {
        $rules = [
            'file_number'         => 'nullable|string|max:50',
            'file_year'           => 'required|digits:4',
            'first_name'          => 'required|string|max:255',
            'father_name'         => 'required|string|max:255',
            'grandfather_name'    => 'nullable|string|max:255',
            'family_name'         => 'required|string|max:255',
            'national_id'         => 'required|string|max:20',
            'plot_number'         => 'required|string|max:50',
            'parcel_number'       => 'required|string|max:50',
            'division_number'     => 'required|string|max:50',
            'area_size'           => 'required|numeric',
            'neighborhood'        => 'required|string|max:255',
            'address'             => 'required|string',
            'building_type'       => 'required|string|max:255',
            'floors_count'        => 'required|array',
            'subdivision_type'    => 'required|string|in:مفرز,غير مفرز',
            'subdivision_project' => 'required_if:subdivision_type,مفرز|nullable|string|max:255',
            'gis_fees'            => 'required|numeric|min:0',
            'penalties'           => 'nullable|numeric|min:0',
            'license_fees'        => 'required|numeric|min:0',
            'total_amount'        => 'required|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0',
            'remaining_amount'    => 'required|numeric|min:0',
            'application_date'    => 'required|date',
            'session_date'        => 'required|date',
            'file_registered_at'  => 'required|date',
            'last_payment_date'   => 'required|date',
            'phone_number'        => 'required|string|max:20',
            'mobile_number'       => 'required|string|max:20',
            'notes'               => 'nullable|string',
            'agreement'           => 'required|accepted',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric'  => 'حقل :attribute يجب أن يكون رقم.',
            'string'   => 'حقل :attribute يجب أن يكون نص.',
            'digits'   => 'حقل :attribute يجب أن يحتوي على :digits رقم.',
            'integer'  => 'حقل :attribute يجب أن يكون عدد صحيح.',
            'min'      => 'حقل :attribute يجب أن يكون على الأقل :min.',
            'max'      => 'حقل :attribute يجب ألا يتجاوز :max حرف/رقم.',
            'date'     => 'حقل :attribute يجب أن يكون تاريخ صالح.',
            'nullable' => 'حقل :attribute يمكن أن يكون فارغاً.',
            'accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ];

        $rules['penalties'] = 'nullable|numeric|min:0';

        $validated = $request->validate($rules, $messages);

        // رقم الملف للقراءة فقط وتزايدي: يُولَّد دائماً عند الإضافة (ترتيب عددي صحيح)
        $maxNumeric = Engineering::select('file_number')->get()
            ->map(fn ($r) => (int) preg_replace('/\D/', '', (string) ($r->file_number ?? '')))
            ->filter(fn ($n) => $n > 0)
            ->max();
        $validated['file_number'] = (string) (($maxNumeric ?? 0) + 1);

        if (isset($validated['agreement'])) {
            $validated['agreement'] = $validated['agreement'] === 'on' || $validated['agreement'] === true || $validated['agreement'] === '1' || $validated['agreement'] === 1;
        }

        if (! isset($validated['total_amount']) || empty($validated['total_amount'])) {
            $validated['total_amount'] = ($validated['gis_fees'] ?? 0) + ($validated['penalties'] ?? 0) + ($validated['license_fees'] ?? 0);
        }

        if (! isset($validated['remaining_amount']) || empty($validated['remaining_amount'])) {
            $validated['remaining_amount'] = $validated['total_amount'] - ($validated['paid_amount'] ?? 0);
        }

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $engineering = Engineering::create($validated);
        
        // Log activity
        activity()
            ->performedOn($engineering)
            ->causedBy(auth()->user())
            ->log('تم إنشاء ملف هندسة: ' . $engineering->file_number);

        // معالجة مرفقات ملف الهندسة
        if ($request->hasFile('attachments')) {
            $this->service->handleAttachments($engineering, $request->file('attachments'));
        }

        $warningMessage = null;
        if ($request->paid_amount > 0) {
            try {
                $this->service->addPayment($engineering, [
                    'amount'         => $request->paid_amount,
                    'payment_method' => 'cash',
                    'payment_date'   => now()->toDateString(),
                    'receipt_number' => null,
                    'notes'          => 'دفعة عند التأسيس',
                ]);
            } catch (\RuntimeException $e) {
                // نحافظ على إنشاء الملف حتى لو تعذر تسجيل دفعة التأسيس بسبب حالة الرزمة.
                $warningMessage = $e->getMessage();
                $engineering->update([
                    'paid_amount' => 0,
                    'remaining_amount' => (float) ($engineering->total_amount ?? 0),
                ]);
                $this->service->calculateFinancials($engineering);
            }
        }

        $this->service->logHistory($engineering, 'إضافة');

        $redirect = redirect()
            ->route('engineering.edit', $engineering->id)
            ->with('success', 'تم إنشاء الملف بنجاح!');

        if ($warningMessage) {
            return $redirect->with('warning', $warningMessage);
        }

        return $redirect;
    }

    public function edit($id)
    {
        $engineering = Engineering::findOrFail($id);
        $engineering->load(['payments' => function($q) {
            $q->with('user')->latest();
        }]);
        $histories = EngineeringsFeeHistory::where('engineering_id', $engineering->id)->latest()->get();
        return view('engineering.wizard', compact('engineering', 'histories'));
    }

    public function update(Request $request, $id)
    {
        $engineering = Engineering::findOrFail($id);

        $rules = [
            'file_number'         => [
                'required', 'string', 'max:50',
                Rule::unique('engineerings', 'file_number')->whereNull('deleted_at')->ignore($engineering->id),
            ],
            'file_year'           => 'required|digits:4',
            'first_name'          => 'required|string|max:255',
            'father_name'         => 'required|string|max:255',
            'grandfather_name'    => 'nullable|string|max:255',
            'family_name'         => 'required|string|max:255',
            'national_id'         => 'required|string|max:20',
            'plot_number'         => 'required|string|max:50',
            'parcel_number'       => 'required|string|max:50',
            'division_number'     => 'required|string|max:50',
            'area_size'           => 'required|numeric',
            'neighborhood'        => 'required|string|max:255',
            'address'             => 'required|string',
            'building_type'       => 'required|string|max:255',
            'floors_count'        => 'required|array',
            'subdivision_type'    => 'required|string|in:مفرز,غير مفرز',
            'subdivision_project' => 'required_if:subdivision_type,مفرز|nullable|string|max:255',
            'gis_fees'            => 'required|numeric|min:0',
            'penalties'           => 'nullable|numeric|min:0',
            'license_fees'        => 'required|numeric|min:0',
            'total_amount'        => 'required|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0',
            'remaining_amount'    => 'required|numeric|min:0',
            'application_date'    => 'required|date',
            'session_date'        => 'required|date',
            'file_registered_at'  => 'required|date',
            'last_payment_date'   => 'required|date',
            'phone_number'        => 'required|string|max:20',
            'mobile_number'       => 'required|string|max:20',
            'notes'               => 'nullable|string',
            'agreement'           => 'required|accepted',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric'  => 'حقل :attribute يجب أن يكون رقم.',
            'string'   => 'حقل :attribute يجب أن يكون نص.',
            'digits'   => 'حقل :attribute يجب أن يحتوي على :digits رقم.',
            'integer'  => 'حقل :attribute يجب أن يكون عدد صحيح.',
            'min'      => 'حقل :attribute يجب أن يكون على الأقل :min.',
            'max'      => 'حقل :attribute يجب ألا يتجاوز :max حرف/رقم.',
            'date'     => 'حقل :attribute يجب أن يكون تاريخ صالح.',
            'nullable' => 'حقل :attribute يمكن أن يكون فارغاً.',
            'accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ];

        $validated = $request->validate($rules, $messages);

        if (isset($validated['agreement'])) {
            $validated['agreement'] = $validated['agreement'] === 'on' || $validated['agreement'] === true || $validated['agreement'] === '1' || $validated['agreement'] === 1;
        }

        if (! isset($validated['total_amount']) || empty($validated['total_amount'])) {
            $validated['total_amount'] = ($validated['gis_fees'] ?? 0) + ($validated['penalties'] ?? 0) + ($validated['license_fees'] ?? 0);
        }

        if (! isset($validated['remaining_amount']) || empty($validated['remaining_amount'])) {
            $validated['remaining_amount'] = $validated['total_amount'] - ($validated['paid_amount'] ?? 0);
        }

        $engineering->update($validated);
        
        // Log activity
        activity()
            ->performedOn($engineering)
            ->causedBy(auth()->user())
            ->log('تم تعديل ملف هندسة: ' . $engineering->file_number);

        // معالجة المرفقات الجديدة إن وُجدت
        if ($request->hasFile('attachments')) {
            $this->service->handleAttachments($engineering, $request->file('attachments'));
        }

        $this->service->calculateFinancials($engineering);
        $this->service->logHistory($engineering, 'تعديل');

        return redirect()->route('engineering.edit', $engineering->id)->with('success', 'تم تحديث الملف بنجاح!');
    }

    public function destroy($id)
    {
        $engineering = Engineering::findOrFail($id);
        
        // Log activity before delete
        activity()
            ->performedOn($engineering)
            ->causedBy(auth()->user())
            ->log('تم حذف ملف هندسة: ' . $engineering->file_number);
        
        $engineering->delete();

        return redirect()->route('engineering.index')->with('success', 'تم حذف الملف بنجاح!');
    }

    public function print($id)
    {
        $engineering = Engineering::with('attachments')->findOrFail($id);

        $engineering->citizen_name = trim(
            $engineering->first_name . ' ' .
            $engineering->father_name . ' ' .
            ($engineering->grandfather_name ?? '') . ' ' .
            $engineering->family_name
        );

        $engineering->license_type = $engineering->building_type ?? 'بناء';
        $engineering->license_date = $engineering->file_registered_at ?? now();

        return view('engineering.print', compact('engineering'));
    }

    public function export(Request $request)
    {
        $search     = $request->input('search');
        $fileNumber = $request->input('file_number');

        $files = Engineering::query();

        if ($search) {
            $files->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('grandfather_name', 'like', "%{$search}%")
                    ->orWhere('family_name', 'like', "%{$search}%");
            });
        }

        if ($fileNumber) {
            $files->where('file_number', 'like', "%{$fileNumber}%");
        }

        $engineerings = $files->orderBy('created_at', 'desc')->get();

        $filename = 'engineering_files_' . date('Y-m-d') . '.csv';

        $buffer = fopen('php://temp', 'r+');
        // UTF-8 BOM for Excel
        fwrite($buffer, "\xEF\xBB\xBF");

        $headers = [
            'File Number',
            'File Year',
            'First Name',
            'Father Name',
            'Grandfather Name',
            'Family Name',
            'National ID',
            'Plot Number',
            'Parcel Number',
            'Division Number',
            'Area Size',
            'Neighborhood',
            'Address',
            'Building Type',
            'Floors Count',
            'Apartments Count',
            'Subdivision Project',
            'GIS Fees',
            'License Fees',
            'Total Amount',
            'Paid Amount',
            'Remaining Amount',
            'Application Date',
            'Session Date',
            'File Registered At',
            'Last Payment Date',
            'Phone Number',
            'Mobile Number',
            'Notes',
        ];
        fputcsv($buffer, $headers);

        foreach ($engineerings as $engineering) {
            $floorsCount = $engineering->floors_count;
            if (is_array($floorsCount)) {
                $floorsCount = implode(', ', $floorsCount);
            }
            $floorsCount = $floorsCount ?? '';

            fputcsv($buffer, [
                $engineering->file_number ?? '',
                $engineering->file_year ?? '',
                $engineering->first_name ?? '',
                $engineering->father_name ?? '',
                $engineering->grandfather_name ?? '',
                $engineering->family_name ?? '',
                $engineering->national_id ?? '',
                $engineering->plot_number ?? '',
                $engineering->parcel_number ?? '',
                $engineering->division_number ?? '',
                $engineering->area_size ?? '',
                $engineering->neighborhood ?? '',
                $engineering->address ?? '',
                $engineering->building_type ?? '',
                $floorsCount,
                $engineering->apartments_count ?? '',
                $engineering->subdivision_project ?? '',
                $engineering->gis_fees ?? '',
                $engineering->license_fees ?? '',
                $engineering->total_amount ?? '',
                $engineering->paid_amount ?? '',
                $engineering->remaining_amount ?? '',
                $engineering->application_date ?? '',
                $engineering->session_date ?? '',
                $engineering->file_registered_at ?? '',
                $engineering->last_payment_date ?? '',
                $engineering->phone_number ?? '',
                $engineering->mobile_number ?? '',
                $engineering->notes ?? '',
            ]);
        }

        rewind($buffer);
        $csv = stream_get_contents($buffer);
        fclose($buffer);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ]);
    }

    public function addPayment(Request $request, $id)
    {
        $engineering = Engineering::findOrFail($id);

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,check',
            'payment_date' => 'required|date|after_or_equal:today|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric' => 'حقل :attribute يجب أن يكون رقم.',
            'min' => 'حقل :attribute يجب أن يكون على الأقل :min.',
            'in' => 'حقل :attribute قيمة غير صالحة.',
            'date' => 'حقل :attribute يجب أن يكون تاريخ صالح.',
            'after_or_equal' => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
            'before_or_equal' => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
        ];

        $validated = $request->validate($rules, $messages);

        $newRemaining = $engineering->remaining_amount - $validated['amount'];

        if ($newRemaining < 0) {
            return response()->json([
                'success' => false,
                'message' => 'المبلغ المدفوع يتجاوز المبلغ المتبقي'
            ], 422);
        }

        try {
            $payment = $this->service->addPayment($engineering, [
                'amount'          => $validated['amount'],
                'payment_method'  => $validated['payment_method'],
                'payment_date'    => $validated['payment_date'],
                'receipt_number'  => $validated['reference_number'] ?? null,
                'notes'           => $validated['notes'] ?? null,
            ]);
            
            // Log activity
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->log('تم إضافة دفعة هندسة: ' . $engineering->file_number . ' - المبلغ: ' . $validated['amount']);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $engineering->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة الدفعة بنجاح',
            'payment' => $payment
        ]);
    }

    public function cancelPayment(Request $request, $paymentId)
    {
        $payment = PaymentEngineering::findOrFail($paymentId);

        if ($payment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'هذه الدفعة ملغاة بالفعل'
            ], 422);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:5',
        ]);

        $engineering = $payment->engineering;
        
        // Log activity before cancellation
        activity()
            ->performedOn($payment)
            ->causedBy(auth()->user())
            ->log('تم إلغاء دفعة هندسة: ' . $engineering->file_number . ' - السبب: ' . $validated['cancellation_reason']);

        $payment->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        $engineering->update([
            'paid_amount' => $engineering->paid_amount - $payment->amount,
            'remaining_amount' => $engineering->remaining_amount + $payment->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الدفعة بنجاح'
        ]);
    }
}
