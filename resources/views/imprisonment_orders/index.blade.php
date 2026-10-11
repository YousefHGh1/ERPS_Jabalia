@extends('layout.master')

@section('title')
    {{ __('أوامر الحبس') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('أوامر الحبس') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('أوامر الحبس') }}"
                subtitle="{{ __('إدارة وتصفح سجلات أوامر الحبس') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        @can('store imprisonment_orders')
                                            <a href="#" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#addImprisonmentOrderModal">
                                                <i class="fas fa-plus"></i> {{ __('إضافة جديد') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('status') || request()->filled('from_date') || request()->filled('to_date') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('البحث') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('رقم الأمر، المحكمة، القاضي، اسم المتهم، رقم الهوية') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }}</label>
                                    <select name="status" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('نشط') }}</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
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
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-md-6 mt-8">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8 py-3">
                                        <i class="fas fa-search"></i> {{ __('بحث') }}
                                    </button>
                                    <a href="{{ route('imprisonment_orders.index') }}" class="btn btn-light-primary font-weight-bolder px-8 py-3 ml-2">
                                        <i class="fas fa-sync-alt"></i> {{ __('إعادة تعيين') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-hover">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th class="pl-0" style="min-width: 120px">{{ __('رقم الأمر') }}</th>
                                <th style="min-width: 100px">{{ __('الإخطار') }}</th>
                                <th style="min-width: 110px">{{ __('تاريخ الأمر') }}</th>
                                <th style="min-width: 180px">{{ __('المتهم / رقم الهوية') }}</th>
                                <th style="min-width: 90px">{{ __('الحالة') }}</th>
                                <th class="text-right pr-0" style="min-width: 120px">{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr data-order-id="{{ $order->id }}">
                                    <td class="pl-0">
                                        <span class="badge badge-primary">{{ $order->order_number }}</span>
                                    </td>
                                    <td>{{ $order->notice_id ? ($order->notice->notice_number ?? '—') : '—' }}</td>
                                    <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td>
                                        @if($order->defendant_name || $order->defendant_identity)
                                            <div class="d-flex flex-column">
                                                @if($order->defendant_name)
                                                    <span class="font-weight-bold text-dark">{{ $order->defendant_name }}</span>
                                                @endif
                                                @if($order->defendant_identity)
                                                    <span class="text-muted font-size-sm">{{ $order->defendant_identity }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{!! $order->status_badge !!}</td>
                                    <td class="text-right pr-0">
                                        @can('update imprisonment_orders')
                                            <a href="javascript:;" data-toggle="modal" data-target="#editImprisonmentOrderModal-{{ $order->id }}" class="btn btn-icon btn-light-primary btn-sm mx-1" title="{{ __('تعديل') }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('delete imprisonment_orders')
                                            <a href="javascript:;" class="btn btn-icon btn-light-danger btn-sm mx-1 delete-order" data-id="{{ $order->id }}" title="{{ __('حذف') }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="symbol symbol-100 symbol-light-primary mb-3">
                                            <span class="symbol-label"><i class="fas fa-box-open icon-4x text-primary"></i></span>
                                        </div>
                                        <span class="text-muted font-weight-bold">{{ __('لا توجد بيانات') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">{{ __('عرض') }} {{ $orders->firstItem() }} {{ __('إلى') }} {{ $orders->lastItem() }} {{ __('من') }} {{ $orders->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودالات تعديل أوامر الحبس --}}
    @can('update imprisonment_orders')
        @foreach($orders as $order)
            <div class="modal fade" id="editImprisonmentOrderModal-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="editImprisonmentOrderModalLabel-{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bolder" id="editImprisonmentOrderModalLabel-{{ $order->id }}">{{ __('تعديل أمر الحبس') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body p-8">
                            @include('imprisonment_orders._edit_form', ['order' => $order])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan

    {{-- مودال إضافة أمر حبس (نفس حقول نموذج التعديل) --}}
    @can('store imprisonment_orders')
    <div class="modal fade" id="addImprisonmentOrderModal" tabindex="-1" role="dialog" aria-labelledby="addImprisonmentOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="addImprisonmentOrderForm" method="POST" action="{{ route('imprisonment_orders.store') }}" class="form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bolder" id="addImprisonmentOrderModalLabel">{{ __('إضافة أمر حبس جديد') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-8">
                        <div style="background:#f3f6f9;border-radius:8px;padding:15px;margin-bottom:10px;">
                            <h5 class="text-dark font-weight-bold mb-4" style="font-size:14px;">
                                {{ __('بيانات أمر الحبس الأساسية') }}
                            </h5>

                            {{-- الصف الأول: رقم الأمر + تاريخ الأمر --}}
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('رقم الأمر') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3">
                                    <input type="text"
                                           name="order_number"
                                           class="form-control"
                                           value="{{ old('order_number') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                    <span class="text-danger" id="order_number_error"></span>
                                </div>

                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('تاريخ الأمر') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3">
                                    <input type="date"
                                           name="order_date"
                                           class="form-control"
                                           value="{{ old('order_date') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                    <span class="text-danger" id="order_date_error"></span>
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
                                           value="{{ old('court_name') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                </div>

                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('القاضي') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3">
                                    <input type="text"
                                           name="judge_name"
                                           class="form-control"
                                           value="{{ old('judge_name') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                </div>
                            </div>

                            {{-- الصف الثالث: المتهم + رقم الهوية --}}
                            <div class="form-group row">
                                                            <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('رقم الهوية') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3">
                                    <input type="text"
                                           name="defendant_identity"
                                           class="form-control"
                                           value="{{ old('defendant_identity') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                </div>
                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('اسم المتهم') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-4">
                                    <input type="text"
                                           name="defendant_name"
                                           class="form-control"
                                           value="{{ old('defendant_name') }}"
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
                                           value="{{ old('imprisonment_period') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                </div>

                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('نوع الحبس') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3">
                                    <input type="text"
                                           name="imprisonment_type"
                                           class="form-control"
                                           value="{{ old('imprisonment_type') }}"
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
                                           value="{{ old('case_number') }}"
                                           style="font-size:13px;padding:6px 12px;">
                                </div>

                                <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size:12px;">
                                    {{ __('سنة القضية') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-2">
                                    <input type="number"
                                           name="case_year"
                                           class="form-control"
                                           value="{{ old('case_year', now()->year) }}"
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
                                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('نشط') }}</option>
                                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
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
                                              style="font-size:13px;padding:6px 12px;resize:vertical;">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-warning font-weight-bold px-8 py-3" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bold px-8 py-3" id="addImprisonmentOrderBtn">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

@endsection

@push('js')
<script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
<script src="{{ asset('assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
<script src="{{ asset('assets/js/pages/crud/forms/editors/ckeditor-classic.js') }}"></script>
<script>
    var orderNotesEditorInstance = null;
    $(document).ready(function() {
        $('.select2-notice').select2({
            placeholder: '{{ __('اختر الإخطار') }}',
            width: '100%'
        });
        $('#kt_datetimepicker_order').datetimepicker({
            defaultDate: '{{ now()->format('Y-m-d H:i:s') }}',
            format: 'YYYY-MM-DD HH:mm:ss',
            pick12HourFormat: false,
            use24hours: true
        });

        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.querySelector('#kt-ckeditor-order-notes'), {
                language: 'ar'
            }).then(function(editor) {
                orderNotesEditorInstance = editor;
            }).catch(function(err) {
                console.warn('CKEditor init:', err);
            });
        }

        $('#addImprisonmentOrderForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#addImprisonmentOrderBtn');
            if (orderNotesEditorInstance) {
                orderNotesEditorInstance.updateSourceElement();
            }
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');
            $('#addImprisonmentOrderModal .text-danger').text('');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.success) {
                        var message = response.message || '{{ __('تم إضافة أمر الحبس بنجاح') }}';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '{{ __('تم') }}',
                                text: message,
                                icon: 'success',
                                confirmButtonText: '{{ __('حسناً') }}'
                            }).then(function () {
                                $('#addImprisonmentOrderModal').modal('hide');
                                form[0].reset();
                                if (orderNotesEditorInstance) orderNotesEditorInstance.setData('');
                                location.reload();
                            });
                        } else {
                            $('#addImprisonmentOrderModal').modal('hide');
                            form[0].reset();
                            if (orderNotesEditorInstance) orderNotesEditorInstance.setData('');
                            location.reload();
                        }
                    } else {
                        btn.prop('disabled', false).html('{{ __('حفظ') }}');
                        var msg = response.message || '{{ __('حدث خطأ أثناء الحفظ') }}';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ title: '{{ __('خطأ') }}', text: msg, icon: 'error' });
                        } else {
                            alert(msg);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('{{ __('حفظ') }}');
                    if (xhr.status === 422 && xhr.responseJSON) {
                        var errors = xhr.responseJSON.errors || {};
                        $.each(errors, function(field, msg) {
                            var err = Array.isArray(msg) ? msg[0] : msg;
                            var id = field.replace(/\./g, '_') + '_error';
                            if ($('#' + id).length) $('#' + id).text(err);
                        });
                        if (xhr.responseJSON.message && typeof Swal !== 'undefined') {
                            Swal.fire({ title: 'تنبيه', text: xhr.responseJSON.message, icon: 'warning' });
                        }
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('حدث خطأ أثناء الحفظ') }}';
                        if (typeof Swal !== 'undefined') Swal.fire({ title: 'خطأ', text: msg, icon: 'error' }); else alert(msg);
                    }
                }
            });
        });
        $('#addImprisonmentOrderModal').on('hidden.bs.modal', function() {
            $('#addImprisonmentOrderForm')[0].reset();
            $('#addImprisonmentOrderModal .text-danger').text('');
            $('#addImprisonmentOrderBtn').prop('disabled', false).html('{{ __('حفظ') }}');
            if (orderNotesEditorInstance) orderNotesEditorInstance.setData('');
        });
        @if(session('openAddModal'))
        $('#addImprisonmentOrderModal').modal('show');
        @endif
        // فتح مودال الإضافة وتعبئة بيانات المتهم من معلمات الرابط
        (function openAndFillFromQuery() {
            var params = new URLSearchParams(window.location.search);
            var idVal = params.get('defendant_identity') || params.get('identity_number');
            var nameVal = params.get('defendant_name') || params.get('name');
            if (nameVal) try { nameVal = decodeURIComponent(nameVal); } catch (e) {}
            if (!idVal && !nameVal) return;
            var $modal = $('#addImprisonmentOrderModal');
            if (!$modal.length) return;
            function fillFields() {
                if (idVal) $modal.find('input[name="defendant_identity"]').val(idVal);
                if (nameVal) $modal.find('input[name="defendant_name"]').val(nameVal);
            }
            $modal.one('shown.bs.modal', fillFields);
            $modal.modal('show');
            setTimeout(fillFields, 400);
        })();

        // تقديم نموذج التعديل عبر AJAX
        $(document).on('submit', '.edit-imprisonment-order-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var orderId = form.data('order-id');
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري التحديث...') }}');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response && response.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '{{ __('تم') }}',
                                text: response.message || '{{ __('تم تحديث أمر الحبس بنجاح') }}',
                                icon: 'success',
                                confirmButtonText: '{{ __('حسناً') }}'
                            }).then(function () {
                                $('#editImprisonmentOrderModal-' + orderId).modal('hide');
                                location.reload();
                            });
                        } else {
                            $('#editImprisonmentOrderModal-' + orderId).modal('hide');
                            location.reload();
                        }
                    } else {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                        var msg = (response && response.message) ? response.message : '{{ __('حدث خطأ أثناء التحديث') }}';
                        if (typeof Swal !== 'undefined') Swal.fire({ title: '{{ __('خطأ') }}', text: msg, icon: 'error' }); else alert(msg);
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('حدث خطأ أثناء التحديث') }}';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var firstErr = Object.values(xhr.responseJSON.errors).flat()[0];
                        if (firstErr) msg = firstErr;
                    }
                    if (typeof Swal !== 'undefined') Swal.fire({ title: '{{ __('خطأ') }}', text: msg, icon: 'error' }); else alert(msg);
                }
            });
        });

        // تأكيد حذف أمر الحبس عبر SweetAlert
        $('.delete-order').click(function () {
            var id = $(this).data('id');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('هل أنت متأكد؟') }}',
                    text: '{{ __('سيتم حذف هذا الأمر نهائياً') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('نعم، احذف') }}',
                    cancelButtonText: '{{ __('إلغاء') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteOrderAjax(id);
                    }
                });
            } else {
                if (confirm('{{ __('هل أنت متأكد من حذف هذا الأمر؟') }}')) {
                    deleteOrderAjax(id);
                }
            }
        });

        function deleteOrderAjax(id) {
            $.ajax({
                url: '{{ route("imprisonment_orders.destroy", ["imprisonment_order" => "ORDER_ID_PLACEHOLDER"]) }}'.replace('ORDER_ID_PLACEHOLDER', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('تم الحذف بنجاح') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert('{{ __('تم الحذف بنجاح') }}');
                    }

                    // إزالة الصف من الجدول مباشرة بدون الاعتماد على إعادة تحميل الصفحة
                    $('tr[data-order-id="' + id + '"]').remove();
                },
                error: function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('حدث خطأ أثناء الحذف') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert('{{ __('حدث خطأ أثناء الحذف') }}');
                    }
                }
            });
        }

        // عند إدخال رقم الهوية إحضار اسم المتهم تلقائياً
        $(document).on('blur', 'input[name="defendant_identity"]', function () {
            var $idInput = $(this);
            var idValue = ($idInput.val() || '').trim();
            if (!idValue) {
                return;
            }

            var $nameInput = $idInput.closest('.form-group').parent().find('input[name="defendant_name"]').first();
            if ($nameInput.length === 0) {
                return;
            }

            $.ajax({
                url: '{{ route("getCzn") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    czn_id: idValue
                },
                success: function (data) {
                    if (data && !data.error) {
                        var full =
                            data.FULLNAME ||
                            data.full_name_ar ||
                            data.name_ar ||
                            data.name ||
                            '';
                        if (full) {
                            $nameInput.val(full);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '{{ __('تم') }}',
                                    text: '{{ __('تم جلب اسم المتهم بناءً على رقم الهوية') }}',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        }
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('تنبيه') }}',
                            text: '{{ __('لم يتم العثور على بيانات لهذا الرقم، يمكنك إدخال الاسم يدوياً') }}',
                            icon: 'info',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('تنبيه') }}',
                            text: '{{ __('لم يتم جلب البيانات، يمكنك إدخال الاسم يدوياً') }}',
                            icon: 'info',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
