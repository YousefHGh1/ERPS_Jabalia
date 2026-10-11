<?php

namespace App\Services;

use App\Models\Engineering;
use App\Models\EngineeringsFeeHistory;
use App\Models\PaymentEngineering;
use App\Services\CentralTreasuryPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\EngineeringAttachment;
use Illuminate\Http\UploadedFile;

class EngineeringService
{
    public function __construct(
        protected CentralTreasuryPaymentService $centralTreasuryPaymentService
    ) {
    }
    public function calculateFinancials(Engineering $engineering)
    {
        $totalPaid = (float) $engineering->payments()->where('status', 'active')->sum('amount');
        $engineering->paid_amount = $totalPaid;

        // إجمالي المستحق = رسوم GIS + رسوم الترخيص + الغرامات (إن وُجدت)
        $gisFees      = (float) ($engineering->gis_fees ?? 0);
        $licenseFees  = (float) ($engineering->license_fees ?? 0);
        $penalties    = (float) ($engineering->penalties ?? 0);

        $engineering->total_amount    = $gisFees + $licenseFees + $penalties;
        $engineering->remaining_amount = $engineering->total_amount - $totalPaid;
        $engineering->save();

        return $engineering;
    }

    public function addPayment(Engineering $engineering, array $data)
    {
        return DB::transaction(function () use ($engineering, $data) {
            // First calculate current financials to get accurate balance
            $this->calculateFinancials($engineering);

            $balanceAfter = $engineering->remaining_amount - $data['amount'];

            $payment = $this->centralTreasuryPaymentService->createEngineeringPayment($engineering, [
                'amount'          => $data['amount'],
                'balance_after'   => $balanceAfter,
                'payment_method'  => $data['payment_method'] ?? 'cash',
                'receipt_number'  => $data['receipt_number'] ?? null,
                'payment_date'    => $data['payment_date'],
                'notes'           => $data['notes'] ?? null,
            ]);

            $this->calculateFinancials($engineering);

            return $payment;
        });
    }

    public function cancelPayment(PaymentEngineering $payment, string $reason)
    {
        return DB::transaction(function () use ($payment, $reason) {
            $payment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason
            ]);

            $this->calculateFinancials($payment->engineering);

            return $payment;
        });
    }

    public function logHistory(Engineering $engineering, string $operation)
    {
        return EngineeringsFeeHistory::create([
            'engineering_id' => $engineering->id,
            'fee_amount' => $engineering->total_amount,
            'operation_type' => $operation,
            'action_date' => now(),
        ]);
    }

    /**
     * معالجة مرفقات ملف الهندسة والتنظيم.
     * يتم تخزين الملفات في مجلد public/engineering_files باسم: رقم_الملف + '_' + اسم_الملف_المرفوع.
     *
     * @param Engineering $engineering
     * @param UploadedFile[] $files
     */
    public function handleAttachments(Engineering $engineering, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            $safeBase = Str::slug($baseName, '_');
            $fileNumber = $engineering->file_number ?? 'engineering';
            $storedName = $fileNumber . '_' . $safeBase . ($extension ? '.' . $extension : '');

            $path = $file->storeAs('engineering_files', $storedName, 'public');

            EngineeringAttachment::create([
                'engineering_id' => $engineering->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'created_by' => auth()->id(),
            ]);
        }
    }
}
