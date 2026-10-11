@if(request('modal'))
    <input type="hidden" name="from_modal" value="1">
@endif

<form method="POST" action="{{ route('imprisonment_orders.update', $order) }}" class="form edit-imprisonment-order-form" data-order-id="{{ $order->id }}">
    @csrf
    @method('PUT')
    @if(request('modal'))
        <input type="hidden" name="from_modal" value="1">
    @endif

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div style="background:#f3f6f9;border-radius:8px;padding:15px;margin-bottom:10px;">
                <h5 class="text-dark font-weight-bold mb-4" style="font-size:14px;">
                    {{ __('بيانات أمر الحبس الأساسية') }}
                </h5>

                {{-- الصف الأول: رقم الأمر + تاريخ الأمر --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('رقم الأمر') }}
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="order_number"
                               class="form-control"
                               value="{{ $order->order_number }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('تاريخ الأمر') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="date"
                               name="order_date"
                               class="form-control"
                               value="{{ optional($order->order_date)->format('Y-m-d') }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>
                </div>

                {{-- الصف الثاني: المحكمة + القاضي --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('المحكمة') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-4">
                        <input type="text"
                               name="court_name"
                               class="form-control"
                               value="{{ $order->court_name }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('القاضي') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="judge_name"
                               class="form-control"
                               value="{{ $order->judge_name }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>
                </div>

                {{-- الصف الثالث: المتهم + رقم الهوية --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('اسم المتهم') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-4">
                        <input type="text"
                               name="defendant_name"
                               class="form-control"
                               value="{{ $order->defendant_name }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('رقم الهوية') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="defendant_identity"
                               class="form-control"
                               value="{{ $order->defendant_identity }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>
                </div>

                {{-- الصف الرابع: مدة ونوع الحبس --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('مدة الحبس') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="imprisonment_period"
                               class="form-control"
                               value="{{ $order->imprisonment_period }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('نوع الحبس') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="imprisonment_type"
                               class="form-control"
                               value="{{ $order->imprisonment_type }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>
                </div>

                {{-- الصف الخامس: رقم القضية / سنة القضية --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('رقم القضية') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="case_number"
                               class="form-control"
                               value="{{ $order->case_number }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('سنة القضية') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-2">
                        <input type="number"
                               name="case_year"
                               class="form-control"
                               value="{{ $order->case_year }}"
                               style="font-size:13px;padding:6px 12px;">
                    </div>
                </div>

                {{-- الصف السادس: الحالة --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('الحالة') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <select name="status" class="form-control" style="font-size:13px;">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                            <option value="active" {{ $order->status === 'active' ? 'selected' : '' }}>{{ __('نشط') }}</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                        </select>
                    </div>
                </div>

                {{-- الصف السابع: الوصف --}}
                <div class="form-group row mb-0">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                        {{ __('الوصف') }}
                    </label>
                    <div class="col-lg-8">
                        <textarea name="description"
                                  class="form-control"
                                  rows="4"
                                  style="font-size:13px;padding:6px 12px;resize:vertical;">{{ $order->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div class="d-flex justify-content-end" style="padding-top:5px;">
                <button type="button"
                        class="btn btn-sm btn-warning font-weight-bolder mr-2"
                        data-dismiss="modal"
                        style="font-size:12px;padding:6px 16px;">
                    <i class="fas fa-times-circle"></i> {{ __('إلغاء') }}
                </button>
                <button type="submit"
                        class="btn btn-sm btn-primary font-weight-bolder"
                        style="font-size:12px;padding:6px 16px;">
                    <i class="fas fa-check"></i> {{ __('تحديث') }}
                </button>
            </div>
        </div>
    </div>
</form>

