@extends('layout.master')

@section('title')
    {{ __('Advance Request Details') }}
@endsection

@section('page_title')
    {{ __('تفاصيل طلب السلفة') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('السلف') }}
@endsection

@section('content')
    <div class="container-fluid">
                    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
                    <x-page-header
                        title="{{ __('تفاصيل طلب السلفة') }}
                                #{{ $advance->id }}"
                    >
                        <x-slot name="actions">
                            <span
                                                            class="label label-lg label-light-{{ $advance->status_color }} label-inline font-weight-bold">
                                                            {{ $advance->status_label }}
                                                        </span>
                        </x-slot>
                    </x-page-header>
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm border-0 gutter-b">

                    <div class="card-body pt-7">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-muted" style="width: 200px;">{{ __('الموظف') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->employee->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('رقم الموظف') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->employee->emp_number ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('القسم') }}</td>
                                    <td class="font-weight-bolder">
                                        {{ $advance->employee->section->name ?? ($advance->employee->department->name ?? '—') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('المبلغ') }}</td>
                                    <td class="font-weight-bolder text-primary font-size-h4">
                                        {{ number_format($advance->amount, 2) }} شيكل</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('السبب') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->reason }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('تاريخ الطلب') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->request_date }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('تاريخ السداد المتوقع') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->expected_repayment_date ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('تاريخ التقديم') }}</td>
                                    <td class="font-weight-bolder">{{ $advance->created_at }}</td>
                                </tr>
                                @if ($advance->rejection_reason)
                                    <tr>
                                        <td class="font-weight-bold text-danger">{{ __('سبب الرفض') }}</td>
                                        <td class="font-weight-bolder text-danger">{{ $advance->rejection_reason }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approval Timeline -->
                <div class="card card-custom shadow-sm border-0 gutter-b">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('مسار الموافقات') }}</span>
                        </h3>
                    </div>

                    <div class="card-body pt-0">
                        <div class="timeline timeline-6 mt-3">
                            <!-- Step 1: Section Director -->
                            <div class="timeline-item align-items-start">
                                <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">
                                    {{ $advance->section_approved_at ? $advance->section_approved_at->format('Y-m-d') : '—' }}
                                </div>
                                <div class="timeline-badge">
                                    <i
                                        class="fa fa-{{ $advance->status == 'pending' ? 'clock text-warning' : ($advance->status == 'rejected' && !$advance->section_approved_at ? 'times text-danger' : 'check text-success') }}"></i>
                                </div>
                                <div class="timeline-content d-flex">
                                    <span class="font-weight-bolder text-dark-75 pl-3">
                                        {{ __('مدير الدائرة') }}
                                    </span>
                                    <span class="text-muted font-weight-bold">
                                        @if ($advance->section_approved_at)
                                            <span class="text-success">تمت الموافقة</span>
                                            @if ($advance->section_notes)
                                                <br><small>{{ $advance->section_notes }}</small>
                                            @endif
                                        @elseif($advance->status == 'rejected' && !$advance->section_approved_at)
                                            <span class="text-danger">لم تتم الموافقة</span>
                                        @else
                                            <span class="text-warning">بانتظار الموافقة</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Step 2: Mayor -->
                            <div class="timeline-item align-items-start">
                                <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">
                                    {{ $advance->mayor_approved_at ? $advance->mayor_approved_at->format('Y-m-d') : '—' }}
                                </div>
                                <div class="timeline-badge">
                                    <i
                                        class="fa fa-{{ $advance->status == 'pending' || $advance->status == 'section_approved' ? 'clock text-warning' : ($advance->status == 'rejected' && !$advance->mayor_approved_at ? 'times text-danger' : 'check text-success') }}"></i>
                                </div>
                                <div class="timeline-content d-flex">
                                    <span class="font-weight-bolder text-dark-75 pl-3">
                                        {{ __('رئيس البلدية') }}
                                    </span>
                                    <span class="text-muted font-weight-bold">
                                        @if ($advance->mayor_approved_at)
                                            <span class="text-success">تمت الموافقة</span>
                                            @if ($advance->mayor_notes)
                                                <br><small>{{ $advance->mayor_notes }}</small>
                                            @endif
                                        @elseif($advance->status == 'rejected' && $advance->section_approved_at && !$advance->mayor_approved_at)
                                            <span class="text-danger">لم تتم الموافقة</span>
                                        @elseif(in_array($advance->status, ['section_approved', 'mayor_approved', 'finance_approved']))
                                            <span class="text-warning">بانتظار الموافقة</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Step 3: Finance -->
                            <div class="timeline-item align-items-start">
                                <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">
                                    {{ $advance->finance_approved_at ? $advance->finance_approved_at->format('Y-m-d') : '—' }}
                                </div>
                                <div class="timeline-badge">
                                    <i
                                        class="fa fa-{{ $advance->status == 'finance_approved' ? 'check text-success' : ($advance->status == 'rejected' && $advance->mayor_approved_at ? 'times text-danger' : 'clock text-muted') }}"></i>
                                </div>
                                <div class="timeline-content d-flex">
                                    <span class="font-weight-bolder text-dark-75 pl-3">
                                        {{ __('دائرة المالية') }}
                                    </span>
                                    <span class="text-muted font-weight-bold">
                                        @if ($advance->finance_approved_at)
                                            <span class="text-success">تمت الموافقة</span>
                                            @if ($advance->finance_notes)
                                                <br><small>{{ $advance->finance_notes }}</small>
                                            @endif
                                        @elseif($advance->status == 'rejected' && $advance->mayor_approved_at && !$advance->finance_approved_at)
                                            <span class="text-danger">لم تتم الموافقة</span>
                                        @elseif($advance->status == 'mayor_approved' || $advance->status == 'finance_approved')
                                            <span class="text-warning">بانتظار الموافقة</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card card-custom shadow-sm border-0 gutter-b">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('معلومات الحالة') }}</span>
                        </h3>
                    </div>

                    <div class="card-body pt-0 text-center">
                        <div class="p-5">
                            <div class="symbol symbol-80 symbol-{{ $advance->status_color }} mb-4">
                                <span class="symbol-label font-size-h1">
                                    <i
                                        class="fa fa-{{ $advance->status == 'finance_approved' ? 'check-circle' : ($advance->status == 'rejected' ? 'times-circle' : 'clock') }}"></i>
                                </span>
                            </div>
                            <h4 class="font-weight-bolder">{{ $advance->status_label }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @php
                    $employee = auth()->user()->employee;
                    $canApprove = false;
                    $approveRoute = null;
                    $approveLabel = '';
                    if ($employee) {
                        if ($advance->section_director_id == $employee->id && $advance->status == 'pending') {
                            $canApprove = true;
                            $approveRoute = route('employee-advances.approve-section', $advance->id);
                            $approveLabel = 'الموافقة كمدير دائرة';
                        } elseif ($advance->mayor_id == $employee->id && $advance->status == 'section_approved') {
                            $canApprove = true;
                            $approveRoute = route('employee-advances.approve-mayor', $advance->id);
                            $approveLabel = 'الموافقة كرئيس بلدية';
                        } elseif (
                            $advance->finance_officer_id == $employee->id &&
                            $advance->status == 'mayor_approved'
                        ) {
                            $canApprove = true;
                            $approveRoute = route('employee-advances.approve-finance', $advance->id);
                            $approveLabel = 'الموافقة كمسؤول مالي';
                        }
                    }
                @endphp

                @if ($canApprove)
                    <div class="card card-custom shadow-sm border-0 gutter-b">
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">{{ __('إجراءات سريعة') }}</span>
                            </h3>
                        </div>

                        <div class="card-body pt-0">
                            <button type="button" class="btn btn-success btn-block font-weight-bold mb-3"
                                data-toggle="modal" data-target="#quickApproveModal">
                                <i class="fa fa-check"></i> {{ $approveLabel }}
                            </button>
                            <button type="button" class="btn btn-danger btn-block font-weight-bold" data-toggle="modal"
                                data-target="#quickRejectModal">
                                <i class="fa fa-times"></i> رفض الطلب
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('employee-advances.index') }}" class="btn btn-light-primary font-weight-bold">
                <i class="fa fa-arrow-right"></i> {{ __('العودة إلى قائمة السلف') }}
            </a>
        </div>
    </div>

    <!-- Quick Approve Modal -->
    @if (isset($approveRoute))
        <div class="modal fade" id="quickApproveModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ $approveRoute }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $approveLabel }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                            <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }} شيكل</strong></p>
                            <div class="form-group">
                                <label>ملاحظات (اختياري)</label>
                                <textarea
                                    name="{{ $advance->section_director_id == auth()->user()->employee_id && $advance->status == 'pending' ? 'section_notes' : ($advance->mayor_id == auth()->user()->employee_id ? 'mayor_notes' : 'finance_notes') }}"
                                    class="form-control" rows="3" placeholder="أي ملاحظات إضافية..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success">تأكيد الموافقة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Reject Modal -->
    <div class="modal fade" id="quickRejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('employee-advances.reject', $advance->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض طلب السلفة</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                        <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }} شيكل</strong></p>
                        <div class="form-group">
                            <label>سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="أدخل سبب الرفض..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
