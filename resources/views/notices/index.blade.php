@extends('layout.master')

@section('title')
    {{ __('الإخطارات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإخطارات') }}
@endsection

@push('css')
<style>
    /* تحسين مودالات الإخطارات */
    #addNoticeModal .modal-content,
    #editNoticeModal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    #addNoticeModal .modal-header,
    #editNoticeModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.75rem;
        align-items: center;
    }
    #addNoticeModal .modal-title,
    #editNoticeModal .modal-title {
        color: #fff;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
    }
    #addNoticeModal .modal-title i,
    #editNoticeModal .modal-title i {
        margin-left: 10px;
        font-size: 1.5rem;
        opacity: 0.9;
    }
    #addNoticeModal .close,
    #editNoticeModal .close {
        color: #fff;
        opacity: 0.9;
        text-shadow: none;
        padding: 0.5rem;
    }
    #addNoticeModal .close:hover,
    #editNoticeModal .close:hover {
        color: #fff;
        opacity: 1;
    }
    #addNoticeModal .modal-body,
    #editNoticeModal .modal-body {
        padding: 1.75rem;
        max-height: 75vh;
        overflow-y: auto;
    }
    #addNoticeModal .modal-footer,
    #editNoticeModal .modal-footer {
        border-top: 1px solid #ebedf3;
        padding: 1rem 1.75rem;
        background: #f9fafb;
    }
    /* بطاقات داخل المودال */
    #addNoticeModal .card.card-custom,
    #editNoticeModal .card.card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }
    #addNoticeModal .card-header,
    #editNoticeModal .card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #ebedf3;
        padding: 1rem 1.25rem;
        border-radius: 12px 12px 0 0;
    }
    #addNoticeModal .card-title,
    #editNoticeModal .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #3f4254;
    }
    #addNoticeModal .card-body,
    #editNoticeModal .card-body {
        padding: 1.25rem;
    }
    /* شريط البحث في مودال الإضافة */
    #addNoticeModal .row.mb-5 .col-md-4 {
        margin-bottom: 0.5rem;
    }
    #addNoticeModal .row.mb-5 label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #5e6278;
    }
    /* نموذج التعديل */
    #edit-notice-form-container .form-group.row label {
        color: #5e6278;
    }
    #editNoticeModal .modal-body > .text-center .spinner {
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('الإخطارات') }}"
                subtitle="{{ __('إدارة وتصفح سجلات الإخطارات') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        @can('store notices')
                                            <a href="#" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#addNoticeModal">
                                                <i class="fas fa-plus"></i> {{ __('إضافة جديد') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('status') || request()->filled('notice_reason_id') || request()->filled('from_date') || request()->filled('to_date') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('البحث') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('رقم الإخطار، الوصف') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('سبب الإخطار') }}</label>
                                    <select name="notice_reason_id" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        @foreach($noticeReasons as $reason)
                                            <option value="{{ $reason->id }}" {{ request('notice_reason_id') == $reason->id ? 'selected' : '' }}>
                                                {{ $reason->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }}</label>
                                    <select name="status" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('مسودة') }}</option>
                                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('مرسل') }}</option>
                                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('مسلّم') }}</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('مكان الإيجار') }}</label>
                                    <select name="rent_location_id" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        @foreach($rentLocations as $location)
                                            <option value="{{ $location->id }}" {{ request('rent_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
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
                                <div class="col-xl-6 col-md-6 mt-8">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8 py-3">
                                        <i class="fas fa-search"></i> {{ __('بحث') }}
                                    </button>
                                    <a href="{{ route('notices.index') }}" class="btn btn-light-primary font-weight-bolder px-8 py-3 ml-2">
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
                                <th class="pl-0" style="min-width: 120px">{{ __('رقم الإخطار') }}</th>
                                <th style="min-width: 110px">{{ __('تاريخ الإخطار') }}</th>
                                <th style="min-width: 150px">{{ __('سبب الإخطار') }}</th>
                                <th style="min-width: 120px">{{ __('مكان الإيجار') }}</th>
                                <th style="min-width: 90px">{{ __('الحالة') }}</th>
                                <th style="min-width: 120px">{{ __('أنشأ بواسطة') }}</th>
                                <th class="text-right pr-0" style="min-width: 120px">{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notices as $notice)
                                <tr>
                                    <td class="pl-0">
                                        <span class="badge badge-primary">{{ $notice->notice_number }}</span>
                                    </td>
                                    <td>{{ $notice->notice_date->format('d-m-Y') }}</td>
                                    <td>{{ $notice->noticeReason->name ?? '-' }}</td>
                                    <td>{{ $notice->rentLocation->name ?? '-' }}</td>
                                    <td>{!! $notice->status_badge !!}</td>
                                    <td>{{ $notice->creator->name ?? '-' }}</td>
                                    <td class="text-right pr-0">
                                        @can('update notices')
                                            <a href="javascript:;" class="btn btn-icon btn-light-primary btn-sm mx-1 edit-notice" data-id="{{ $notice->id }}" title="{{ __('تعديل') }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('delete notices')
                                            <a href="javascript:;" class="btn btn-icon btn-light-danger btn-sm mx-1 delete-notice" data-id="{{ $notice->id }}" title="{{ __('حذف') }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">
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
                        <span class="text-muted">{{ __('عرض') }} {{ $notices->firstItem() }} {{ __('إلى') }} {{ $notices->lastItem() }} {{ __('من') }} {{ $notices->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {{ $notices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إضافة إخطار (نموذج قلم الجمهور) --}}
    @can('store notices')
    <div class="modal fade" id="addNoticeModal" tabindex="-1" role="dialog" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="addNoticeForm" method="POST" action="{{ route('notices.store') }}" class="form card-shadowless">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bolder d-flex align-items-center" id="addNoticeModalLabel">
                            <i class="fas fa-file"></i>
                            {{ __('إضافة إخطار جديد') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-8">
                        {{-- شريط البحث (هوية / اسم / رقم اشتراك) --}}
                        <div class="notice-search-bar bg-light-primary rounded-lg p-5 mb-6">
                            <h6 class="text-dark font-weight-bolder mb-4 font-size-sm">
                                <i class="fas fa-search text-primary ml-1"></i>
                                {{ __('البحث عن المواطن') }}
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="col-form-label font-weight-bold text-dark">{{ __('رقم الهوية') }}</label>
                                    <select class="form-control form-control-solid select2" id="kt_select2_111"></select>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="col-form-label font-weight-bold text-dark">{{ __('الاسم') }}</label>
                                    <select class="form-control form-control-solid select2" id="kt_select2_222"></select>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-form-label font-weight-bold text-dark">{{ __('رقم الاشتراك') }}</label>
                                    <select class="form-control form-control-solid select2" id="kt_select2_333"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- بطاقة بيانات المواطن --}}
                            <div class="col-md-5">
                                <div class="card card-custom mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">بيانات المواطن</h3>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" class="form-control" id="user_id" name="user_id" value="">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>رقم الهوية</label>
                                                    <input type="number" class="form-control" id="identity_number" name="identity_number" placeholder="رقم الهوية"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>رقم الاشتراك</label>
                                                    <input type="number" class="form-control" id="jebaia_no" name="jebaia_no" placeholder="رقم الاشتراك"/>
                                                    <span class="text-danger" id="jebaia_no_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>الاسم الأول</label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="الاسم الأول"/>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>الاسم الثاني</label>
                                                    <input type="text" class="form-control" id="second_name" name="second_name" placeholder="الاسم الثاني"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>الاسم الثالث</label>
                                                    <input type="text" class="form-control" id="third_name" name="third_name" placeholder="الاسم الثالث" />
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>الاسم الأخير</label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="الاسم الأخير" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>الموبايل</label>
                                            <input type="number" class="form-control" id="mobile" name="mobile" placeholder="الموبايل"/>
                                        </div>
                                        <input class="form-control" id="SEX" name="sex" type="hidden" value=""/>
                                        <input class="form-control" id="SOCIAL_STATUS" name="social_status" type="hidden" value=""/>
                                    </div>
                                </div>
                            </div>

                            {{-- بطاقة بيانات الإخطار --}}
                            <div class="col-md-7">
                                <div class="card card-custom">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">بيانات الإخطار</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>اختر السنة:</label>
                                                <select class="form-control select2 select2_" id="year" name="year">
                                                    <option value=""></option>
                                                    @for ($x = now()->year; $x >= 2000; $x--)
                                                        <option value="{{ $x }}">{{ $x }}</option>
                                                    @endfor
                                                </select>
                                                <span class="text-danger" id="year_error"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>رقم الإخطار:</label>
                                                <input type="number" class="form-control" id="notice_id" name="notice_id" placeholder="رقم الإخطار"/>
                                                <span class="text-danger" id="notice_id_error"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>اختر الشهر:</label>
                                                <select class="form-control select2 select2_" id="notice_month" name="notice_month">
                                                    <option value=""></option>
                                                    @for ($x = 1; $x <= 12; $x++)
                                                        <option value="{{ $x }}">{{ $x }}</option>
                                                    @endfor
                                                </select>
                                                <span class="text-danger" id="notice_month_error"></span>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label>اختر سبب الإخطار:</label>
                                                <select class="form-control select2" id="notice_reason_id" name="notice_reason_id">
                                                    <option value=""></option>
                                                    @foreach($noticeReasons as $reason)
                                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="notice_reason_id_error"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>المبلغ المحكوم:</label>
                                                <input type="number" class="form-control" id="fine_amount" name="fine_amount" placeholder="المبلغ المحكوم"/>
                                                <span class="text-danger" id="fine_amount_error"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>تاريخ الإخطار:</label>
                                                <div class="input-group date" id="kt_datetimepicker_notice" data-target-input="nearest">
                                                    <input type="text" name="notice_date" class="form-control datetimepicker-input" placeholder="Select date & time" data-target="#kt_datetimepicker_notice"/>
                                                    <div class="input-group-append" data-target="#kt_datetimepicker_notice" data-toggle="datetimepicker">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="text-danger" id="notice_date_error"></span>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label>اختر نوع السوق:</label>
                                                <select class="form-control select2" id="rent_location_id" name="rent_location_id">
                                                    <option value=""></option>
                                                    @foreach($rentLocations as $location)
                                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="rent_location_id_error"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>وصف البناء:</label>
                                                <input type="text" class="form-control" id="build_desc" name="build_desc" placeholder="وصف البناء"/>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>رقم البسطة:</label>
                                                <input type="number" class="form-control" id="rent_no" name="rent_no" placeholder="رقم البسطة"/>
                                            </div>

                                            <div class="col-md-8 form-group">
                                                <label>العنوان:</label>
                                                <input type="text" class="form-control" id="notice_address" name="notice_address" placeholder="العنوان"/>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>رقم القضية:</label>
                                                <input type="text" class="form-control" id="org_file_number" name="org_file_number" placeholder="رقم القضية"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-warning font-weight-bold px-8 py-3" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bold px-8 py-3" id="addNoticeBtn">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- مودال تعديل إخطار --}}
    @can('update notices')
    <div class="modal fade" id="editNoticeModal" tabindex="-1" role="dialog" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bolder" id="editNoticeModalLabel">
                        <i class="fas fa-edit"></i>
                        {{ __('تعديل الإخطار') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-8" id="edit-notice-form-container">
                    <div class="text-center" style="padding: 20px 0;">
                        <div class="spinner spinner-primary spinner-lg"></div>
                        <p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p>
                    </div>
                </div>
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
    $(document).ready(function() {
        // تهيئة Select2 و DateTimePicker كما في قلم الجمهور
        $('#year').select2({
            placeholder: 'اختر السنة'
        });
        $('#notice_month').select2({
            placeholder: 'اختر الشهر'
        });
        $('#notice_reason_id').select2({
            placeholder: 'اختر سبب الإخطار'
        });
        $('#rent_location_id').select2({
            placeholder: 'اختر نوع السوق'
        });
        $('#kt_datetimepicker_notice').datetimepicker({
            defaultDate: '{{ \Carbon\Carbon::now() }}',
            format : 'YYYY-MM-DD HH:mm:ss',
            pick12HourFormat: false,
            use24hours: true
        });

        $('#addNoticeForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#addNoticeBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');
            $('.text-danger', form).text('');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.success) {
                        var message = response.message || '{{ __('تم إضافة الإخطار بنجاح') }}';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '{{ __('تم') }}',
                                text: message,
                                icon: 'success',
                                confirmButtonText: '{{ __('حسناً') }}'
                            }).then(function () {
                                $('#addNoticeModal').modal('hide');
                                form[0].reset();
                                location.reload();
                            });
                        } else {
                            $('#addNoticeModal').modal('hide');
                            form[0].reset();
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
                            $('#' + field.replace(/\./g, '_') + '_error').text(err);
                        });
                        if (xhr.responseJSON.message && typeof Swal !== 'undefined') {
                            Swal.fire({ title: 'تنبيه', text: xhr.responseJSON.message, icon: 'warning' });
                        }
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('حدث خطأ أثناء الحفظ') }}';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ title: 'خطأ', text: msg, icon: 'error' });
                        } else {
                            alert(msg);
                        }
                    }
                }
            });
        });
        $('#addNoticeModal').on('hidden.bs.modal', function() {
            $('#addNoticeForm')[0].reset();
            $('.text-danger').text('');
            $('#addNoticeBtn').prop('disabled', false).html('{{ __('حفظ') }}');
        });
        @if(session('openAddModal'))
        $('#addNoticeModal').modal('show');
        @endif
        // فتح مودال الإضافة وتعبئة البيانات من معلمات الرابط (مثل الرابط من صفحة المكلف)
        (function openAndFillFromQuery() {
            var params = new URLSearchParams(window.location.search);
            var identityNumber = params.get('identity_number');
            var name = params.get('name');
            var mobile = params.get('mobile');
            var address = params.get('address');
            if (name) try { name = decodeURIComponent(name); } catch (e) {}
            if (address) try { address = decodeURIComponent(address); } catch (e) {}
            if (!identityNumber && !name) return;
            var $modal = $('#addNoticeModal');
            if (!$modal.length) return;
            function fillFields() {
                if (identityNumber) $modal.find('#identity_number').val(identityNumber);
                if (mobile) $modal.find('input[name="mobile"]').val(mobile);
                if (name && name.trim()) {
                    var parts = name.trim().split(/\s+/);
                    $modal.find('#first_name').val(parts[0] || '');
                    $modal.find('#second_name').val(parts[1] || '');
                    $modal.find('#third_name').val(parts[2] || '');
                    $modal.find('#last_name').val(parts[3] || parts[2] || '');
                }
            }
            $modal.one('shown.bs.modal', fillFields);
            $modal.modal('show');
            setTimeout(fillFields, 400);
        })();
        // فتح مودال تعديل الإخطار
        $(document).on('click', '.edit-notice', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#editNoticeModal').modal('show');

            $('#edit-notice-form-container').html(
                '<div class="text-center" style="padding: 20px 0;">' +
                '<div class="spinner spinner-primary spinner-lg"></div>' +
                '<p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p>' +
                '</div>'
            );

            $.ajax({
                url: '{{ route("notices.edit", ":id") }}'.replace(':id', id) + '?modal=1',
                type: 'GET',
                success: function (response) {
                    $('#edit-notice-form-container').html(response);
                },
                error: function () {
                    $('#edit-notice-form-container').html(
                        '<div class="alert alert-danger" style="font-size: 13px; padding: 10px;">{{ __('حدث خطأ أثناء تحميل بيانات الإخطار') }}</div>'
                    );
                }
            });
        });

        $('#editNoticeModal').on('hidden.bs.modal', function () {
            $('#edit-notice-form-container').html(
                '<div class="text-center" style="padding: 20px 0;">' +
                '<div class="spinner spinner-primary spinner-lg"></div>' +
                '<p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p>' +
                '</div>'
            );
        });

        // حفظ التعديل عبر AJAX
        $(document).off('submit', '#edit-notice-form').on('submit', '#edit-notice-form', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();

            submitBtn.prop('disabled', true)
                .html('<i class="spinner spinner-sm spinner-primary"></i> {{ __('جاري التحديث...') }}');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (response) {
                    submitBtn.prop('disabled', false).html(originalText);
                    $('#editNoticeModal').modal('hide');

                    var message = response && response.message ? response.message : '{{ __('تم تحديث الإخطار بنجاح') }}';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: message,
                            confirmButtonText: '{{ __('حسناً') }}',
                            timer: 2000,
                            showConfirmButton: true
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert(message);
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalText);

                    if (xhr.status === 422 && xhr.responseJSON) {
                        var response = xhr.responseJSON;
                        var errors = response.errors || {};
                        var errorMessages = [];

                        $.each(errors, function (key, value) {
                            var fieldLabel = key;
                            var msg = Array.isArray(value) ? value[0] : value;
                            errorMessages.push(fieldLabel + ' : ' + msg);
                        });

                        var msgText = errorMessages.join('\\n');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: response.message || '{{ __('خطأ في التحقق') }}',
                                html: '<div style="text-align:right;direction:rtl;">' + errorMessages.join('<br>') + '</div>',
                                confirmButtonText: '{{ __('حسناً') }}'
                            });
                        } else {
                            alert(msgText);
                        }
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : '{{ __('حدث خطأ أثناء التحديث') }}';

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: msg,
                                confirmButtonText: '{{ __('حسناً') }}'
                            });
                        } else {
                            alert(msg);
                        }
                    }
                }
            });
        });

        // تأكيد حذف الإخطار عبر SweetAlert
        $('.delete-notice').click(function () {
            var id = $(this).data('id');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('هل أنت متأكد؟') }}',
                    text: '{{ __('سيتم حذف هذا الإخطار نهائياً') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('نعم، احذف') }}',
                    cancelButtonText: '{{ __('إلغاء') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteNoticeAjax(id);
                    }
                });
            } else {
                if (confirm('{{ __('هل أنت متأكد من حذف هذا الإخطار؟') }}')) {
                    deleteNoticeAjax(id);
                }
            }
        });

        function deleteNoticeAjax(id) {
            $.ajax({
                url: '{{ route("notices.destroy", ":id") }}'.replace(':id', id),
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
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert('{{ __('تم الحذف بنجاح') }}');
                        window.location.reload();
                    }
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

        // إعدادات AJAX عامة
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // البحث برقم الهوية
        $('#kt_select2_111').select2({
            placeholder: 'ابحث في الهوية',
            ajax: {
                url: "{{ route('notices.fetch_id') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.identity_number,
                                id: item.identity_number,
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function () {
            var identity_number = this.value;
            $.ajax({
                type: 'get',
                url: '{{ route('notices.ajax-user-data') }}',
                data: {identity_number: identity_number},
                success: function (data) {
                    $('#kt_select2_222').children().remove();
                    $('#kt_select2_222').append(data.name);
                    $('#kt_select2_333').children().remove();
                    $('#user_id').val(data.IDNO.id);
                    $('#identity_number').val(data.IDNO.identity_number);
                    $('#mobile').val(data.IDNO.profile.mobile);
                    $('#first_name').val(data.IDNO.profile.first_name);
                    $('#second_name').val(data.IDNO.profile.second_name);
                    $('#third_name').val(data.IDNO.profile.third_name);
                    $('#last_name').val(data.IDNO.profile.last_name);

                    $('#first_name, #second_name, #third_name, #last_name')
                        .attr('disabled', 'disabled')
                        .css({'background-color': '#dddddd','color': '#7b7b7b'});
                }
            });
        });

        // البحث بالاسم
        $('#kt_select2_222').select2({
            placeholder: 'ابحث في الاسم',
            ajax: {
                url: "{{ route('notices.fetch_name') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function () {
            var name = this.value;
            $.ajax({
                type: 'get',
                url: '{{ url('/') }}' + '/ajaxNameDataNotice',
                data: {name: name},
                success: function (data) {
                    $('#kt_select2_111').children().remove();
                    $('#kt_select2_111').append(data.identity_number);
                    $('#kt_select2_333').children().remove();
                    $('#user_id').val(data.IDNO.id);
                    $('#identity_number').val(data.IDNO.identity_number);
                    $('#mobile').val(data.IDNO.profile.mobile);
                    $('#first_name').val(data.IDNO.profile.first_name);
                    $('#second_name').val(data.IDNO.profile.second_name);
                    $('#third_name').val(data.IDNO.profile.third_name);
                    $('#last_name').val(data.IDNO.profile.last_name);

                    $('#first_name, #second_name, #third_name, #last_name')
                        .attr('disabled', 'disabled')
                        .css({'background-color': '#dddddd','color': '#7b7b7b'});
                }
            });
        });

        // البحث برقم الاشتراك
        $('#kt_select2_333').select2({
            placeholder: 'ابحث في رقم الاشتراك',
            ajax: {
                url: "{{ route('notices.fetch_jebaia') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.jebaia_no,
                                id: item.jebaia_no
                            };
                        })
                    };
                }
            }
        }).on('change', function () {
            var mokalaf = this.value;
            $.ajax({
                type: 'get',
                url: '{{ url('/') }}' + '/ajaxJebaiaDataNotice',
                data: {mokalaf: mokalaf},
                success: function (data) {
                    $('#kt_select2_111').children().remove();
                    $('#kt_select2_111').append(data.idNo);
                    $('#kt_select2_222').children().remove();
                    $('#kt_select2_222').append(data.userName);
                    $('#user_id').val(data.IDNO.id);
                    $('#identity_number').val(data.IDNO.identity_number);
                    $('#mobile').val(data.IDNO.profile.mobile);
                    $('#first_name').val(data.prof_first_name);
                    $('#second_name').val(data.prof_second_name);
                    $('#third_name').val(data.prof_third_name);
                    $('#last_name').val(data.prof_last_name);

                    $('#first_name, #second_name, #third_name, #last_name')
                        .attr('disabled', 'disabled')
                        .css({'background-color': '#dddddd','color': '#7b7b7b'});
                }
            });
        });

        // التحقق من رقم الهوية وجلب بيانات المواطن عند الإدخال اليدوي
        function checkid(value) {
            if (isNaN(value) || value.length != 9 || !IsValidId(value)) {
                return false;
            }
        }

        function IsValidId(id) {
            var A;
            var B = 0;
            for (var i = 0; i <= 7; i++) {
                A = parseInt(id.substring(i, i + 1));
                if ((i + 1) % 2 == 0) {
                    A = A * 2;
                }
                if (A > 9) {
                    A = A - 9;
                }
                B = B + A;
            }
            B = B % 10;
            B = (10 - B) % 10;
            if (B != parseInt(id.substring(8))) {
                return false;
            } else {
                return true;
            }
        }

        $('#identity_number').on('keydown blur', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                $(this).blur();
            }

            if (e.type == 'blur') {
                var P_DOC_ID = $('#identity_number').val();
                if (checkid(P_DOC_ID) === false) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'تحذير', text: 'رقم الهوية المدخلة خاطئ .. الرجاء التأكد من الرقم', icon: 'warning' });
                    }
                    $('#addNoticeForm')[0].reset();
                    $('#identity_number').val(P_DOC_ID);
                    $('.select2_').val('').trigger('change');
                    $('#user_id').val('');
                    return false;
                }

                if (typeof KTApp !== 'undefined') {
                    KTApp.block('.card-shadowless', { overlayColor: '#000000', type: 'v2', state: 'primary' });
                }

                $.ajax({
                    type: 'get',
                    url: '{{ route('notices.ajax-user-data') }}',
                    dataType: 'json',
                    data: {identity_number: P_DOC_ID},
                    success: function (data) {
                        $('#addNoticeForm')[0].reset();
                        $('.select2_').val('').trigger('change');
                        if (typeof KTApp !== 'undefined') {
                            KTApp.unblock('.card-shadowless');
                        }
                        $('#user_id').val(data.IDNO.id);
                        $('#identity_number').val(data.IDNO.identity_number);
                        $('#jebaia_no').val(data.IDNO.jebaia_no || '');
                        $('#mobile').val(data.IDNO.profile.mobile || '');
                        $('#first_name').val(data.IDNO.profile.first_name || '');
                        $('#second_name').val(data.IDNO.profile.second_name || '');
                        $('#third_name').val(data.IDNO.profile.third_name || '');
                        $('#last_name').val(data.IDNO.profile.last_name || '');
                    },
                    error: function () {
                        $.ajax({
                            type: 'GET',
                            url: '{{ route('getuserData') }}',
                            dataType: 'json',
                            data: {
                                P_DOC_ID: P_DOC_ID
                            },
                            success: function (data) {
                                var obj = (typeof data === 'string') ? JSON.parse(data) : data;
                                $('#addNoticeForm')[0].reset();
                                $('.select2_').val('').trigger('change');
                                if (typeof KTApp !== 'undefined') {
                                    KTApp.unblock('.card-shadowless');
                                }
                                $('#identity_number').val(P_DOC_ID);
                                $('#first_name').val(obj.FNAME_ARB);
                                $('#second_name').val(obj.SNAME_ARB);
                                $('#third_name').val(obj.TNAME_ARB);
                                $('#last_name').val(obj.LNAME_ARB);
                                $('#SEX').val(obj.SEX);
                                $('#SOCIAL_STATUS').val(obj.SOCIAL_STATUS);

                                $.ajax({
                                    type: 'get',
                                    url: '{{ route('insertuserData') }}',
                                    dataType: 'json',
                                    data: {
                                        P_DOC_ID: P_DOC_ID,
                                        first_name: $('#first_name').val(),
                                        second_name: $('#second_name').val(),
                                        third_name: $('#third_name').val(),
                                        last_name: $('#last_name').val(),
                                        sex: $('#SEX').val(),
                                        social_status: $('#SOCIAL_STATUS').val()
                                    },
                                    success: function (data) {
                                        var obj = (typeof data === 'string') ? JSON.parse(data) : data;
                                        $('#user_id').val(obj.id || '');
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
