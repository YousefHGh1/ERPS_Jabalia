<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RevenueBatch;
use App\Models\Payment;
use App\Models\PaymentEngineering;
use App\Models\RequestPayment;
use App\Models\ExternalCollectionPayment;
use App\Services\CentralTreasuryBatchService;
use Illuminate\Http\Request;
use RuntimeException;

class CentralTreasuryController extends Controller
{
    public function __construct(
        protected CentralTreasuryBatchService $batchService,
    ) {
        $this->middleware('auth');
        $this->middleware('permission:view central_treasury')->only([
            'todaySummary',
            'history',
            'showBatch',
        ]);
        $this->middleware('permission:manage central_treasury')->only([
            'closeSectionBatch',
            'closeUnifiedToday',
            'reopenBatch',
        ]);
    }

    public function todaySummary()
    {
        $unifiedBatch = RevenueBatch::query()
            ->unified()
            ->forDate(now()->toDateString())
            ->orderByDesc('id')
            ->first();

        $sectionBatches = $unifiedBatch
            ? $unifiedBatch->sectionBatches()->with('section')->orderBy('id')->get()
            : collect();

        $batchIds = $unifiedBatch
            ? collect($unifiedBatch->collectPaymentBatchIds())
            : collect();

        $craftPayments = collect();
        $engineeringPayments = collect();
        $requestPayments = collect();
        $externalCollectionPayments = collect();
        $cancelledCraftPayments = collect();
        $cancelledEngineeringPayments = collect();
        $cancelledRequestPayments = collect();
        $cancelledExternalCollectionPayments = collect();
        $todayActiveTotal = 0;
        $craftTotal = 0;
        $engineeringTotal = 0;
        $requestsTotal = 0;
        $externalCollectionTotal = 0;

        if ($batchIds->isNotEmpty()) {
            $craftPayments = Payment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'active')
                ->with(['craftLicense', 'user'])
                ->latest('payment_date')
                ->get();

            $engineeringPayments = PaymentEngineering::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'active')
                ->with(['engineering', 'user'])
                ->latest('payment_date')
                ->get();

            $requestPayments = RequestPayment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'active')
                ->with(['citizenRequest', 'user'])
                ->latest('payment_date')
                ->get();

            $externalCollectionPayments = ExternalCollectionPayment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'paid')
                ->with(['externalCollection', 'user'])
                ->latest('payment_date')
                ->get();

            $todayActiveTotal = (float) $craftPayments->sum('amount')
                + (float) $engineeringPayments->sum('amount')
                + (float) $requestPayments->sum('amount')
                + (float) $externalCollectionPayments->sum('amount');
            $craftTotal = (float) $craftPayments->sum('amount');
            $engineeringTotal = (float) $engineeringPayments->sum('amount');
            $requestsTotal = (float) $requestPayments->sum('amount');
            $externalCollectionTotal = (float) $externalCollectionPayments->sum('amount');

            $cancelledCraftPayments = Payment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'cancelled')
                ->with(['craftLicense', 'user'])
                ->latest('payment_date')
                ->get();

            $cancelledEngineeringPayments = PaymentEngineering::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'cancelled')
                ->with(['engineering', 'user'])
                ->latest('payment_date')
                ->get();

            $cancelledRequestPayments = RequestPayment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'cancelled')
                ->with(['citizenRequest', 'user'])
                ->latest('payment_date')
                ->get();

            $cancelledExternalCollectionPayments = ExternalCollectionPayment::whereIn('revenue_batch_id', $batchIds)
                ->where('status', 'cancelled')
                ->with(['externalCollection', 'user'])
                ->latest('payment_date')
                ->get();
        }

        $sectionBatchBySource = [
            'public_pen_requests' => $sectionBatches->firstWhere('section_id', (int) config('central_treasury.revenue_sources.public_pen_requests.section_id')),
            'engineering' => $sectionBatches->firstWhere('section_id', (int) config('central_treasury.revenue_sources.engineering.section_id')),
            'crafts' => $sectionBatches->firstWhere('section_id', (int) config('central_treasury.revenue_sources.crafts.section_id')),
        ];

        return view('central_treasury.today', [
            'unifiedBatch'                   => $unifiedBatch,
            'sectionBatches'                 => $sectionBatches,
            'craftPayments'                  => $craftPayments,
            'engineeringPayments'            => $engineeringPayments,
            'requestPayments'                => $requestPayments,
            'externalCollectionPayments'     => $externalCollectionPayments,
            'cancelledCraftPayments'         => $cancelledCraftPayments,
            'cancelledEngineeringPayments'   => $cancelledEngineeringPayments,
            'cancelledRequestPayments'       => $cancelledRequestPayments,
            'cancelledExternalCollectionPayments' => $cancelledExternalCollectionPayments,
            'todayActiveTotal'               => $todayActiveTotal,
            'craftTotal'                     => $craftTotal,
            'engineeringTotal'               => $engineeringTotal,
            'requestsTotal'                  => $requestsTotal,
            'externalCollectionTotal'        => $externalCollectionTotal,
            'sectionBatchBySource'           => $sectionBatchBySource,
        ]);
    }

    public function closeSectionBatch(RevenueBatch $batch)
    {
        try {
            $this->batchService->closeSectionBatch($batch);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم إغلاق رزمة القسم بنجاح.');
    }

    public function closeUnifiedToday()
    {
        try {
            $this->batchService->closeUnifiedBatchForToday();
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم إغلاق الرزمة الموحدة لهذا اليوم.');
    }

    public function reopenBatch(RevenueBatch $batch)
    {
        try {
            if (! $batch->date || ! $batch->date->isToday()) {
                throw new RuntimeException('لا يمكن إعادة فتح هذه الرزمة إلا لتاريخ اليوم.');
            }

            $this->batchService->reopenBatch($batch);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('central_treasury.today')
            ->with('success', 'تم إعادة فتح رزمة القسم.');
    }

    public function history()
    {
        $batches = RevenueBatch::query()
            ->unified()
            ->where('status', 'closed')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('central_treasury.history', compact('batches'));
    }

    public function showBatch(RevenueBatch $batch)
    {
        $batchIds = collect($batch->collectPaymentBatchIds());

        $craftPayments = Payment::whereIn('revenue_batch_id', $batchIds)
            ->with(['craftLicense', 'user'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $engineeringPayments = PaymentEngineering::whereIn('revenue_batch_id', $batchIds)
            ->with(['engineering', 'user'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $requestPayments = RequestPayment::whereIn('revenue_batch_id', $batchIds)
            ->with(['citizenRequest', 'user'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $externalCollectionPayments = ExternalCollectionPayment::whereIn('revenue_batch_id', $batchIds)
            ->with(['externalCollection', 'user'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $childSectionBatches = $batch->isUnified()
            ? $batch->sectionBatches()->with('section')->orderBy('id')->get()
            : collect();

        return view('central_treasury.show_batch', [
            'batch'                      => $batch,
            'childSectionBatches'        => $childSectionBatches,
            'craftPayments'              => $craftPayments,
            'engineeringPayments'        => $engineeringPayments,
            'requestPayments'            => $requestPayments,
            'externalCollectionPayments' => $externalCollectionPayments,
        ]);
    }
}
