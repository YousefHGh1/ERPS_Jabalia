<?php

namespace App\Http\Controllers;

use App\Models\CraftLicense;
use App\Models\Engineering;
use App\Models\Mokalaf;
use App\Models\Request as CitizenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CitizenInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $identityNumber = trim((string) $request->query('identity_number', ''));
        $invoiceData = null;
        $warning = null;

        if ($identityNumber !== '') {
            $invoiceData = $this->buildInvoiceData($identityNumber);
            if ($invoiceData === []) {
                $warning = 'رقم الهوية غير مسجل كمكلف.';
                $invoiceData = null;
            }
        }

        return view('citizens.unified_invoice', [
            'identityNumber' => $identityNumber,
            'invoiceData' => $invoiceData,
            'warning' => $warning,
        ]);
    }

    public function print(Request $request)
    {
        $identityNumber = trim((string) $request->query('identity_number', ''));
        if ($identityNumber === '') {
            return redirect()
                ->route('citizen.unified_invoice')
                ->with('error', 'رقم الهوية مطلوب لطباعة الفاتورة.');
        }

        $invoiceData = $this->buildInvoiceData($identityNumber);
        if ($invoiceData === []) {
            return redirect()
                ->route('citizen.unified_invoice', ['identity_number' => $identityNumber])
                ->with('warning', 'رقم الهوية غير مسجل كمكلف.');
        }

        return view('citizens.unified_invoice_print', [
            'identityNumber' => $identityNumber,
            'invoiceData' => $invoiceData,
        ]);
    }

    protected function buildInvoiceData(string $identityNumber): array
    {
        $identityVariants = $this->buildIdentityVariants($identityNumber);
        $mokalaf = Mokalaf::query()
            ->where(function ($q) use ($identityVariants) {
                $q->whereIn('identity_number', $identityVariants)
                    ->orWhereRaw("REPLACE(REPLACE(identity_number, '-', ''), ' ', '') IN (" . implode(',', array_fill(0, count($identityVariants), '?')) . ")", $identityVariants);
            })
            ->first();

        if (!$mokalaf) {
            return [];
        }

        // Use identity from mokalafs table as the single trusted source for invoice lookup.
        $identityVariants = $this->buildIdentityVariants((string) $mokalaf->identity_number);

        $craftLicenses = CraftLicense::query()
            ->where(function ($q) use ($identityVariants) {
                $q->whereIn('id_number', $identityVariants)
                    ->orWhereRaw("REPLACE(REPLACE(id_number, '-', ''), ' ', '') IN (" . implode(',', array_fill(0, count($identityVariants), '?')) . ")", $identityVariants);
            })
            ->with(['payments' => function ($q) {
                $q->where('status', 'active')->with('user')->orderByDesc('payment_date')->orderByDesc('id');
            }])
            ->get();

        $engineerings = Engineering::query()
            ->where(function ($q) use ($identityVariants) {
                $q->whereIn('national_id', $identityVariants)
                    ->orWhereRaw("REPLACE(REPLACE(national_id, '-', ''), ' ', '') IN (" . implode(',', array_fill(0, count($identityVariants), '?')) . ")", $identityVariants);
            })
            ->with(['payments' => function ($q) {
                $q->where('status', 'active')->with('user')->orderByDesc('payment_date')->orderByDesc('id');
            }])
            ->get();

        $requests = CitizenRequest::query()
            ->whereHas('mokalaf', function ($q) use ($identityVariants) {
                $q->whereIn('identity_number', $identityVariants)
                    ->orWhereRaw("REPLACE(REPLACE(identity_number, '-', ''), ' ', '') IN (" . implode(',', array_fill(0, count($identityVariants), '?')) . ")", $identityVariants);
            })
            ->with([
                'mokalaf',
                'payments' => function ($q) {
                    $q->where('status', 'active')->with('user')->orderByDesc('payment_date')->orderByDesc('id');
                },
            ])
            ->get();

        $paymentRows = collect()
            ->merge($this->craftPaymentRows($craftLicenses))
            ->merge($this->engineeringPaymentRows($engineerings))
            ->merge($this->requestPaymentRows($requests))
            ->sortByDesc('payment_date')
            ->values();

        $craftTotalDue = (float) $craftLicenses->sum(fn ($item) => (float) ($item->final_price ?? 0));
        $engineeringTotalDue = (float) $engineerings->sum(fn ($item) => (float) ($item->total_amount ?? 0));
        $requestsTotalDue = (float) $requests->sum(fn ($item) => (float) ($item->fee_amount ?? 0));

        $craftTotalPaid = (float) $craftLicenses->flatMap->payments->sum('amount');
        $engineeringTotalPaid = (float) $engineerings->flatMap->payments->sum('amount');
        $requestsTotalPaid = (float) $requests->flatMap->payments->sum('amount');

        $citizenName = (string) ($mokalaf->name ?: 'غير متوفر');

        return [
            'citizen_name' => $citizenName,
            'payments' => $paymentRows,
            'totals' => [
                'craft_due' => $craftTotalDue,
                'engineering_due' => $engineeringTotalDue,
                'requests_due' => $requestsTotalDue,
                'craft_paid' => $craftTotalPaid,
                'engineering_paid' => $engineeringTotalPaid,
                'requests_paid' => $requestsTotalPaid,
                'all_due' => $craftTotalDue + $engineeringTotalDue + $requestsTotalDue,
                'all_paid' => $craftTotalPaid + $engineeringTotalPaid + $requestsTotalPaid,
            ],
        ];
    }

    protected function buildIdentityVariants(string $identityNumber): array
    {
        $raw = trim($identityNumber);
        $digitsOnly = preg_replace('/\D+/', '', $raw) ?? '';
        $withoutLeadingZeros = ltrim($digitsOnly, '0');

        $variants = collect([$raw, $digitsOnly, $withoutLeadingZeros])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (string) $v)
            ->unique()
            ->values()
            ->all();

        return $variants === [] ? [$raw] : $variants;
    }

    protected function craftPaymentRows(Collection $licenses): Collection
    {
        return $licenses->flatMap(function ($license) {
            return $license->payments->map(function ($payment) use ($license) {
                return [
                    'section' => 'الحرف والصناعات',
                    'reference' => $license->file_number ?: ('رخصة #' . $license->id),
                    'payment_number' => $payment->payment_number ?: $payment->id,
                    'payment_method' => $payment->payment_method,
                    'amount' => (float) $payment->amount,
                    'payment_date' => optional($payment->payment_date)->format('Y-m-d') ?: (string) $payment->payment_date,
                    'collector' => optional($payment->user)->name ?: '-',
                    'notes' => $payment->notes,
                ];
            });
        });
    }

    protected function engineeringPaymentRows(Collection $engineerings): Collection
    {
        return $engineerings->flatMap(function ($engineering) {
            return $engineering->payments->map(function ($payment) use ($engineering) {
                return [
                    'section' => 'الهندسة والتنظيم',
                    'reference' => $engineering->file_number ?: ('ملف هندسة #' . $engineering->id),
                    'payment_number' => $payment->payment_number ?: $payment->id,
                    'payment_method' => $payment->payment_method,
                    'amount' => (float) $payment->amount,
                    'payment_date' => optional($payment->payment_date)->format('Y-m-d') ?: (string) $payment->payment_date,
                    'collector' => optional($payment->user)->name ?: '-',
                    'notes' => $payment->notes,
                ];
            });
        });
    }

    protected function requestPaymentRows(Collection $requests): Collection
    {
        return $requests->flatMap(function ($citizenRequest) {
            return $citizenRequest->payments->map(function ($payment) use ($citizenRequest) {
                return [
                    'section' => 'قلم الجمهور',
                    'reference' => 'طلب #' . $citizenRequest->id,
                    'payment_number' => $payment->payment_number ?: $payment->id,
                    'payment_method' => $payment->payment_method,
                    'amount' => (float) $payment->amount,
                    'payment_date' => optional($payment->payment_date)->format('Y-m-d') ?: (string) $payment->payment_date,
                    'collector' => optional($payment->user)->name ?: '-',
                    'notes' => $payment->notes,
                ];
            });
        });
    }
}
