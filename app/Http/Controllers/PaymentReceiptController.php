<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentEngineering;
use App\Models\RequestPayment;
use App\Models\ExternalCollectionPayment;

class PaymentReceiptController extends Controller
{
    public function craft(Payment $payment)
    {
        $payment->load(['craftLicense', 'user']);

        return view('receipts.payment', [
            'sectionName' => 'الحرف والصناعات',
            'reference' => optional($payment->craftLicense)->file_number ?: ('رخصة #' . $payment->craft_license_id),
            'payerName' => optional($payment->craftLicense)->owner_name ?: '-',
            'paymentNumber' => $payment->payment_number ?: $payment->id,
            'amount' => (float) $payment->amount,
            'balanceAfter' => (float) ($payment->balance_after ?? 0),
            'paymentDate' => (string) $payment->payment_date,
            'paymentMethod' => (string) ($payment->payment_method ?? '-'),
            'collector' => optional($payment->user)->name ?: '-',
            'notes' => $payment->notes,
        ]);
    }

    public function engineering(PaymentEngineering $payment)
    {
        $payment->load(['engineering', 'user']);

        return view('receipts.payment', [
            'sectionName' => 'الهندسة والتنظيم',
            'reference' => optional($payment->engineering)->file_number ?: ('ملف هندسة #' . $payment->engineering_id),
            'payerName' => trim(implode(' ', array_filter([
                optional($payment->engineering)->first_name,
                optional($payment->engineering)->father_name,
                optional($payment->engineering)->grandfather_name,
                optional($payment->engineering)->family_name,
            ]))) ?: '-',
            'paymentNumber' => $payment->payment_number ?: $payment->id,
            'amount' => (float) $payment->amount,
            'balanceAfter' => (float) ($payment->balance_after ?? 0),
            'paymentDate' => optional($payment->payment_date)?->format('Y-m-d') ?: (string) $payment->payment_date,
            'paymentMethod' => (string) ($payment->payment_method ?? '-'),
            'collector' => optional($payment->user)->name ?: '-',
            'notes' => $payment->notes,
        ]);
    }

    public function request(RequestPayment $payment)
    {
        $payment->load(['citizenRequest.mokalaf', 'user']);

        return view('receipts.payment', [
            'sectionName' => 'قلم الجمهور',
            'reference' => 'طلب #' . $payment->request_id,
            'payerName' => optional(optional($payment->citizenRequest)->mokalaf)->name ?: '-',
            'paymentNumber' => $payment->payment_number ?: $payment->id,
            'amount' => (float) $payment->amount,
            'balanceAfter' => (float) ($payment->balance_after ?? 0),
            'paymentDate' => optional($payment->payment_date)?->format('Y-m-d') ?: (string) $payment->payment_date,
            'paymentMethod' => (string) ($payment->payment_method ?? '-'),
            'collector' => optional($payment->user)->name ?: '-',
            'notes' => $payment->notes,
        ]);
    }

    public function externalCollection(ExternalCollectionPayment $payment)
    {
        $payment->load(['externalCollection.mokalaf', 'externalCollection.section', 'user']);

        return view('receipts.payment', [
            'sectionName' => 'التحصيل الخارجي',
            'reference' => 'تحصيل #' . $payment->external_collection_id,
            'payerName' => $payment->externalCollection->taxpayer_full_name,
            'paymentNumber' => $payment->payment_number ?: $payment->id,
            'amount' => (float) $payment->amount,
            'balanceAfter' => (float) ($payment->balance_after ?? 0),
            'paymentDate' => $payment->payment_date->format('Y-m-d'),
            'paymentMethod' => ExternalCollectionPayment::getPaymentMethods()[$payment->payment_method] ?? $payment->payment_method,
            'collector' => optional($payment->user)->name ?: '-',
            'notes' => $payment->notes,
        ]);
    }
}
