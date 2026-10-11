@extends('layout.master')

@section('title')
    {{ __('الطلبات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الطلبات') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            :title="__('الطلبات')"
            :subtitle="__('إدارة وتصفح سجلات الطلبات')"
            icon="fa-file-lines"
        >
            <x-slot name="actions">
                <a href="#" class="btn btn-light-success font-weight-bolder" data-toggle="collapse"
                    data-target="#collapseSearch">
                    <i class="fas fa-search"></i> {{ __('بحث') }}
                </a>
                @can('store requests')
                    <a href="#" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#addRequestModal">
                        <i class="fas fa-plus"></i> {{ __('إضافة جديد') }}
                    </a>
                @endcan
            </x-slot>
        </x-page-header>

        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body">
                @php
                    $requestStats = $requestStats ?? ['total' => 0, 'urgent' => 0, 'overdue' => 0, 'due_soon' => 0, 'pending' => 0];
                    $monitor = request('monitor');
                @endphp
                <div class="row mb-7">
                    <div class="col-xl col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('requests.index') }}" class="d-block bg-light-primary rounded p-5 text-decoration-none h-100 {{ $monitor ? '' : 'border border-primary' }}">
                            <span class="text-muted font-weight-bold d-block mb-2">{{ __('إجمالي الطلبات') }}</span>
                            <span class="font-size-h3 font-weight-bolder text-primary">{{ $requestStats['total'] }}</span>
                        </a>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('requests.index', ['is_urgent' => 1]) }}" class="d-block bg-light-danger rounded p-5 text-decoration-none h-100 {{ request('is_urgent') === '1' ? 'border border-danger' : '' }}">
                            <span class="text-muted font-weight-bold d-block mb-2">{{ __('طلبات عاجلة') }}</span>
                            <span class="font-size-h3 font-weight-bolder text-danger">{{ $requestStats['urgent'] }}</span>
                        </a>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('requests.index', ['monitor' => 'overdue']) }}" class="d-block bg-light-warning rounded p-5 text-decoration-none h-100 {{ $monitor === 'overdue' ? 'border border-warning' : '' }}">
                            <span class="text-muted font-weight-bold d-block mb-2">{{ __('طلبات متأخرة') }}</span>
                            <span class="font-size-h3 font-weight-bolder text-warning">{{ $requestStats['overdue'] }}</span>
                        </a>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('requests.index', ['monitor' => 'due_soon']) }}" class="d-block bg-light-info rounded p-5 text-decoration-none h-100 {{ $monitor === 'due_soon' ? 'border border-info' : '' }}">
                            <span class="text-muted font-weight-bold d-block mb-2">{{ __('مواعيد قريبة') }}</span>
                            <span class="font-size-h3 font-weight-bolder text-info">{{ $requestStats['due_soon'] }}</span>
                        </a>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('requests.index', ['monitor' => 'pending']) }}" class="d-block bg-light-success rounded p-5 text-decoration-none h-100 {{ $monitor === 'pending' ? 'border border-success' : '' }}">
                            <span class="text-muted font-weight-bold d-block mb-2">{{ __('قيد الانتظار') }}</span>
                            <span class="font-size-h3 font-weight-bolder text-success">{{ $requestStats['pending'] }}</span>
                        </a>
                    </div>
                </div>
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('request_type_id') || request()->filled('status_id') || request()->filled('mokalaf_id') || request()->filled('monitor') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form">
                            @if(request()->filled('monitor'))
                                <input type="hidden" name="monitor" value="{{ request('monitor') }}">
                            @endif
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('البحث') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('بحث شامل: رقم الطلب، النص، المكلف، الهوية، الترخيص، الجوال') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('نوع الطلب') }}</label>
                                    <select name="request_type_id" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        @foreach($requestTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('request_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }}</label>
                                    <select name="status_id" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        @foreach($requestStatuses as $status)
                                            <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('المكلف') }}</label>
                                    <select name="mokalaf_id" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        @foreach($mokalafs as $mokalaf)
                                            <option value="{{ $mokalaf->id }}" {{ request('mokalaf_id') == $mokalaf->id ? 'selected' : '' }}>
                                                {{ $mokalaf->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('عاجل') }}</label>
                                    <select name="is_urgent" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="1" {{ request('is_urgent') == '1' ? 'selected' : '' }}>{{ __('نعم') }}</option>
                                        <option value="0" {{ request('is_urgent') == '0' ? 'selected' : '' }}>{{ __('لا') }}</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('من تاريخ') }}</label>
                                    <input name="from_date" type="date" class="form-control form-control-solid"
                                        value="{{ request('from_date') }}" />
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('إلى تاريخ') }}</label>
                                    <input name="to_date" type="date" class="form-control form-control-solid"
                                        value="{{ request('to_date') }}" />
                                </div>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark opacity-0">{{ __('إجراءات') }}</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </button>
                                        <a href="{{ route('requests.index') }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-rotate-right"></i> {{ __('إعادة تعيين') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr>
                                <th>{{ __('رقم الطلب') }}</th>
                                <th>{{ __('نوع الطلب') }}</th>
                                <th>{{ __('المكلف') }}</th>
                                <th>{{ __('الحالة / الأهمية') }}</th>
                                <th>{{ __('الموعد النهائي') }}</th>
                                <th>{{ __('تاريخ الإنشاء') }}</th>
                                <th>{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <span class="badge badge-primary">{{ $request->id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $request->requestType->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($request->mokalaf)
                                            @can('view', $request->mokalaf)
                                                <a href="{{ route('mokalafs.show', $request->mokalaf) }}" class="text-dark-75 font-weight-bolder text-hover-primary">
                                                    {{ $request->mokalaf->name }}
                                                </a>
                                            @else
                                                <span class="text-dark-75 font-weight-bolder">{{ $request->mokalaf->name }}</span>
                                            @endcan
                                            <span class="text-muted d-block font-size-sm">
                                                {{ $request->mokalaf->identity_number ?: $request->mokalaf->license_number ?: '-' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div>
                                                <span class="badge badge-{{ $request->status->color ?? 'secondary' }}">
                                                    {{ $request->status->name ?? '-' }}
                                                </span>
                                                @if($request->is_urgent)
                                                    <span class="badge badge-danger ml-1 mt-1 mt-md-0">{{ __('عاجل') }}</span>
                                                @else
                                                    <span class="badge badge-success ml-1 mt-1 mt-md-0">{{ __('عادي') }}</span>
                                                @endif
                                            </div>
                                            @if($request->status_reason_sections_text)
                                                <span class="d-block text-muted font-size-sm mt-2">{{ $request->status_reason_sections_text }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($request->deadline)
                                            <span>{{ $request->deadline->format('d-m-Y') }}</span>
                                            @if($request->deadline->isPast() && optional($request->status)->color !== 'success')
                                                <span class="badge badge-warning d-block mt-2">{{ __('متأخر') }}</span>
                                            @elseif($request->deadline->isBetween(now(), now()->addDays(7)))
                                                <span class="badge badge-info d-block mt-2">{{ __('قريب') }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light-primary dropdown-toggle px-4 py-2"
                                                    type="button"
                                                    id="requestActionsDropdown{{ $request->id }}"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="fas fa-ellipsis-vertical mr-1"></i>
                                                <span class="d-none d-md-inline">{{ __('إجراءات') }}</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="requestActionsDropdown{{ $request->id }}">
                                                @can('update requests')
                                                    <a href="#" class="dropdown-item js-open-edit-request" data-url="{{ route('requests.edit', $request) }}">
                                                        <i class="fas fa-pen-to-square text-primary mr-2"></i> {{ __('تعديل') }}
                                                    </a>
                                                @endcan
                                                <a href="{{ route('requests.show', $request) }}" class="dropdown-item">
                                                    <i class="fas fa-eye text-info mr-2"></i> {{ __('عرض التفاصيل') }}
                                                </a>
                                                <a href="{{ route('requests.print', $request) }}" class="dropdown-item" target="_blank">
                                                    <i class="fas fa-print text-success mr-2"></i> {{ __('طباعة الطلب') }}
                                                </a>
                                                @can('delete requests')
                                                    <a href="javascript:;" class="dropdown-item delete-request-btn" data-id="{{ $request->id }}" data-url="{{ route('requests.destroy', $request) }}">
                                                        <i class="fas fa-trash text-danger mr-2"></i> {{ __('حذف') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-10">
                                        <x-empty-state
                                            icon="fa-inbox"
                                            :title="__('لا توجد بيانات')"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">{{ __('عرض') }} {{ $requests->firstItem() }} {{ __('إلى') }} {{ $requests->lastItem() }} {{ __('من') }} {{ $requests->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {{-- {{ $requests->links() }} --}}
                         {!! $requests->withQueryString()->links('pagination::bootstrap-5') !!}

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إضافة طلب جديد (نفس صفحة الإنشاء بكامل تفاصيلها) --}}
    @can('store requests')
    <div class="modal fade add-request-modal" id="addRequestModal" tabindex="-1" role="dialog" aria-labelledby="addRequestModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-success border-0 py-5">
                    <h5 class="modal-title font-weight-bolder text-light d-flex align-items-center" id="addRequestModalLabel">
                        <i class="fas fa-plus icon-lg text-success ml-5 mr-5"></i>
                        <span>{{ __('إضافة طلب جديد') }}</span>
                    </h5>
                    <button type="button" class="close ml-auto mr-0" data-dismiss="modal" aria-label="{{ __('إغلاق') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6 add-request-modal-body">
                    @include('requests._create_form_full', ['prefix' => 'modal_', 'requestTypes' => $requestTypes])
                </div>
                <div class="modal-footer border-top bg-light flex-wrap py-4">
                    <button type="button" class="btn btn-light-warning font-weight-bold order-2 order-md-1" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('إلغاء') }}
                    </button>
                    <!-- <a href="{{ route('requests.create') }}" class="btn btn-light-primary font-weight-bold order-1 order-md-2 ml-auto mr-2" target="_blank">
                        <i class="fas fa-book-open"></i> {{ __('إنشاء في صفحة كاملة') }}
                    </a> -->
                    <button type="button" class="btn btn-primary font-weight-bold order-3" id="addRequestSubmitBtn">
                        <i class="fas fa-check"></i> {{ __('حفظ') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('update requests')
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-primary border-0 py-5">
                    <h5 class="modal-title font-weight-bolder text-dark d-flex align-items-center" id="editRequestModalLabel">
                        <i class="fas fa-pen-to-square icon-lg text-primary ml-2"></i>
                        <span>{{ __('تعديل الطلب') }}</span>
                    </h5>
                    <button type="button" class="close ml-auto mr-0" data-dismiss="modal" aria-label="{{ __('إغلاق') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6" id="editRequestModalBody">
                    <div class="text-center py-10 text-muted">{{ __('جاري التحميل...') }}</div>
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('fab')
    @can('store requests')
        <a href="{{ route('requests.create') }}" class="fab-button" aria-label="{{ __('إضافة طلب جديد') }}">
            <i class="fa-solid fa-plus"></i>
        </a>
    @endcan
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/custom/select2/js/select2.full.min.js') }}"></script>
@include('requests._mokalaf_select2_js')
@include('requests.partials.fee_amount_modal_script')
@include('requests.partials.edit_request_interactions_script')
@include('requests.partials.form_enhancements_script')
<script>
    $(document).ready(function() {
        var p = 'modal_';
        $('#addRequestModal').on('show.bs.modal', function () {
            window.initMokalafSelect2({
                prefix: p,
                formId: p + 'kt_form_2',
                readOnlyMainSelect: true,
                enableQueryPrefill: false
            });
            window.initRequestFormEnhancements({
                prefix: p,
                formId: p + 'kt_form_2',
                draftKey: 'request_create_modal_draft'
            });

            // تهيئة قائمة نوع الطلب داخل المودال (Select2) مع ضبط الـ dropdown داخل المودال نفسه
            var $requestTypeSelect = $('#' + p + 'kt_select2_4');
            if ($requestTypeSelect.length && !$requestTypeSelect.hasClass('select2-hidden-accessible')) {
                $requestTypeSelect.select2({
                    dropdownParent: $('#addRequestModal'),
                    placeholder: '{{ __("اختر نوع الطلب") }}',
                    width: '100%'
                });
            }
        }).on('hidden.bs.modal', function () {
            var form = $('#' + p + 'kt_form_2')[0];
            if (form) form.reset();
            $('#' + p + 'mokalaf_id_hidden').remove();
            $('#' + p + 'kt_select2_111').val(null).trigger('change');
            $('#' + p + 'kt_select2_222').val(null).trigger('change');
            $('#' + p + 'kt_select2_333').val(null).trigger('change');
            $('#' + p + 'kt_select2_3').val(null).trigger('change').css({'pointer-events': '', 'opacity': ''});
            $('#' + p + 'common_name').addClass('d-none');
            $('#' + p + 'kt_form_2 .invalid-feedback').text('');
            $('#' + p + 'request_fee_amount_hidden').val('');
            var $feeSummary = $('#' + p + 'request_fee_amount_summary');
            if ($feeSummary.length) {
                $feeSummary.text($feeSummary.data('empty-label') || '');
            }
            $('#' + p + 'requestFeeAmountModal_fee_input').val('');
            $('#' + p + 'join_').prop('checked', false);
            $('#' + p + 'common_name').addClass('d-none');
            $('#' + p + 'common_name_input').val('');
            $('#' + p + 'request_attachments_preview').empty();
        });

        $(document).on('change', '#' + p + 'join_', function () {
            if ($(this).is(':checked')) {
                $('#' + p + 'common_name').removeClass('d-none');
            } else {
                $('#' + p + 'common_name').addClass('d-none');
            }
        });

        $('#addRequestSubmitBtn').on('click', function () {
            var form = $('#' + p + 'kt_form_2')[0];
            if (!form || !form.checkValidity()) {
                if (form) form.reportValidity();
                return;
            }
            var mokalafId = $('#' + p + 'kt_select2_3').val();
            if (mokalafId && $('#' + p + 'mokalaf_id_hidden').length === 0) {
                $('<input>').attr({ type: 'hidden', id: p + 'mokalaf_id_hidden', name: 'mokalaf_id', value: mokalafId }).appendTo('#' + p + 'kt_form_2');
            } else if (mokalafId) {
                $('#' + p + 'mokalaf_id_hidden').val(mokalafId);
            }
            $('#' + p + 'kt_select2_3').prop('disabled', false).css({'pointer-events': '', 'opacity': ''});
            $('#' + p + 'address').prop('disabled', false);
            var btn = $(this);
            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');
            $('#' + p + 'kt_form_2 .invalid-feedback').text('');
            var formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.set('join', $('#' + p + 'join_').is(':checked') ? '1' : '0');
            $.ajax({
                url: '{{ route("requests.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (res) {
                    if (res.success) {
                        localStorage.removeItem('request_create_modal_draft');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'success', title: '{{ __("تم") }}', text: res.message || '{{ __("تم إضافة الطلب بنجاح") }}', confirmButtonText: '{{ __("حسناً") }}' })
                                .then(function () { $('#addRequestModal').modal('hide'); location.reload(); });
                        } else {
                            $('#addRequestModal').modal('hide');
                            location.reload();
                        }
                    } else {
                        btn.prop('disabled', false).html(originalHtml);
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: res.message || '' });
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __("حدث خطأ أثناء إضافة الطلب") }}';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, arr) {
                            var id = p + key.replace('.', '_') + '_error';
                            if ($('#' + id).length) $('#' + id).text(Array.isArray(arr) ? arr[0] : arr);
                        });
                    }
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
                }
            });
        });

        console.log('Delete script loaded');
        console.log('Found delete buttons:', $('.delete-request-btn').length);

        // Use event delegation to handle dynamically loaded content
        $(document).on('click', '.delete-request-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Delete button clicked');
            var requestId = $(this).data('id');
            var deleteUrl = $(this).data('url');

            console.log('Request ID:', requestId);
            console.log('Delete URL:', deleteUrl);

            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded');
                if (confirm('{{ __('هل أنت متأكد من حذف هذا الطلب؟') }}')) {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': deleteUrl,
                        'style': 'display: none;'
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': $('meta[name="csrf-token"]').attr('content')
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));

                    $('body').append(form);
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف الطلب بشكل نهائي ولا يمكن التراجع عن هذا الإجراء!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('User confirmed deletion');

                    // Show loading
                    Swal.fire({
                        title: 'جاري الحذف...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create form dynamically
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': deleteUrl,
                        'style': 'display: none;'
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': $('meta[name="csrf-token"]').attr('content')
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));

                    $('body').append(form);
                    console.log('Submitting form to:', deleteUrl);
                    form.submit();
                } else {
                    console.log('User cancelled deletion');
                }
            });
        });

        @can('update requests')
        function removeEditModalReparentedSubmodals() {
            $('body > .modal').each(function () {
                var id = this.id || '';
                if (id.indexOf('erm_') === 0) {
                    $(this).remove();
                }
            });
        }

        function loadEditRequestModalContent(url) {
            if (!url) {
                return;
            }
            removeEditModalReparentedSubmodals();
            $('#editRequestModalBody').html('<div class="text-center py-10 text-muted"><span class="spinner-border spinner-border-sm mr-2" role="status"></span>{{ __('جاري التحميل...') }}</div>');
            $.ajax({
                url: url,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (html) {
                    $('#editRequestModalBody').html(html);
                    bindRequestEditInteractions($('#editRequestModalBody'));
                    if (typeof window.syncFeeAmountSummaries === 'function') {
                        window.syncFeeAmountSummaries();
                    }
                },
                error: function () {
                    $('#editRequestModalBody').html('<div class="alert alert-danger mb-0">{{ __('تعذر تحميل نموذج التعديل. حاول مرة أخرى أو افتح الصفحة كاملة.') }}</div>');
                }
            });
        }

        window.reloadEditRequestModal = function () {
            var url = $('#editRequestModal').data('edit-url');
            if (!url || !$('#editRequestModal').hasClass('show')) {
                window.location.reload();
                return;
            }
            loadEditRequestModalContent(url);
        };

        $(document).on('click', '.js-open-edit-request', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            if (!url) {
                return;
            }
            $('#editRequestModal').data('edit-url', url);
            $('#editRequestModal').modal('show');
            loadEditRequestModalContent(url);
        });

        $('#editRequestModal').on('hidden.bs.modal', function () {
            $('#editRequestModalBody').empty();
            $('#editRequestModal').removeData('edit-url');
            removeEditModalReparentedSubmodals();
        });

        $(document).on('submit', '#editRequestModal .request-edit-form', function (e) {
            e.preventDefault();
            var form = this;
            var $form = $(form);
            var $btn = $form.find('[type="submit"]');
            var originalHtml = $btn.length ? $btn.html() : '';
            if ($btn.length) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');
            }
            var fd = new FormData(form);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                success: function (res) {
                    if (res.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'success', title: '{{ __("تم") }}', text: res.message || '{{ __("تم تحديث الطلب بنجاح") }}', confirmButtonText: '{{ __("حسناً") }}' })
                                .then(function () { $('#editRequestModal').modal('hide'); location.reload(); });
                        } else {
                            $('#editRequestModal').modal('hide');
                            location.reload();
                        }
                    } else if ($btn.length) {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function (xhr) {
                    if ($btn.length) {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                    var msg = '{{ __("حدث خطأ أثناء تحديث الطلب") }}';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errs = xhr.responseJSON.errors;
                        var parts = [];
                        Object.keys(errs).forEach(function (k) {
                            var v = errs[k];
                            if (Array.isArray(v)) {
                                parts = parts.concat(v);
                            } else if (v) {
                                parts.push(v);
                            }
                        });
                        if (parts.length) {
                            msg = parts.join('\n');
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        });
        @endcan
    });
</script>
@endsection
