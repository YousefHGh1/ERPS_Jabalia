@extends('layout.master')

@section('title')
    تفاصيل التحصيل الخارجي
@endsection

@php
use App\Models\ExternalCollectionPayment;
@endphp

@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
    لوحة التحكم
@endsection

@section('sub_title')
    تفاصيل التحصيل الخارجي
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="تفاصيل التحصيل الخارجي #{{ $externalCollection->id }}"
                subtitle="عرض معلومات التحصيل الخارجي والمدفوعات"
            >
                <x-slot name="actions">
                    <a href="{{ route('external-collections.index') }}" class="btn btn-light-primary font-weight-bolder mr-2">
                                            <i class="fas fa-arrow-right"></i> العودة
                                        </a>
                                        @can('external-collection-edit')
                                            <a href="{{ route('external-collections.edit', $externalCollection) }}" class="btn btn-light-warning font-weight-bolder mr-2">
                                                <i class="fas fa-edit"></i> تعديل
                                            </a>
                                        @endcan
                                        <form action="{{ route('external-collections.fix-calculations', $externalCollection) }}" method="POST" class="d-inline" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-light-info font-weight-bolder" 
                                                    onclick="return confirm('هل تريد إعادة حساب حالة الدفع؟')">
                                                <i class="fas fa-calculator"></i> إصلاح الحسابات
                                            </button>
                                        </form>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">معلومات المكلف</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>الاسم:</strong></td>
                                    <td>
                                        @if($externalCollection->mokalaf)
                                            <a href="{{ route('mokalafs.show', $externalCollection->mokalaf->id) }}" class="text-decoration-none">
                                                {{ $externalCollection->mokalaf->name }}
                                            </a>
                                        @else
                                            {{ $externalCollection->taxpayer_name ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهوية:</strong></td>
                                    <td>{{ $externalCollection->identity_number }}</td>
                                </tr>
                                @if($externalCollection->mokalaf)
                                    <tr>
                                        <td><strong>رقم المشغل:</strong></td>
                                        <td>{{ $externalCollection->mokalaf->license_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>العنوان:</strong></td>
                                        <td>{{ $externalCollection->mokalaf->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>الجوال:</strong></td>
                                        <td>{{ $externalCollection->mokalaf->mobile ?? '-' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">معلومات التحصيل</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>القسم:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $externalCollection->section->name ?? '-' }}
                                        </span>
                                        @if($externalCollection->section && $externalCollection->section->department)
                                            <br><small class="text-muted">{{ $externalCollection->section->department->name }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>المبلغ:</strong></td>
                                    <td class="fw-bold text-success">{{ number_format($externalCollection->amount, 2) }} شيكل</td>
                                </tr>
                                <tr>
                                    <td><strong>حالة الدفع:</strong></td>
                                    <td>
                                        {!! $externalCollection->payment_status_badge !!}
                                        @if($externalCollection->paid_amount > 0)
                                            <br><small class="text-muted">
                                                المدفوع: {{ number_format($externalCollection->paid_amount, 2) }} شيكل
                                                @if($externalCollection->remaining_amount > 0)
                                                    | المتبقي: {{ number_format($externalCollection->remaining_amount, 2) }} شيكل
                                                @endif
                                                ({{ number_format($externalCollection->payment_percentage, 1) }}%)
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>الملاحظات:</strong></td>
                                    <td>{{ $externalCollection->notes ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ الإنشاء:</strong></td>
                                    <td>{{ $externalCollection->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if($externalCollection->updated_at != $externalCollection->created_at)
                                    <tr>
                                        <td><strong>آخر تحديث:</strong></td>
                                        <td>{{ $externalCollection->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">معلومات المنشئ</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>المنشئ:</strong></td>
                                    <td>{{ $externalCollection->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ الإنشاء:</strong></td>
                                    <td>{{ $externalCollection->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        @if($externalCollection->updater)
                            <div class="col-md-6">
                                <h6 class="text-muted">معلومات المحدث</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>المحدث:</strong></td>
                                        <td>{{ $externalCollection->updater->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>تاريخ التحديث:</strong></td>
                                        <td>{{ $externalCollection->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<!-- قسم المدفوعات -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-muted mb-0">سجل المدفوعات</h6>
            @can('external-collection-payment')
                @if(!$externalCollection->isFullyPaid())
                    <a href="{{ route('external-collections.payment', $externalCollection) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> إضافة دفعة
                    </a>
                @endif
            @endcan
        </div>

        @if($externalCollection->payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>رقم الدفعة</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الدفع</th>
                            <th>المستخدم</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($externalCollection->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_number ?? $payment->id }}</td>
                                <td class="fw-bold">{{ number_format($payment->amount, 2) }} شيكل</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ExternalCollectionPayment::getPaymentMethods()[$payment->payment_method] ?? $payment->payment_method }}
                                    </span>
                                </td>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td>{{ $payment->user->name ?? '-' }}</td>
                                <td>
                                    @if($payment->status === 'paid')
                                        <span class="badge bg-success">مدفوع</span>
                                    @elseif($payment->status === 'cancelled')
                                        <span class="badge bg-danger">ملغي</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $payment->status }}</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($payment->notes, 30) ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('receipts.external-collection', $payment) }}" class="btn btn-info" title="طباعة الإيصال" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @can('external-collection-payment')
                                            @if($payment->isCancellable())
                                                <form action="{{ route('external-collections.payments.cancel', $payment) }}" method="POST"
                                                      class="d-inline"
                                                      onsubmit="var reason = prompt('أدخل سبب الإلغاء:'); if(!reason){ return false; } this.querySelector('input[name=cancellation_reason]').value = reason; return confirm('تأكيد إلغاء الدفعة؟');">
                                                    @csrf
                                                    <input type="hidden" name="cancellation_reason" value="">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="إلغاء الدفعة">
                                                        <i class="fas fa-times mr-1"></i> إلغاء
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" title="لا يمكن الإلغاء" disabled>
                                                    <i class="fas fa-lock mr-1"></i> مغلق
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> لا توجد مدفوعات مسجلة لهذا التحصيل
            </div>
        @endif
    </div>
            </div>
        </div>
    </div>
@endsection
