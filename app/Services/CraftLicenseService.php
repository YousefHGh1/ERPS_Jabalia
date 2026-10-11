<?php

namespace App\Services;

use App\Models\CraftLicense;
use App\Models\CraftFeeHistory;
use App\Models\CraftLicenseAttachment;
use App\Models\CraftLicensePartner;
use App\Models\Payment;
use App\Services\CentralTreasuryPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class CraftLicenseService
{
    public function __construct(
        protected CentralTreasuryPaymentService $centralTreasuryPaymentService
    ) {
    }
    /**
     * احتساب المبالغ المالية للترخيص:
     * - إجمالي المستحق = الديون المرحلة (total_debts) + رسوم الحرفة + رسوم اللوحة - مبلغ الخصم
     * - الخصم يُطبَّق على رسوم السنة الحالية (الحرفة + اللوحة) فقط.
     * - الرصيد المتبقي = إجمالي المستحق - المبلغ المدفوع
     */
    public function calculateFinancials(CraftLicense $license)
    {
        $totalPaid = (float) $license->payments()->where('status', 'active')->sum('amount');
        $license->payment_amount = round($totalPaid, 2);

        $totalDebts = (float) ($license->total_debts ?? 0);
        $craftFees = (float) ($license->craft_fees ?? 0);
        $signboardFees = (float) ($license->signboard_fees ?? 0);
        $currentFeesBase = $craftFees + $signboardFees;
        $discountPercent = (float) ($license->discount_percent ?? 0);
        $discountAmount = $discountPercent > 0 ? $currentFeesBase * ($discountPercent / 100) : 0;
        $currentAfterDiscount = $currentFeesBase - $discountAmount;

        // إجمالي المستحق = ديون مرحلة + رسوم السنة الحالية بعد الخصم
        $finalPrice = $totalDebts + $currentAfterDiscount;
        $license->final_price = round($finalPrice, 2);
        $license->remaining = round($license->final_price - $totalPaid, 2);
        $license->save();

        return $license;
    }

    public function addPayment(CraftLicense $license, array $data)
    {
        return DB::transaction(function () use ($license, $data) {
            // First calculate current financials to get accurate balance
            $this->calculateFinancials($license);
            
            $balanceAfter = $license->remaining - $data['amount'];

            $payment = $this->centralTreasuryPaymentService->createCraftPayment([
                'craft_license_id' => $license->id,
                'amount'           => $data['amount'],
                'balance_after'    => $balanceAfter,
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date'     => $data['payment_date'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $this->calculateFinancials($license);

            // تحديث حالة الترخيص بعد الدفع:
            // إذا كانت الحالة "غير مجدد" (renewal_pending) أو حالة غير محددة ولدينا رصيد صفري → تعتبر "تجديد سنوي".
            if ($license->remaining <= 0 && in_array($license->status, ['renewal_pending', 'undefined'])) {
                $license->status = 'renewal';
                $license->save();
            }

            return $payment;
        });
    }

    public function cancelPayment(Payment $payment, string $reason)
    {
        return DB::transaction(function () use ($payment, $reason) {
            $payment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason
            ]);

            $this->calculateFinancials($payment->craftLicense);

            return $payment;
        });
    }

    public function logHistory(CraftLicense $license, string $operation, ?string $details = null)
    {
        return CraftFeeHistory::create([
            'file_number' => $license->file_number,
            'craft_type' => $license->craft_type,
            'fee_amount' => $license->craft_fees,
            'operation_type' => $details ? ($operation . ' - ' . $details) : $operation,
            'action_date' => now(),
        ]);
    }

    /**
     * حفظ الشركاء المرتبطين برخصة حرفة.
     */
    public function syncPartners(CraftLicense $license, array $identities = [], array $names = []): void
    {
        // تفريغ الشركاء وإعادة إنشائهم حسب المدخلات
        $license->partners()->delete();

        foreach ($identities as $index => $identity) {
            $identity = trim((string) $identity);
            if ($identity === '') {
                continue;
            }

            $name = isset($names[$index]) ? trim((string) $names[$index]) : null;

            $license->partners()->create([
                'identity_number' => $identity,
                'name' => $name,
            ]);
        }
    }

    /**
     * معالجة مرفقات رخصة الحرفة.
     * يتم تخزين الملفات في مجلد public/craft_licenses باسم: رقم_الملف + '_' + اسم_الملف_المرفوع.
     */
    public function handleAttachments(CraftLicense $license, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            // رقم الملف + اسم آمن
            $safeBase = Str::slug($baseName, '_');
            $fileNumber = $license->file_number ?? 'license';
            $storedName = $fileNumber . '_' . $safeBase . ($extension ? '.' . $extension : '');

            $path = $file->storeAs('craft_licenses', $storedName, 'public');

            CraftLicenseAttachment::create([
                'craft_license_id' => $license->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'created_by' => auth()->id(),
            ]);
        }
    }

    /**
     * ترصيد الرسوم السنوية تلقائياً لكل رخص الحرف النشطة للسنة الحالية.
     * - لا يرصّد للحالات المغلقة أو المتوقفة.
     * - يستخدم craft_fees كرسوم سنوية.
     * - يعتمد على first_license_year / registration_date لتحديد سنة البداية.
     * - last_license تُعامل كسنة آخر ترخيص/تجديد تم ترصيـدها.
     */
    public function accrueAnnualFeesForCurrentYear(): void
    {
        $currentYear = (int) now()->year;

        CraftLicense::query()
            ->whereNotIn('status', ['closed', 'stopped'])
            ->chunkById(200, function ($licenses) use ($currentYear) {
                /** @var CraftLicense $license */
                foreach ($licenses as $license) {
                    $this->accrueAnnualFee($license, $currentYear);
                }
            });
    }

    protected function accrueAnnualFee(CraftLicense $license, int $year): void
    {
        // لا نرصّد إذا كان لا توجد رسوم حرفة معرفة
        if (($license->craft_fees ?? 0) <= 0) {
            return;
        }

        // سنة البداية: أول سنة ترخيص أو سنة تسجيل الملف
        $startYear = (int) ($license->first_license_year
            ?: optional($license->registration_date)->year
            ?: $year);

        if ($year < $startYear) {
            return;
        }

        // سنة آخر ترخيص مُسجَّلة (last_license تُستخدم كسنة آخر ترخيص)
        $lastYear = (int) ($license->last_license ?: ($startYear - 1));

        // إذا كان آخر ترخيص >= السنة الحالية فلا حاجة للترصيد
        if ($lastYear >= $year) {
            return;
        }

        // ترصيد عن كل سنة لم تُرصَّد بعد حتى السنة الحالية
        $yearsToAccrue = $year - $lastYear;

        for ($i = 1; $i <= $yearsToAccrue; $i++) {
            // إذا تغيرت الحالة إلى مغلقة/متوقفة أثناء التكرار نوقف
            if (in_array($license->status, ['closed', 'stopped'])) {
                break;
            }

            $license->total_debts = ($license->total_debts ?? 0) + ($license->craft_fees ?? 0);
            $lastYear++;
            $license->last_license = (string) $lastYear;

            // تسجيل في سجل الرسوم لكل سنة
            CraftFeeHistory::create([
                'file_number' => $license->file_number,
                'craft_type' => $license->craft_type,
                'fee_amount' => $license->craft_fees,
                'operation_type' => 'ترصيد سنوي تلقائي للسنة ' . $lastYear,
                'action_date' => Carbon::create($lastYear, 12, 31),
            ]);
        }

        // تحديث عدد السنوات المرصدة من أول سنة ترخيص حتى آخر سنة ترخيص
        $effectiveStartYear = (int) $startYear;
        $effectiveLastYear = (int) ($license->last_license ?: 0);
        if ($effectiveLastYear >= $effectiveStartYear) {
            $license->remaining_years = $effectiveLastYear - $effectiveStartYear + 1;
        } else {
            $license->remaining_years = 0;
        }

        // إعادة حساب الأرقام النهائية
        $this->calculateFinancials($license);

        // تحديث الحالة بعد الترصيد:
        // إذا بقي مبلغ مستحق → "غير مجدد"
        // إذا لا يوجد متبقي → "تجديد سنوي"
        if ($license->remaining > 0) {
            $license->status = 'renewal_pending';
        } else {
            $license->status = 'renewal';
        }

        $license->save();
    }
}
