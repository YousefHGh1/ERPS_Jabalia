@extends('layout.master')

@section('title')
    {{ __('إضافة طلب جديد') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الطلبات') }} / {{ __('إضافة جديد') }}
@endsection

@section('content')
    <div class="container-fluid request-create-page">
        <!-- Search Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom gutter-b example example-compact request-search-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-1">
                                <label class="d-block d-md-inline-block">البحث برقم الهوية | مشتغل مرخص:</label>
                                <div>
                                    <select class="form-control select2" id="kt_select2_111"></select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="d-block d-md-inline-block">البحث بالاسم:</label>
                                <div>
                                    <select class="form-control select2" id="kt_select2_222"></select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="d-block d-md-inline-block">البحث برقم المكلف:</label>
                                <div>
                                    <select class="form-control select2" id="kt_select2_333"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('requests.store') }}" method="post" class="form" id="kt_form_2"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <div class="card card-custom gutter-b example example-compact request-main-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="d-block d-md-inline-block">رقم المكلف:</label>
                                    <div>
                                        <select class="form-control select2 @error('mokalaf_id') is-invalid @enderror"
                                            id="kt_select2_3" name="mokalaf_id" required disabled
                                            style="background-color: #f3f6f9; cursor: not-allowed;">
                                            <option value=""></option>
                                        </select>
                                        @error('mokalaf_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="d-block d-md-inline-block">اسم المكلف:</label>
                                    <input type="text" class="form-control" id="mokalaf_name" readonly
                                        style="background-color: #f3f6f9; cursor: not-allowed;"
                                        placeholder="سيتم التعبئة تلقائياً">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="d-block d-md-inline-block">العنوان:</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="d-block d-md-inline-block mb-0">نوع الطلب:</label>
                                        @can('store request_types')
                                            <button type="button" class="btn btn-sm btn-light-success" data-toggle="modal"
                                                data-target="#create-request-type-modal">
                                                <i class="fas fa-plus"></i> إضافة نوع
                                            </button>
                                        @endcan
                                    </div>
                                    <div>
                                        <select class="form-control select2 @error('request_type_id') is-invalid @enderror"
                                            id="kt_select2_4" name="request_type_id" required>
                                            <option value=""></option>
                                            @foreach ($requestTypes as $request_type)
                                                <option value="{{ $request_type->id }}">{{ $request_type->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('request_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    @include('requests.partials.fee_amount_trigger', [
                                        'prefix' => '',
                                        'feeModalId' => 'requestFeeAmountModal',
                                    ])
                                </div>

                                @can('store request_types')
                                    <div class="modal fade" id="create-request-type-modal" tabindex="-1" role="dialog"
                                        aria-labelledby="create-request-type-modal-label" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-success py-4">
                                                    <h5 class="modal-title text-white font-weight-bolder"
                                                        id="create-request-type-modal-label">إضافة نوع طلب</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="إغلاق"><i aria-hidden="true"
                                                            class="fas fa-times"></i></button>
                                                </div>
                                                <div id="create-request-type-form">
                                                    <div class="modal-body p-10">
                                                        <div class="form-group mb-0">
                                                            <label class="font-weight-bold">اسم نوع الطلب <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="name"
                                                                id="new-request-type-name"
                                                                class="form-control form-control-solid"
                                                                placeholder="أدخل اسم نوع الطلب">
                                                            <div class="text-danger mt-1" id="request-type-name-error"></div>
                                                        </div>
                                                        <div class="form-group mt-5 mb-0">
                                                            <label class="font-weight-bold">الوصف</label>
                                                            <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="أدخل الوصف"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-warning font-weight-bold"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button type="button" class="btn btn-success font-weight-bold"
                                                            id="save-request-type">حفظ ومتابعة المسارات</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan

                                @can('store request_workflows')
                                    <div class="modal fade" id="create-request-workflow-modal" tabindex="-1" role="dialog"
                                        aria-labelledby="create-request-workflow-modal-label" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-primary py-4">
                                                    <h5 class="modal-title text-white font-weight-bolder"
                                                        id="create-request-workflow-modal-label">إضافة مسارات نوع الطلب</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="إغلاق"><i aria-hidden="true"
                                                            class="fas fa-times"></i></button>
                                                </div>
                                                <div id="create-request-workflow-form">
                                                    <div class="modal-body p-10">
                                                        <div
                                                            class="alert alert-light-primary d-flex align-items-center p-4 mb-6">
                                                            <i class="fas fa-info-circle text-primary mr-3"></i><span>تم
                                                                إنشاء مسار الاستلام تلقائيًا. يمكنك إضافة مسارات أخرى لهذا
                                                                النوع.</span>
                                                        </div>
                                                        {{-- بدون name حتى لا يتعارض مع حقل request_type_id في النموذج الرئيسي --}}
                                                        <input type="hidden" id="new-request-type-id">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">الجهة المسؤولة (دائرة / قسم)
                                                                <span class="text-danger">*</span></label>
                                                            <select id="new-workflow-unit"
                                                                class="form-control form-control-solid">
                                                                <option value="">اختر دائرة أو قسم</option>
                                                                <optgroup label="الدوائر">
                                                                    @foreach ($departments as $department)
                                                                        <option value="department:{{ $department->id }}">
                                                                            {{ $department->name }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                                <optgroup label="الأقسام">
                                                                    @foreach ($sections as $section)
                                                                        <option value="section:{{ $section->id }}">
                                                                            {{ $section->name }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            </select>
                                                            <div class="text-danger mt-1" id="workflow-unit-error"></div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">ترتيب الخطوة <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" id="new-workflow-step-order"
                                                                class="form-control form-control-solid" min="2"
                                                                value="2">
                                                            <div class="text-danger mt-1" id="workflow-step-order-error">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <label class="font-weight-bold">المسار السابق <span
                                                                    class="text-muted font-weight-normal">(اختياري)</span></label>
                                                            <select id="new-workflow-previous"
                                                                class="form-control form-control-solid">
                                                                <option value="">بدون مسار سابق</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-warning font-weight-bold"
                                                            data-dismiss="modal">إنهاء</button>
                                                        <button type="button" class="btn btn-primary font-weight-bold"
                                                            id="save-request-workflow">حفظ المسار</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                            <div class="form-group">
                                <div class="checkbox-list">
                                    <label class="checkbox checkbox-outline">
                                        <input type="checkbox" id="join_" name="join" value="1"
                                            {{ old('join') ? 'checked' : '' }} />
                                        <span></span>
                                        طلب مشترك
                                    </label>
                                </div>
                            </div>

                            <div class="form-group @if (!old('join')) d-none @endif" id="common_name">
                                <label class="d-block d-md-inline-block font-weight-bold">أسماء مقدمي الطلب</label>
                                <input type="text" class="form-control @error('common_name') is-invalid @enderror"
                                    name="common_name" value="{{ old('common_name') }}"
                                    placeholder="أدخل أسماء المقدمين" />
                                @error('common_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="d-block d-md-inline-block">نص الطلب:</label>
                                <input type="text" name="text" id="request_text"
                                    class="form-control form-control-solid form-control-lg @error('text') is-invalid @enderror"
                                    value="{{ old('text') }}" placeholder="أدخل نص الطلب" required />
                                @error('text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="d-block d-md-inline-block">المرفقات (اختياري):</label>
                                <input type="file" name="attachments[]" id="request_attachments"
                                    class="form-control form-control-solid" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.zip,.rar" />
                                <span class="form-text text-muted">يمكنك اختيار عدة ملفات. الصيغ المسموحة: PDF, Word,
                                    Excel, صور (jpg, png, gif, ...), نص, أرشيف (zip, rar)</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="save_req" class="btn btn-primary mr-2">حفظ</button>
                            <a href="{{ route('requests.index') }}" class="btn btn-warning">إلغاء</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card card-custom request-address-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                عنوان المكلف بالتفصيل
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">الموبايل</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile"
                                            value="{{ old('mobile') }}" placeholder="الموبايل" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">حساب الجباية</label>
                                        <input type="text" class="form-control" id="jebaia_no" name="jebaia_no"
                                            value="{{ old('jebaia_no') }}" placeholder="حساب الجباية" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="d-block d-md-inline-block">الحي <span
                                        class="text-muted font-weight-normal">(اختياري)</span></label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood"
                                    value="{{ old('neighborhood') }}" placeholder="الحي" />
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">القطعة <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="widget" name="widget"
                                            value="{{ old('widget') }}" placeholder="القطعة" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">القسيمة <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="voucher" name="voucher"
                                            value="{{ old('voucher') }}" placeholder="القسيمة" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">المقسم <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="divider" name="divider"
                                            value="{{ old('divider') }}" placeholder="المقسم" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">البناية <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="building" name="building"
                                            value="{{ old('building') }}" placeholder="البناية" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">الطابق <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="{{ old('floor') }}" placeholder="الطابق" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">الشقة <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="apartment" name="apartment"
                                            value="{{ old('apartment') }}" placeholder="الشقة" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">المالك <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="owner" name="owner"
                                            value="{{ old('owner') }}" placeholder="المالك" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label class="d-block d-md-inline-block">المستأجر <span
                                                class="text-muted font-weight-normal">(اختياري)</span></label>
                                        <input type="text" class="form-control" id="tenant" name="tenant"
                                            value="{{ old('tenant') }}" placeholder="المستأجر" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('requests.partials.fee_amount_modal', [
            'prefix' => '',
            'feeModalId' => 'requestFeeAmountModal',
        ])
    </div>
@endsection

@section('styles')
    <link href="{{ asset('assets/plugins/custom/select2/css/select2.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .request-create-page {
            padding-bottom: 0.75rem;
        }

        .request-create-page>.row,
        .request-create-page>form>.row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .request-create-page>.row>[class*="col-"],
        .request-create-page>form>.row>[class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .request-search-card {
            margin-bottom: 0.75rem !important;
        }

        .request-search-card .card-body {
            padding: 0.75rem 1rem;
        }

        .request-search-card .col-md-4 {
            margin-bottom: 0 !important;
        }

        .request-search-card label,
        .request-main-card label,
        .request-address-card label {
            font-size: 0.8rem;
            line-height: 1.2;
        }

        .request-create-page .form-control,
        .request-create-page .select2-container--default .select2-selection--single {
            min-height: 35px;
            height: 35px;
            padding-top: 0.35rem;
            padding-bottom: 0.35rem;
        }

        .request-create-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 27px;
        }

        .request-main-card,
        .request-address-card {
            height: calc(100% - 0.75rem);
            margin-bottom: 0.75rem !important;
        }

        .request-main-card .card-body,
        .request-address-card .card-body {
            padding: 1rem;
        }

        .request-main-card .form-group,
        .request-address-card .form-group {
            margin-bottom: 0.6rem;
        }

        .request-main-card .row,
        .request-address-card .row {
            margin-left: -0.35rem;
            margin-right: -0.35rem;
        }

        .request-main-card .row>[class*="col-"],
        .request-address-card .row>[class*="col-"] {
            padding-left: 0.35rem;
            padding-right: 0.35rem;
        }

        .request-address-card .card-header {
            min-height: 48px;
            padding: 0.75rem 1rem;
        }

        .request-address-card .card-title {
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .request-address-card .card-body>.form-group,
        .request-address-card .card-body>.row {
            margin-bottom: 0.35rem;
        }

        .request-main-card .card-footer {
            padding: 0.75rem 1rem;
        }

        @media (min-width: 992px) {
            .request-create-page>form>.row {
                align-items: stretch;
            }

            .request-main-card .card-body,
            .request-address-card .card-body {
                padding: 0.85rem 1rem;
            }

            .request-main-card .form-control,
            .request-address-card .form-control {
                font-size: 0.8rem;
            }

            .request-main-card .form-text {
                font-size: 0.68rem;
                white-space: nowrap;
            }
        }

        @media (max-width: 767.98px) {
            .request-create-page {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .request-search-card .col-md-4 {
                margin-bottom: 0.5rem !important;
            }

            .request-main-card,
            .request-address-card {
                height: auto;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}" type="text/javascript"></script>
    @include('requests._mokalaf_select2_js')
    @include('requests.partials.fee_amount_modal_script')
    @include('requests.partials.form_enhancements_script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var requestTypeStoreUrl = '{{ route('request-types.store') }}';
            var workflowStoreUrl = '{{ route('request-workflows.store') }}';

            $('#save-request-type').on('click', function(e) {
                e.preventDefault();

                var $button = $('#save-request-type');
                var $form = $('#create-request-type-form');
                $('#request-type-name-error').empty();
                $button.prop('disabled', true).text('جاري الحفظ...');

                $.ajax({
                    url: requestTypeStoreUrl,
                    type: 'POST',
                    data: $form.find(':input').serialize(),
                    success: function(response) {
                        var requestType = response.data;
                        var initialWorkflow = requestType.workflow;
                        var option = new Option(requestType.name, requestType.id, true, true);

                        $('#kt_select2_4').append(option).trigger('change');
                        $('#new-request-type-id').val(requestType.id);
                        $('#new-workflow-previous').html('<option value="' + initialWorkflow
                            .id + '">' + initialWorkflow.name + ' (مسار أولي)</option>');
                        $('#new-workflow-previous').prepend(
                            '<option value="">بدون مسار سابق</option>').val('');
                        $('#create-request-type-modal').modal('hide');
                        $form.find('input[name="name"], textarea[name="description"]').val('');
                        $button.prop('disabled', false).text('حفظ ومتابعة المسارات');

                        @can('store request_workflows')
                            $('#create-request-workflow-modal').modal('show');
                        @else
                            showRequestTypeSavedMessage();
                        @endcan
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON && xhr.responseJSON.errors;
                        $('#request-type-name-error').text(errors && errors.name ? errors.name[
                            0] : (xhr.responseJSON ? xhr.responseJSON.message :
                            'حدث خطأ أثناء الحفظ'));
                        $button.prop('disabled', false).text('حفظ ومتابعة المسارات');
                    }
                });
            });

            $('#save-request-workflow').on('click', function(e) {
                e.preventDefault();

                var $button = $('#save-request-workflow');
                $('#workflow-unit-error, #workflow-step-order-error').empty();

                var requestTypeId = $('#new-request-type-id').val();
                var unit = $('#new-workflow-unit').val();

                if (!requestTypeId) {
                    $('#workflow-unit-error').text('يرجى اختيار نوع الطلب أولاً');
                    return;
                }

                if (!unit) {
                    $('#workflow-unit-error').text('يرجى اختيار دائرة أو قسم');
                    $('#new-workflow-unit').focus();
                    return;
                }

                var stepOrder = parseInt($('#new-workflow-step-order').val(), 10);
                if (!stepOrder || stepOrder < 1) {
                    $('#workflow-step-order-error').text('يرجى إدخال ترتيب صحيح (أكبر من 0)');
                    return;
                }

                var unitParts = unit.split(':');
                var formData = {
                    request_type_id: requestTypeId,
                    unit_type: unitParts[0],
                    unit_id: unitParts[1],
                    step_order: stepOrder,
                    previous_workflow_id: $('#new-workflow-previous').val() || null
                };

                $button.prop('disabled', true).text('جاري الحفظ...');

                $.ajax({
                    url: workflowStoreUrl,
                    type: 'POST',
                    data: formData,
                    success: function() {
                        $('#create-request-workflow-modal').modal('hide');
                        $('#new-workflow-unit').val('');
                        $('#new-workflow-step-order').val('2');
                        $('#new-workflow-previous').html(
                            '<option value="">بدون مسار سابق</option>');
                        $button.prop('disabled', false).text('حفظ المسار');
                        showRequestTypeSavedMessage('تم إضافة نوع الطلب والمسار بنجاح');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON && xhr.responseJSON.errors;
                        var unitError = errors && (errors.unit_id || errors.unit_type);
                        $('#workflow-unit-error').text(unitError ? unitError[0] : '');
                        $('#workflow-step-order-error').text(errors && errors.step_order ?
                            errors.step_order[0] : '');
                        if (!errors && xhr.responseJSON && xhr.responseJSON.message) {
                            showRequestTypeSavedMessage(xhr.responseJSON.message, 'error');
                        }
                        $button.prop('disabled', false).text('حفظ المسار');
                    }
                });
            });

            function showRequestTypeSavedMessage(message, icon) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: icon || 'success',
                        title: message || 'تم إضافة نوع الطلب بنجاح',
                        confirmButtonText: 'حسنًا'
                    });
                }
            }

            var clickcount = 0;
            $('#save_req').on('click', function(e) {
                clickcount++;
                if (clickcount === 1) {
                    var mokalafId = $('#kt_select2_3').val();
                    if (mokalafId) {
                        if ($('#mokalaf_id_hidden').length === 0) {
                            $('<input>').attr({
                                type: 'hidden',
                                id: 'mokalaf_id_hidden',
                                name: 'mokalaf_id',
                                value: mokalafId
                            }).appendTo('#kt_form_2');
                        } else {
                            $('#mokalaf_id_hidden').val(mokalafId);
                        }
                    }

                    $('#kt_select2_3').prop('disabled', false).css({
                        'pointer-events': '',
                        'opacity': ''
                    });
                    $('#address').prop('disabled', false);

                    $('#kt_form_2').submit();
                }
            });

            $('input').on("keypress", function(e) {
                if (e.keyCode == 13) {
                    var inputs = $(this).parents("form").eq(0).find(":input");
                    var idx = inputs.index(this);
                    if (idx == inputs.length - 1) {
                        inputs[0].select();
                    } else {
                        inputs[idx + 1].focus();
                        inputs[idx + 1].select();
                    }
                    return false;
                }
            });

            $('#join_').change(function() {
                if ($(this).is(":checked")) {
                    $('#common_name').removeClass('d-none');
                } else {
                    $('#common_name').addClass('d-none');
                }
            });

            window.initMokalafSelect2({
                prefix: '',
                formId: 'kt_form_2',
                readOnlyMainSelect: true,
                enableQueryPrefill: true
            });

            window.initRequestFormEnhancements({
                prefix: '',
                formId: 'kt_form_2',
                draftKey: 'request_create_page_draft'
            });

            @if (session('success'))
                setTimeout(function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجح!',
                            text: '{{ session('success') }}',
                            confirmButtonText: 'حسناً',
                            timer: 3000,
                            showConfirmButton: true
                        }).then(function() {
                            window.location.href = '{{ route('requests.index') }}';
                        });
                    } else {
                        window.location.href = '{{ route('requests.index') }}';
                    }
                }, 100);
            @endif
        });
    </script>
@endsection
