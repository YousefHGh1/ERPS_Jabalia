{{-- يُوضع داخل النموذج: حقل مخفي + ملخص + زر يفتح المودال --}}
@php
    $p = $prefix ?? '';
    $modalId = $feeModalId ?? ($p . 'requestFeeAmountModal');
    $raw = isset($feeAmountInitial) ? $feeAmountInitial : old('fee_amount');
    $feeStr = ($raw === null || $raw === '') ? '' : (string) $raw;
@endphp
<input type="hidden" name="fee_amount" id="{{ $p }}request_fee_amount_hidden" value="{{ $feeStr }}">
<div class="form-group mb-0">
    <label class="d-block font-weight-bold text-dark">{{ __('الرسم المستحق (قلم الجمهور)') }} <span class="text-muted font-weight-normal">({{ __('اختياري') }})</span></label>
    <div class="d-flex align-items-center flex-wrap mt-2">
        <span id="{{ $p }}request_fee_amount_summary" class="text-dark font-weight-bolder mr-3 mb-2 mb-md-0" data-empty-label="{{ __('لا يوجد رسم محدد') }}"></span>
        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold mb-2 mb-md-0" data-toggle="modal" data-target="#{{ $modalId }}">
            <i class="fas fa-pen"></i> {{ __('تعيين الرسم') }}
        </button>
    </div>
    <span class="form-text text-muted">{{ __('إن وُجد رسم يُحصَّل عبر الصندوق المركزي، عيّنه هنا لتسجيل الدفعات لاحقاً من تعديل الطلب.') }}</span>
    @error('fee_amount')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
