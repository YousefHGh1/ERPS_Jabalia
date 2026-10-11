{{--
  نموذج تعديل الطلب — نفس تخطيط إضافة الطلب (_create_form_full) بدون قسم البحث.
  $fieldPrefix: '' للصفحة، مثل erm_ للمودال.
  $layoutMode: 'page' | 'modal'
  $nestedInModal: true داخل مودال الفهرس (دفعات فرعية)
--}}
@php
    $p = $fieldPrefix ?? '';
    $feeModalId = ($p === '' || $p === null) ? 'requestFeeAmountModal' : $p . 'requestFeeAmountModal';
    $nested = !empty($nestedInModal);
    $layoutMode = $layoutMode ?? 'page';
    $m = $requestModel->mokalaf;
    $mokalafName = $m ? $m->name . ($m->trashed() ? ' (' . __('محذوف') . ')' : '') : '';
@endphp

<div class="request-edit-root" data-field-prefix="{{ $p }}">
<form method="POST" action="{{ route('requests.update', $requestModel) }}" enctype="multipart/form-data" class="request-edit-form form" id="{{ $p }}request_edit_form">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-7">
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('رقم الطلب') }}:</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" value="{{ $requestModel->id }}" readonly
                                style="background-color: #f3f6f9; cursor: not-allowed;">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('الحالة') }}:</label>
                            <div class="flex-grow-1">
                                <input type="text" class="form-control form-control-sm" value="{{ $requestModel->status->name ?? '-' }}" readonly
                                    style="background-color: #f3f6f9; cursor: not-allowed;">
                                @if($requestModel->status_reason_sections_text)
                                    <span class="form-text text-muted font-size-sm mb-0">{{ $requestModel->status_reason_sections_text }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('رقم المكلف') }}:</label>
                            <div class="flex-grow-1">
                                <select name="mokalaf_id" class="form-control form-control-sm @error('mokalaf_id') is-invalid @enderror" required>
                                    @foreach($mokalafs as $mokalaf)
                                        <option value="{{ $mokalaf->id }}" {{ (int) $requestModel->mokalaf_id === (int) $mokalaf->id ? 'selected' : '' }}>
                                            {{ $mokalaf->name }}{{ $mokalaf->trashed() ? ' (' . __('محذوف') . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mokalaf_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('اسم المكلف') }}:</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1 js-req-edit-mokalaf-name" readonly
                                style="background-color: #f3f6f9; cursor: not-allowed;"
                                value="{{ $mokalafName }}"
                                placeholder="{{ __('سيتم التعبئة تلقائياً') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('العنوان') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" name="address" id="{{ $p }}address"
                                value="{{ old('address', $requestModel->address) }}">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('نوع الطلب') }}:</label>
                            <div class="flex-grow-1">
                                <select name="request_type_id" class="form-control form-control-sm @error('request_type_id') is-invalid @enderror" required>
                                    <option value="">{{ __('اختر نوع الطلب') }}</option>
                                    @foreach($requestTypes as $type)
                                        <option value="{{ $type->id }}" {{ (int) $requestModel->request_type_id === (int) $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('request_type_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('الموعد النهائي') }}</label>
                            <input name="deadline" type="date" class="form-control form-control-sm flex-grow-1 @error('deadline') is-invalid @enderror"
                                value="{{ old('deadline', $requestModel->deadline ? $requestModel->deadline->format('Y-m-d') : '') }}">
                            @error('deadline')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('عاجل') }}</label>
                            <div class="checkbox-list pt-1">
                                <label class="checkbox checkbox-outline mb-0">
                                    <input type="checkbox" name="is_urgent" value="1" @checked(old('is_urgent', $requestModel->is_urgent))>
                                    <span></span>
                                    {{ __('طلب عاجل') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        @include('requests.partials.fee_amount_trigger', [
                            'prefix' => $p,
                            'feeModalId' => $feeModalId,
                            'feeAmountInitial' => old('fee_amount', $requestModel->fee_amount),
                        ])
                    </div>

                    <div class="form-group mb-2">
                        <div class="checkbox-list">
                            <label class="checkbox checkbox-outline">
                                <input type="checkbox" name="join" id="{{ $p }}request_edit_join" value="1" class="js-req-edit-join" @checked(old('join', $requestModel->join))>
                                <span></span>
                                {{ __('طلب مشترك') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group js-req-edit-common-wrap @if(!old('join', $requestModel->join)) d-none @endif mb-2" id="{{ $p }}request_edit_common_name_wrap">
                        <div class="d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 140px;">{{ __('أسماء مقدمي الطلب') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1 @error('common_name') is-invalid @enderror" name="common_name"
                                value="{{ old('common_name', $requestModel->common_name) }}" placeholder="{{ __('أدخل أسماء المقدمين') }}">
                        </div>
                        @error('common_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group d-flex align-items-start mb-2">
                        <label class="mb-0 mr-2 text-nowrap pt-2 d-block d-md-inline-block" style="min-width: 100px;">{{ __('نص الطلب') }}:</label>
                        <div class="flex-grow-1">
                            <input type="text" name="text" id="{{ $p }}request_text" maxlength="5000"
                                class="form-control form-control-solid form-control-lg @error('text') is-invalid @enderror"
                                value="{{ old('text', $requestModel->text) }}" placeholder="{{ __('أدخل نص الطلب') }}" required>
                            @error('text')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="d-block mb-2 font-weight-bold">{{ __('المرفقات الحالية') }}</label>
                        @if($requestModel->attachments->count() > 0)
                            <div class="mb-3">
                                @foreach($requestModel->attachments as $attachment)
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded flex-wrap bg-light">
                                        <span class="text-dark font-size-sm">{{ $attachment->file_name }}</span>
                                        <div>
                                            <a href="{{ route('requests.download-attachment', [$requestModel, $attachment]) }}"
                                               class="btn btn-xs btn-light-primary mr-1" target="_blank">
                                                <i class="fas fa-download"></i> {{ __('تحميل') }}
                                            </a>
                                            <a href="javascript:;" class="btn btn-xs btn-light-danger js-req-delete-attachment"
                                               data-id="{{ $attachment->id }}" data-request-id="{{ $requestModel->id }}">
                                                <i class="fas fa-trash"></i> {{ __('حذف') }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted font-size-sm mb-3">{{ __('لا توجد مرفقات') }}</p>
                        @endif
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block">{{ __('إضافة مرفقات') }} ({{ __('اختياري') }}):</label>
                        <div class="mt-2">
                            <input type="file" name="attachments[]" id="{{ $p }}request_attachments" class="form-control form-control-sm" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.zip,.rar">
                            <span class="form-text text-muted">{{ __('يمكنك اختيار عدة ملفات. الصيغ المسموحة: PDF, Word, Excel, صور، أرشيف') }}</span>
                        </div>
                        @error('attachments.*')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    @if($layoutMode === 'modal')
                        <button type="button" class="btn btn-warning font-weight-bold mr-2" data-dismiss="modal">
                            {{ __('إلغاء') }}
                        </button>
                        <a href="{{ route('requests.edit', $requestModel) }}" target="_blank" class="btn btn-light-primary font-weight-bold mr-2">
                            <i class="fas fa-book-open"></i> {{ __('فتح في صفحة كاملة') }}
                        </a>
                    @else
                        <a href="{{ route('requests.index') }}" class="btn btn-warning font-weight-bold mr-2">{{ __('إلغاء') }}</a>
                    @endif
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-check"></i> {{ __('تحديث') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">{{ __('عنوان المكلف بالتفصيل') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الموبايل') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1 @error('mobile') is-invalid @enderror"
                                id="{{ $p }}mobile" name="mobile" placeholder="{{ __('الموبايل') }}"
                                value="{{ old('mobile', $requestModel->mobile) }}">
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('حساب الجباية') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $p }}jebaia_no" name="jebaia_no" placeholder="{{ __('حساب الجباية') }}"
                                value="{{ old('jebaia_no', $requestModel->jebaia_no) }}">
                        </div>
                    </div>
                    @error('mobile')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror
                    <div class="form-group d-flex align-items-center mb-2">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الحي') }} <span class="text-muted font-weight-normal">({{ __('اختياري') }})</span></label>
                        <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}neighborhood" name="neighborhood"
                            placeholder="{{ __('الحي') }}" value="{{ old('neighborhood', $requestModel->neighborhood) }}">
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('القطعة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}widget" name="widget"
                                placeholder="{{ __('القطعة') }}" value="{{ old('widget', $requestModel->widget) }}">
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('القسيمة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}voucher" name="voucher"
                                placeholder="{{ __('القسيمة') }}" value="{{ old('voucher', $requestModel->voucher) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('المقسم') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}divider" name="divider"
                                placeholder="{{ __('المقسم') }}" value="{{ old('divider', $requestModel->divider) }}">
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('البناية') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}building" name="building"
                                placeholder="{{ __('البناية') }}" value="{{ old('building', $requestModel->building) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الطابق') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}floor" name="floor"
                                placeholder="{{ __('الطابق') }}" value="{{ old('floor', $requestModel->floor) }}">
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الشقة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}apartment" name="apartment"
                                placeholder="{{ __('الشقة') }}" value="{{ old('apartment', $requestModel->apartment) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('المالك') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}owner" name="owner"
                                placeholder="{{ __('المالك') }}" value="{{ old('owner', $requestModel->owner) }}">
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('المستأجر') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" id="{{ $p }}tenant" name="tenant"
                                placeholder="{{ __('المستأجر') }}" value="{{ old('tenant', $requestModel->tenant) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@include('requests.partials.fee_amount_modal', [
    'prefix' => $p,
    'feeModalId' => $feeModalId,
])

@php
    $showRequestPayments = ((float) ($requestModel->fee_amount ?? 0) > 0) || $requestModel->payments->isNotEmpty();
@endphp
@if($showRequestPayments)
    <div class="separator separator-dashed my-8"></div>
    <div class="card card-custom gutter-b example example-compact border-left border-3 border-primary" id="{{ $p }}request-payments-section">
        <div class="card-header border-0 py-4">
            <h3 class="card-title">{{ __('الدفعات — الصندوق المركزي (قلم الجمهور)') }}</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-success font-weight-bolder" data-toggle="modal" data-target="#{{ $p }}addRequestPaymentModal">
                    <i class="fas fa-plus"></i> {{ __('إضافة دفعة') }}
                </button>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="alert alert-light-primary mb-4">
                <strong>{{ __('الرسم المستحق:') }}</strong> {{ number_format((float) ($requestModel->fee_amount ?? 0), 2) }} ₪
                &nbsp;|&nbsp;
                <strong>{{ __('المدفوع:') }}</strong> {{ number_format((float) ($requestModel->paid_amount ?? 0), 2) }} ₪
                &nbsp;|&nbsp;
                <strong>{{ __('المتبقي:') }}</strong> {{ number_format((float) ($requestModel->remaining_amount ?? 0), 2) }} ₪
            </div>
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center table-sm">
                    <thead>
                        <tr class="text-muted font-weight-bolder text-uppercase font-size-sm">
                            <th>{{ __('رقم الدفعة') }}</th>
                            <th>{{ __('مرجع') }}</th>
                            <th>{{ __('المبلغ') }}</th>
                            <th>{{ __('طريقة الدفع') }}</th>
                            <th>{{ __('التاريخ') }}</th>
                            <th>{{ __('المتبقي بعد') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requestModel->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_number ?? '—' }}</td>
                                <td>{{ $payment->receipt_number ?? '-' }}</td>
                                <td class="font-weight-bolder">{{ number_format((float) $payment->amount, 2) }} ₪</td>
                                <td>
                                    @if($payment->payment_method === 'cash') {{ __('نقدي') }}
                                    @elseif($payment->payment_method === 'bank') {{ __('حوالة بنكية') }}
                                    @elseif($payment->payment_method === 'check') {{ __('شيك') }}
                                    @else {{ $payment->payment_method }}
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date?->format('Y-m-d') }}</td>
                                <td class="font-weight-bolder">{{ number_format((float) $payment->balance_after, 2) }} ₪</td>
                                <td>
                                    <a href="{{ route('receipts.payments.request', $payment) }}" target="_blank"
                                        class="btn btn-xs btn-light-primary" title="{{ __('طباعة الإيصال') }}">
                                        {{ __('طباعة إيصال') }}
                                    </a>
                                    @if($payment->status === 'active')
                                        <button type="button" class="btn btn-xs btn-light-danger js-req-open-cancel-payment" data-payment-id="{{ $payment->id }}" title="{{ __('إلغاء الدفعة') }}">{{ __('إلغاء') }}</button>
                                    @else
                                        <span class="label label-inline label-light-danger label-sm">{{ __('ملغاة') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted font-size-sm mb-0">{{ __('تسجيل الدفعة يتطلب فتح رزمة يومية في الصندوق المركزي.') }}</p>
        </div>
    </div>

    <div class="modal fade req-nested-submodal" id="{{ $p }}addRequestPaymentModal" tabindex="-1" role="dialog" aria-hidden="true" @if($nested) data-backdrop="false" data-keyboard="true" @endif>
        <div class="modal-dialog modal-lg" role="document">
            <form class="js-req-add-payment-form" action="{{ route('requests.add-payment', $requestModel) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('إضافة دفعة — طلب رقم') }} {{ $requestModel->id }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('المبلغ') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-solid" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('تاريخ الدفع') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('طريقة الدفع') }}</label>
                                    <select name="payment_method" class="form-control form-control-solid">
                                        <option value="cash">{{ __('نقدي') }}</option>
                                        <option value="bank">{{ __('حوالة بنكية') }}</option>
                                        <option value="check">{{ __('شيك') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('رقم المرجع (السند/الشيك)') }}</label>
                                    <input type="text" name="reference_number" class="form-control form-control-solid">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('ملاحظات') }}</label>
                            <textarea name="notes" class="form-control form-control-solid" rows="2"></textarea>
                        </div>
                        <div class="alert alert-info mb-0">
                            <strong>{{ __('المبلغ المتبقي:') }}</strong> {{ number_format((float) ($requestModel->remaining_amount ?? 0), 2) }} ₪
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('حفظ الدفعة') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade req-nested-submodal" id="{{ $p }}cancelRequestPaymentModal" tabindex="-1" role="dialog" aria-hidden="true" @if($nested) data-backdrop="false" data-keyboard="true" @endif>
        <div class="modal-dialog" role="document">
            <form class="js-req-cancel-payment-form" action="#" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('إلغاء دفعة') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('سبب الإلغاء') }} <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" required minlength="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('تراجع') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('تأكيد الإلغاء') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
</div>
