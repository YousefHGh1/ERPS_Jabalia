{{-- نفس محتوى صفحة إنشاء الطلب بالكامل (قسم البحث + النموذج بالعمودين) --}}
@php
    $prefix = $prefix ?? '';
@endphp
<!-- قسم البحث -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-custom gutter-b example example-compact">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block">{{ __('رقم الهوية') }} </label>
                        <select class="form-control form-control-sm select2 flex-grow-1"
                            id="{{ $prefix }}kt_select2_111"></select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block">{{ __('البحث بالاسم') }}:</label>
                        <select class="form-control form-control-sm select2 flex-grow-1"
                            id="{{ $prefix }}kt_select2_222"></select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block">{{ __('البحث برقم المكلف') }}:</label>
                        <select class="form-control form-control-sm select2 flex-grow-1"
                            id="{{ $prefix }}kt_select2_333"></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form request-create-form" id="{{ $prefix }}kt_form_2" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-7">
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 100px;">{{ __('رقم المكلف') }}:</label>
                            <div class="flex-grow-1">
                                <select class="form-control form-control-sm select2"
                                    id="{{ $prefix }}kt_select2_3" name="mokalaf_id" required>
                                    <option value=""></option>
                                </select>
                                <span class="text-danger invalid-feedback"
                                    id="{{ $prefix }}mokalaf_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 100px;">{{ __('اسم المكلف') }}:</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}mokalaf_name" readonly
                                style="background-color: #f3f6f9; cursor: not-allowed;"
                                placeholder="{{ __('سيتم التعبئة تلقائياً') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('العنوان') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}address" name="address">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 100px;">{{ __('نوع الطلب') }}:</label>
                            <div class="flex-grow-1 request-type-select-wrapper">
                                <select class="form-control form-control-sm select2 request-type-select"
                                    id="{{ $prefix }}kt_select2_4" name="request_type_id" required
                                    data-placeholder="{{ __('اختر نوع الطلب') }}">
                                    <option value="">{{ __('اختر نوع الطلب') }}</option>
                                    @isset($requestTypes)
                                        @forelse($requestTypes as $request_type)
                                            <option value="{{ $request_type->id }}">{{ $request_type->name }}</option>
                                        @empty
                                            <option value="" disabled>{{ __('لا توجد أنواع طلبات') }}</option>
                                        @endforelse
                                    @else
                                        <option value="" disabled>{{ __('جاري التحميل...') }}</option>
                                    @endisset
                                </select>
                                <span class="text-danger invalid-feedback"
                                    id="{{ $prefix }}request_type_id_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        @include('requests.partials.fee_amount_trigger', [
                            'prefix' => $prefix,
                            'feeModalId' => $prefix . 'requestFeeAmountModal',
                        ])
                    </div>
                    <div class="form-group">
                        <div class="checkbox-list">
                            <label class="checkbox checkbox-outline">
                                <input type="checkbox" id="{{ $prefix }}join_" name="join" value="1" />
                                <span></span>
                                {{ __('طلب مشترك') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group d-none" id="{{ $prefix }}common_name">
                        <div class="d-flex align-items-center">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 140px;">{{ __('أسماء مقدمي الطلب') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1" name="common_name"
                                id="{{ $prefix }}common_name_input" value="{{ old('common_name') }}" />
                        </div>
                    </div>
                    <div class="form-group d-flex align-items-start">
                        <label class="mb-0 mr-2 text-nowrap pt-2 d-block d-md-inline-block"
                            style="min-width: 100px;">{{ __('نص الطلب') }}:</label>
                        <div class="flex-grow-1">
                            <input type="text" name="text" id="{{ $prefix }}request_text"
                                class="form-control form-control-solid" placeholder="{{ __('أدخل نص الطلب') }}"
                                required />
                            <span class="text-danger invalid-feedback" id="{{ $prefix }}text_error"></span>
                        </div>
                    </div>
                    <div class="form-group d-flex align-items-center">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 100px;">{{ __('المرفقات') }}
                            ({{ __('اختياري') }}):</label>
                        <div class="flex-grow-1">
                            <input type="file" name="attachments[]" id="{{ $prefix }}request_attachments"
                                class="form-control form-control-sm" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.zip,.rar" />
                            <span
                                class="form-text text-muted">{{ __('يمكنك اختيار عدة ملفات. الصيغ المسموحة: PDF, Word, Excel, صور، أرشيف') }}</span>
                        </div>
                    </div>
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
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 95px;">{{ __('الموبايل') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}mobile" name="mobile" placeholder="{{ __('الموبايل') }}" />
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 95px;">{{ __('حساب الجباية') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}jebaia_no" name="jebaia_no"
                                placeholder="{{ __('حساب الجباية') }}" />
                        </div>
                    </div>
                    <div class="form-group d-flex align-items-center mb-2">
                        <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الحي') }} <span
                                class="text-muted font-weight-normal">({{ __('اختياري') }})</span></label>
                        <input type="text" class="form-control form-control-sm flex-grow-1"
                            id="{{ $prefix }}neighborhood" name="neighborhood"
                            placeholder="{{ __('الحي') }}" />
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('القطعة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}widget" name="widget" placeholder="{{ __('القطعة') }}" />
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('القسيمة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}voucher" name="voucher" placeholder="{{ __('القسيمة') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('المقسم') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}divider" name="divider" placeholder="{{ __('المقسم') }}" />
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('البناية') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}building" name="building"
                                placeholder="{{ __('البناية') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الطابق') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}floor" name="floor" placeholder="{{ __('الطابق') }}" />
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('الشقة') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}apartment" name="apartment"
                                placeholder="{{ __('الشقة') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block" style="min-width: 95px;">{{ __('المالك') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}owner" name="owner" placeholder="{{ __('المالك') }}" />
                        </div>
                        <div class="col-xl-6 form-group d-flex align-items-center mb-2">
                            <label class="mb-0 mr-2 text-nowrap d-block d-md-inline-block"
                                style="min-width: 95px;">{{ __('المستأجر') }}</label>
                            <input type="text" class="form-control form-control-sm flex-grow-1"
                                id="{{ $prefix }}tenant" name="tenant" placeholder="{{ __('المستأجر') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@include('requests.partials.fee_amount_modal', [
    'prefix' => $prefix,
    'feeModalId' => $prefix . 'requestFeeAmountModal',
])
