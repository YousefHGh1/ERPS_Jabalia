@extends('layout.master')

@section('title')
    دفع التحصيل الخارجي
@endsection

@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
    لوحة التحكم
@endsection

@section('sub_title')
    دفع التحصيل الخارجي
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="دفع التحصيل الخارجي #{{ $externalCollection->id }}"
                subtitle="معالجة دفعة للتحصيل الخارجي"
            >
                <x-slot name="actions">
                    <a href="{{ route('external-collections.show', $externalCollection) }}" class="btn btn-light-info font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> العودة للتفاصيل
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                    <div class="card card-body bg-light border-0 shadow-sm p-8 mb-7" style="border-radius: 12px;">
                        <h6 class="font-weight-bolder text-dark mb-4"><i class="fas fa-archive text-info mr-2"></i> حالة الرزمة اليومية:</h6>
                        @if($openBatch)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong class="text-dark">رقم الرزمة:</strong> <span class="font-weight-bold text-info">{{ $openBatch->id }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-dark">الحالة:</strong> <span class="badge badge-success">مفتوحة</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-dark">المبلغ الإجمالي:</strong> <span class="font-weight-bold">{{ number_format($openBatch->total_amount, 2) }} شيكل</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-dark">عدد الدفعات:</strong> <span class="font-weight-bold">{{ $openBatch->payments_count }}</span>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info border-0">
                                <i class="fas fa-info-circle"></i> سيتم إنشاء رزمة يومية جديدة تلقائياً عند تسجيل الدفعة.
                            </div>
                        @endif
                    </div>
                    <div class="card card-body bg-light border-0 shadow-sm p-8 mb-7" style="border-radius: 12px;">
                        <h6 class="font-weight-bolder text-dark mb-4"><i class="fas fa-file-invoice text-info mr-2"></i> معلومات التحصيل:</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <strong class="text-dark">المكلف:</strong> <span class="font-weight-bold">{{ $externalCollection->taxpayer_full_name }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong class="text-dark">المبلغ الإجمالي:</strong> <span class="text-danger font-weight-bold">{{ number_format($externalCollection->amount, 2) }} شيكل</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong class="text-dark">المبلغ المتبقي:</strong> <span class="text-success font-weight-bold">{{ number_format($externalCollection->remaining_amount, 2) }} شيكل</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('external-collections.process-payment', $externalCollection) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="font-weight-bold text-dark">المبلغ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid" id="amount" name="amount"
                                               value="{{ old('amount', $externalCollection->remaining_amount) }}"
                                               step="0.01" min="0.01" max="{{ $externalCollection->remaining_amount }}" required>
                                        <span class="input-group-text bg-light">شيكل</span>
                                    </div>
                                    <small class="text-muted mt-1">الحد الأقصى: {{ number_format($externalCollection->remaining_amount, 2) }} شيكل</small>
                                    @error('amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="font-weight-bold text-dark">طريقة الدفع <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" id="payment_method" name="payment_method" required>
                                        <option value="">اختر طريقة الدفع...</option>
                                        @foreach($paymentMethods as $key => $method)
                                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="font-weight-bold text-dark">تاريخ الدفع <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-solid" id="payment_date" name="payment_date"
                                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                           max="{{ now()->format('Y-m-d') }}" required>
                                    @error('payment_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_number" class="font-weight-bold text-dark">رقم الإيصال</label>
                                    <input type="text" class="form-control form-control-solid" id="receipt_number" name="receipt_number"
                                           value="{{ old('receipt_number') }}" placeholder="اختياري">
                                    @error('receipt_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="font-weight-bold text-dark">ملاحظات</label>
                                    <textarea class="form-control form-control-solid" id="notes" name="notes" rows="3" placeholder="ملاحظات إضافية (اختياري)">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($openBatch)
                            <div class="alert alert-success border-0 mb-7" style="border-radius: 12px;">
                                <i class="fas fa-check-circle"></i> سيتم إضافة هذه الدفعة إلى الرزمة اليومية #{{ $openBatch->id }}
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mb-7" style="border-radius: 12px;">
                                <i class="fas fa-exclamation-triangle"></i> لا توجد رزمة يومية مفتوحة حالياً. سيتم تسجيل الدفعة بدون رزمة.
                            </div>
                        @endif

                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('external-collections.show', $externalCollection) }}" class="btn btn-light-secondary font-weight-bolder">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                        <i class="fas fa-money-bill-wave"></i> تسجيل الدفعة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // تحديث المبلغ المتبقي عند تغيير مبلغ الدفع
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        const remaining = {{ $externalCollection->remaining_amount }};
        const newRemaining = remaining - amount;
        
        // يمكن إضافة رسالة توضيحية هنا
        if (amount > remaining) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endpush
