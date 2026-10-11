@extends('layout.master')

@section('title')
    {{ __('الحجوزات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الحجوزات') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('الحجوزات') }}"
                subtitle="{{ __('إدارة وتصفح سجلات الحجوزات') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        @can('store reservations')
                                            <a href="#" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#addReservationModal">
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
                                            placeholder="{{ __('رقم الحجز، الموضوع، القسيمة، القطعة، العنوان، الجباية') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }}</label>
                                    <select name="status" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('مؤكد') }}</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
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
                                    <a href="{{ route('reservations.index') }}" class="btn btn-light-primary font-weight-bolder px-8 py-3 ml-2">
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
                                <th class="pl-0" style="min-width: 110px">{{ __('رقم الحجز') }}</th>
                                <th style="min-width: 160px">{{ __('موضوع الحجز') }}</th>
                                <th style="min-width: 130px">{{ __('مساحة / قسيمة / قطعة') }}</th>
                                <th style="min-width: 120px">{{ __('حساب الجباية') }}</th>
                                <th style="min-width: 160px">{{ __('العنوان') }}</th>
                                <th style="min-width: 100px">{{ __('الحالة') }}</th>
                                <th style="min-width: 130px">{{ __('تاريخ الإنشاء') }}</th>
                                <th class="text-right pr-0" style="min-width: 120px">{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td class="pl-0">
                                        <span class="badge badge-primary">{{ $reservation->reservation_no ?? $reservation->reservation_number }}</span>
                                    </td>
                                    <td>{{ $reservation->subject ?? '—' }}</td>
                                    <td>
                                        {{ $reservation->area ? $reservation->area . 'م² - ' : '' }}
                                        {{ $reservation->voucher ?? '—' }} / {{ $reservation->widget ?? '—' }}
                                    </td>
                                    <td>{{ $reservation->jebaia_no ?? '—' }}</td>
                                    <td>{{ $reservation->address ?? '—' }}</td>
                                    <td>{!! $reservation->status_badge !!}</td>
                                    <td>{{ $reservation->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="text-right pr-0">
                                        @can('update reservations')
                                            <a href="javascript:;" data-toggle="modal" data-target="#editReservationModal-{{ $reservation->id }}" class="btn btn-icon btn-light-primary btn-sm mx-1" title="{{ __('تعديل') }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('delete reservations')
                                            <a href="javascript:;" class="btn btn-icon btn-light-danger btn-sm mx-1 delete-reservation" data-id="{{ $reservation->id }}" title="{{ __('حذف') }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
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
                        <span class="text-muted">{{ __('عرض') }} {{ $reservations->firstItem() }} {{ __('إلى') }} {{ $reservations->lastItem() }} {{ __('من') }} {{ $reservations->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إضافة حجز (نفس حقول قلم الجمهور) --}}
    @can('store reservations')
    <div class="modal fade" id="addReservationModal" tabindex="-1" role="dialog" aria-labelledby="addReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="addReservationForm" method="POST" action="{{ route('reservations.store') }}" class="form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bolder" id="addReservationModalLabel">{{ __('إضافة حجز جديد') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-8">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label text-right">{{ __('رقم الحجز') }}:</label>
                            <div class="col-lg-2">
                                <input type="number" class="form-control" name="reservation_no" required placeholder="{{ __('رقم الحجز') }}" value="{{ old('reservation_no') }}"/>
                                <span class="text-danger" id="reservation_no_error"></span>
                            </div>
                            <label class="col-form-label ml-3">{{ __('موضوع الحجز') }}:</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" required name="subject" value="{{ old('subject') }}" placeholder="{{ __('موضوع الحجز') }}"/>
                                <span class="text-danger" id="subject_error"></span>
                            </div>
                        </div>
                        <div id="kt_repeater_1" style="position: relative">
                            <div class="row">
                                <label class="col-lg-2 col-form-label text-right">{{ __('المستدعي') }}:</label>
                                <div data-repeater-list="summoner" class="col-lg-10">
                                    <div data-repeater-item class="form-group row align-items-center">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control js-reservation-idno" name="idno_summoner" placeholder="{{ __('رقم الهوية') }}" inputmode="numeric" maxlength="9" title="{{ __('أدخل رقم الهوية ثم Enter للاستعلام') }}"/>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control js-reservation-name" name="name_summoner" placeholder="{{ __('الاسم رباعي') }}"/>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger"><i class="la la-trash-o"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" style="position: absolute; top: 2px; left: 27%;">
                                <label class="col-lg-2 col-form-label text-right"></label>
                                <div class="col-lg-4 p-0 ml-auto">
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary"><i class="la la-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div id="kt_repeater_4" style="position: relative">
                            <div class="row">
                                <label class="col-lg-2 col-form-label text-right">{{ __('المستدعى ضده') }}:</label>
                                <div data-repeater-list="defendant" class="col-lg-10">
                                    <div data-repeater-item class="form-group row align-items-center">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control js-reservation-idno" name="idno_defendant" placeholder="{{ __('رقم الهوية') }}" inputmode="numeric" maxlength="9"/>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control js-reservation-name" name="name_defendant" placeholder="{{ __('الاسم رباعي') }}"/>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger"><i class="la la-trash-o"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" style="position: absolute; top: 2px; left: 27%;">
                                <label class="col-lg-2 col-form-label text-right"></label>
                                <div class="col-lg-4 p-0 ml-auto">
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary"><i class="la la-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label text-right">{{ __('مساحة الأرض') }}:</label>
                            <div class="col-lg-2">
                                <input type="text" class="form-control" name="area" value="{{ old('area') }}" placeholder="{{ __('مساحة الأرض') }}"/>
                            </div>
                            <label class="col-form-label ml-3">{{ __('القسيمة') }}:</label>
                            <div class="col-lg-2">
                                <input type="text" class="form-control" required name="voucher" value="{{ old('voucher') }}" placeholder="{{ __('القسيمة') }}"/>
                                <span class="text-danger" id="voucher_error"></span>
                            </div>
                            <label class="col-form-label ml-3">{{ __('القطعة') }}:</label>
                            <div class="col-lg-2">
                                <input type="text" class="form-control" required name="widget" value="{{ old('widget') }}" placeholder="{{ __('القطعة') }}"/>
                                <span class="text-danger" id="widget_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label text-right">{{ __('حساب الجباية') }}:</label>
                            <div class="col-lg-2">
                                <input type="number" class="form-control" name="jebaia_no" value="{{ old('jebaia_no') }}" placeholder="{{ __('حساب الجباية') }}"/>
                            </div>
                            <label class="col-form-label ml-3">{{ __('ملف الترخيص') }}:</label>
                            <div class="col-lg-3">
                                <input type="text" class="form-control" name="license_file" value="{{ old('license_file') }}" placeholder="{{ __('ملف الترخيص') }}"/>
                            </div>
                            <label class="col-form-label ml-3">{{ __('العنوان') }}:</label>
                            <div class="col-lg-3">
                                <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="{{ __('العنوان') }}"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label text-right">{{ __('النص') }}:</label>
                            <div class="col-lg-7">
                                <textarea class="form-control" name="contents" rows="6">{{ old('contents') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-warning font-weight-bold px-8 py-3" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bold px-8 py-3" id="addReservationBtn">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- مودالات تعديل الحجوزات --}}
    @can('update reservations')
        @foreach($reservations as $reservation)
            <div class="modal fade" id="editReservationModal-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="editReservationModalLabel-{{ $reservation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="form edit-reservation-form" data-id="{{ $reservation->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title font-weight-bolder" id="editReservationModalLabel-{{ $reservation->id }}">{{ __('تعديل الحجز') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i aria-hidden="true" class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body p-8">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-right">{{ __('رقم الحجز') }}:</label>
                                    <div class="col-lg-2">
                                        <input type="number" class="form-control" name="reservation_no" required value="{{ $reservation->reservation_no }}" placeholder="{{ __('رقم الحجز') }}"/>
                                    </div>
                                    <label class="col-form-label ml-3">{{ __('موضوع الحجز') }}:</label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" name="subject" required value="{{ $reservation->subject }}" placeholder="{{ __('موضوع الحجز') }}"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-right">{{ __('مساحة الأرض') }}:</label>
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control" name="area" value="{{ $reservation->area }}" placeholder="{{ __('مساحة الأرض') }}"/>
                                    </div>
                                    <label class="col-form-label ml-3">{{ __('القسيمة') }}:</label>
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control" name="voucher" value="{{ $reservation->voucher }}" placeholder="{{ __('القسيمة') }}"/>
                                    </div>
                                    <label class="col-form-label ml-3">{{ __('القطعة') }}:</label>
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control" name="widget" value="{{ $reservation->widget }}" placeholder="{{ __('القطعة') }}"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-right">{{ __('حساب الجباية') }}:</label>
                                    <div class="col-lg-2">
                                        <input type="number" class="form-control" name="jebaia_no" value="{{ $reservation->jebaia_no }}" placeholder="{{ __('حساب الجباية') }}"/>
                                    </div>
                                    <label class="col-form-label ml-3">{{ __('ملف الترخيص') }}:</label>
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" name="license_file" value="{{ $reservation->license_file }}" placeholder="{{ __('ملف الترخيص') }}"/>
                                    </div>
                                    <label class="col-form-label ml-3">{{ __('العنوان') }}:</label>
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" name="address" value="{{ $reservation->address }}" placeholder="{{ __('العنوان') }}"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-right">{{ __('النص') }}:</label>
                                    <div class="col-lg-7">
                                        <textarea class="form-control" name="contents" rows="6">{{ $reservation->contents }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-right">{{ __('الحالة') }}:</label>
                                    <div class="col-lg-3">
                                        <select name="status" class="form-control form-control-solid">
                                            <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                                            <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>{{ __('مؤكد') }}</option>
                                            <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                                            <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-warning font-weight-bold px-8 py-3" data-dismiss="modal">{{ __('إلغاء') }}</button>
                                <button type="submit" class="btn btn-primary font-weight-bold px-8 py-3">{{ __('تحديث') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan
@endsection

@push('js')
<script src="{{ asset('assets/js/pages/crud/forms/widgets/form-repeater.js') }}"></script>
<script>
(function() {
    function isValidId(value) {
        if (!value || value.length !== 9 || /[^0-9]/.test(value)) return false;
        var sum = 0, d;
        for (var i = 0; i < 8; i++) {
            d = parseInt(value.charAt(i), 10);
            sum += (i + 1) % 2 === 0 ? (d * 2 > 9 ? d * 2 - 9 : d * 2) : d;
        }
        return ((10 - (sum % 10)) % 10) === parseInt(value.charAt(8), 10);
    }
    function fullNameFromObj(obj) {
        if (!obj) return '';
        var parts = [obj.FNAME_ARB, obj.SNAME_ARB, obj.TNAME_ARB, obj.LNAME_ARB].filter(Boolean);
        return parts.join(' ').trim() || (obj.full_name_ar || obj.fullNameAr || obj.name_ar || obj.name || '');
    }
    function fetchCitizenAndFill(idnoInput, nameInput) {
        var cznId = ($(idnoInput).val() || '').trim();
        if (!cznId || !isValidId(cznId)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'تحذير', text: 'رقم الهوية يجب أن يكون 9 أرقام صحيحة.', icon: 'warning' });
            }
            return;
        }
        $.ajax({
            url: '{{ route("getCzn") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                czn_id: cznId
            },
            success: function (data) {
                if (data && !data.error) {
                    var full = data.FULLNAME || data.full_name_ar || data.name_ar || data.name || '';
                    if (full) {
                        $(nameInput).val(full);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ title: 'تم', text: 'تم جلب الاسم.', icon: 'success', timer: 1500, showConfirmButton: false });
                        }
                    }
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'تنبيه', text: 'لم يتم جلب البيانات. أدخل الاسم يدوياً.', icon: 'info' });
                }
            },
            error: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'تنبيه', text: 'لم يتم جلب البيانات. أدخل الاسم يدوياً.', icon: 'info' });
                }
            }
        });
    }
    $(document).on('blur', '.js-reservation-idno', function() {
        var $row = $(this).closest('[data-repeater-item]');
        var $name = $row.find('.js-reservation-name');
        if ($name.length) fetchCitizenAndFill(this, $name[0]);
    });
})();
</script>
<script>
$(document).ready(function() {
    $('#addReservationForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#addReservationBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');
        $('#addReservationModal .text-danger').text('');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'تم',
                            text: response.message || '{{ __('تم حفظ الحجز بنجاح') }}',
                            icon: 'success',
                            confirmButtonText: '{{ __('حسناً') }}'
                        }).then(function () {
                            $('#addReservationModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        });
                    } else {
                        $('#addReservationModal').modal('hide');
                        form[0].reset();
                        location.reload();
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
                    if (xhr.responseJSON.message && typeof Swal !== 'undefined') Swal.fire({ title: 'تنبيه', text: xhr.responseJSON.message, icon: 'warning' });
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('حدث خطأ أثناء الحفظ') }}';
                    if (typeof Swal !== 'undefined') Swal.fire({ title: 'خطأ', text: msg, icon: 'error' }); else alert(msg);
                }
            }
        });
    });
    $('#addReservationModal').on('hidden.bs.modal', function() {
        $('#addReservationForm')[0].reset();
        $('#addReservationModal .text-danger').text('');
        $('#addReservationBtn').prop('disabled', false).html('{{ __('حفظ') }}');
    });
    @if(session('openAddModal'))
    $('#addReservationModal').modal('show');
    @endif
    // فتح مودال الإضافة وتعبئة المستدعي الأول من معلمات الرابط
    (function openAndFillFromQuery() {
        var params = new URLSearchParams(window.location.search);
        var identityNumber = params.get('identity_number');
        var name = params.get('name');
        if (name) try { name = decodeURIComponent(name); } catch (e) {}
        if (!identityNumber && !name) return;
        var $modal = $('#addReservationModal');
        if (!$modal.length) return;
        function fillFields() {
            var $idno = $modal.find('.js-reservation-idno').first();
            var $nameInput = $modal.find('.js-reservation-name').first();
            if (identityNumber && $idno.length) $idno.val(identityNumber);
            if (name && $nameInput.length) $nameInput.val(name);
        }
        $modal.one('shown.bs.modal', fillFields);
        $modal.modal('show');
        setTimeout(fillFields, 400);
    })();

    // تعديل الحجز عبر البوب أب
    $('.edit-reservation-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var id = form.data('id');
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'تم',
                            text: response.message || '{{ __('تم تحديث الحجز بنجاح') }}',
                            icon: 'success',
                            confirmButtonText: '{{ __('حسناً') }}'
                        }).then(function () {
                            $('#editReservationModal-' + id).modal('hide');
                            location.reload();
                        });
                    } else {
                        $('#editReservationModal-' + id).modal('hide');
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('{{ __('تحديث') }}');
                if (xhr.status === 422 && xhr.responseJSON) {
                    var msg = xhr.responseJSON.message || '{{ __('حدثت أخطاء في التحقق من البيانات') }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'تنبيه', text: msg, icon: 'warning' });
                    } else {
                        alert(msg);
                    }
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('حدث خطأ أثناء التحديث') }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'خطأ', text: msg, icon: 'error' });
                    } else {
                        alert(msg);
                    }
                }
            }
        });
    });
    $('.delete-reservation').on('click', function() {
        var id = $(this).data('id');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __('هل أنت متأكد؟') }}',
                text: '{{ __('لن يمكنك التراجع عن هذه العملية!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('نعم، حذف') }}',
                cancelButtonText: '{{ __('إلغاء') }}'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("reservations.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            Swal.fire({
                                title: '{{ __('تم') }}',
                                text: '{{ __('تم حذف الحجز بنجاح') }}',
                                icon: 'success',
                                confirmButtonText: '{{ __('حسناً') }}'
                            }).then(function () {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: '{{ __('خطأ') }}',
                                text: '{{ __('حدث خطأ أثناء الحذف') }}',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        } else {
            if (confirm('{{ __('هل أنت متأكد من حذف هذا الحجز؟') }}')) {
                $.ajax({
                    url: '{{ route("reservations.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() { location.reload(); },
                    error: function() { alert('{{ __('حدث خطأ أثناء الحذف') }}'); }
                });
            }
        }
    });
});
</script>
@endpush
