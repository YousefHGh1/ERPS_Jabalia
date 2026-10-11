<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentEngineering;
use App\Models\Request as CitizenRequest;
use App\Models\RequestPayment;
use App\Models\Engineering;
use App\Models\RevenueBatch;
use App\Repositories\RevenueBatchRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CentralTreasuryPaymentService
{
    public function __construct(
        protected CentralTreasuryBatchService $batchService,
        protected RevenueBatchRepository $batches
    ) {
    }

    protected function resolvePaymentDateForBatch(array $data, RevenueBatch $batch): string
    {
        $paymentDate = Carbon::parse($data['payment_date'] ?? now())->toDateString();
        $today = now()->toDateString();
        $batchDate = $batch->date?->toDateString() ?? $today;

        if ($paymentDate !== $today || $paymentDate !== $batchDate) {
            throw new RuntimeException('يُسمح فقط بتسجيل الدفعات بتاريخ اليوم وعلى الرزمة المفتوحة لنفس التاريخ.');
        }

        return $paymentDate;
    }

    protected function nextPaymentNumber(RevenueBatch $batch): int
    {
        $maxCraft = Payment::where('revenue_batch_id', $batch->id)->max('payment_number');
        $maxEngineering = PaymentEngineering::where('revenue_batch_id', $batch->id)->max('payment_number');
        $maxRequest = RequestPayment::where('revenue_batch_id', $batch->id)->max('payment_number');
        $currentMax = max((int) ($maxCraft ?? 0), (int) ($maxEngineering ?? 0), (int) ($maxRequest ?? 0));

        if ($currentMax > 0) {
            return $currentMax + 1;
        }

        return (int) ($batch->sequence_number ?? 0) + 1;
    }

    public function createCraftPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $batch = $this->batchService->getOrOpenSectionBatchForSource('crafts');

            if (! $batch->isOpen()) {
                throw new RuntimeException('لا يمكن تسجيل دفعة على رزمة مغلقة.');
            }
            $paymentDate = $this->resolvePaymentDateForBatch($data, $batch);
            $nextPaymentNumber = $this->nextPaymentNumber($batch);

            /** @var Payment $payment */
            $payment = Payment::create([
                'craft_license_id'   => $data['craft_license_id'],
                'amount'             => $data['amount'],
                'balance_after'      => $data['balance_after'] ?? null,
                'payment_method'     => $data['payment_method'] ?? 'cash',
                'reference_number'   => $data['reference_number'] ?? null,
                'user_id'            => $data['user_id'] ?? Auth::id(),
                'revenue_batch_id'   => $batch->id,
                'payment_date'       => $paymentDate,
                'notes'              => $data['notes'] ?? null,
                'status'             => 'active',
                'cancellation_reason'=> null,
                'payment_number'     => $nextPaymentNumber,
            ]);

            $this->batches->updateTotals($batch, (float) $payment->amount);

            if (function_exists('activity')) {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($payment)
                    ->withProperties([
                        'batch_id' => $batch->id,
                        'amount'   => $payment->amount,
                    ])
                    ->log('created_craft_payment_in_central_treasury');
            }

            return $payment;
        });
    }

    public function createEngineeringPayment(Engineering $engineering, array $data): PaymentEngineering
    {
        return DB::transaction(function () use ($engineering, $data) {
            $batch = $this->batchService->getOrOpenSectionBatchForSource('engineering');

            if (! $batch->isOpen()) {
                throw new RuntimeException('لا يمكن تسجيل دفعة هندسة على رزمة مغلقة.');
            }
            $paymentDate = $this->resolvePaymentDateForBatch($data, $batch);
            $nextPaymentNumber = $this->nextPaymentNumber($batch);

            /** @var PaymentEngineering $payment */
            $payment = $engineering->payments()->create([
                'amount'          => $data['amount'],
                'balance_after'   => $data['balance_after'] ?? 0,
                'payment_method'  => $data['payment_method'] ?? 'cash',
                'receipt_number'  => $data['receipt_number'] ?? null,
                'created_by'      => auth()->id(),
                'payment_date'    => $paymentDate,
                'notes'           => $data['notes'] ?? null,
                'revenue_batch_id'=> $batch->id,
                'status'          => 'active',
                'payment_number'  => $nextPaymentNumber,
            ]);

            $this->batches->updateTotals($batch, (float) $payment->amount);

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($payment)
                    ->withProperties([
                        'batch_id' => $batch->id,
                        'amount'   => $payment->amount,
                    ])
                    ->log('created_engineering_payment_in_central_treasury');
            }

            return $payment;
        });
    }

    public function createRequestPayment(CitizenRequest $citizenRequest, array $data): RequestPayment
    {
        return DB::transaction(function () use ($citizenRequest, $data) {
            $batch = $this->batchService->getOrOpenSectionBatchForSource('public_pen_requests');

            if (! $batch->isOpen()) {
                throw new RuntimeException('لا يمكن تسجيل دفعة طلب (قلم الجمهور) على رزمة مغلقة.');
            }
            $paymentDate = $this->resolvePaymentDateForBatch($data, $batch);
            $nextPaymentNumber = $this->nextPaymentNumber($batch);

            /** @var RequestPayment $payment */
            $payment = $citizenRequest->payments()->create([
                'amount'           => $data['amount'],
                'balance_after'    => $data['balance_after'] ?? 0,
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'receipt_number'   => $data['receipt_number'] ?? null,
                'created_by'       => auth()->id(),
                'payment_date'     => $paymentDate,
                'notes'            => $data['notes'] ?? null,
                'revenue_batch_id' => $batch->id,
                'status'           => 'active',
                'payment_number'   => $nextPaymentNumber,
            ]);

            $this->batches->updateTotals($batch, (float) $payment->amount);

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($payment)
                    ->withProperties([
                        'batch_id' => $batch->id,
                        'amount'   => $payment->amount,
                    ])
                    ->log('created_request_payment_in_central_treasury');
            }

            return $payment;
        });
    }
}
