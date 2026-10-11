{{-- يُوضع خارج <form> الرئيسي (أو في نهاية الصفحة) لتفادي تداخل مع الإرسال --}}
@php
    $p = $prefix ?? '';
    $modalId = $feeModalId ?? ($p . 'requestFeeAmountModal');
@endphp
{{-- backdrop=false: عند فتح هذا المودال فوق «إضافة طلب» لا يُنشأ backdrop ثانٍ فيتجمّد التفاعل مع الشاشة (Bootstrap 4). --}}
<div class="modal fade fee-amount-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-fee-prefix="{{ $p }}" data-backdrop="false" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bolder" id="{{ $modalId }}Label">{{ __('الرسم المستحق (قلم الجمهور)') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('إغلاق') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-4">
                <label class="font-weight-bold">{{ __('المبلغ') }} (₪)</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-solid" id="{{ $modalId }}_fee_input" placeholder="0.00">
                <p class="text-muted font-size-sm mt-3 mb-0">{{ __('اترك الحقل فارغاً أو صفراً إن لم يكن هناك رسم. يُستخدم مع الصندوق المركزي مثل الحرف والهندسة.') }}</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="button" class="btn btn-light-warning font-weight-bold" id="{{ $modalId }}_fee_clear">{{ __('مسح الرسم') }}</button>
                <button type="button" class="btn btn-primary font-weight-bold" id="{{ $modalId }}_fee_apply">{{ __('حفظ') }}</button>
            </div>
        </div>
    </div>
</div>
