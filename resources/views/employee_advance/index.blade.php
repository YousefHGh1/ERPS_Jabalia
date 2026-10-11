@extends('layout.master')

@section('title')
    {{ __('Employee Advances') }}
@endsection

@section('page_title')
    {{ __('إدارة طلبات السلف') }}
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
                title="{{ __('طلبات السلف الخاصة بي') }}"
                subtitle="{{ __('طلبات السلفة المقدمة مني') }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('employee-advances.create') }}" class="btn btn-primary font-weight-bolder">
                                            <i class="fa fa-plus"></i>{{ __('طلب سلفة جديدة') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <!-- My Advances Card -->
        <div class="card card-custom shadow-sm border-0 gutter-b">

            <div class="card-body pt-7">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr class="text-left text-uppercase">
                                <th class="pl-0" style="width: 50px">#</th>
                                <th style="min-width: 120px">{{ __('المبلغ') }}</th>
                                <th style="min-width: 120px">{{ __('تاريخ الطلب') }}</th>
                                <th style="min-width: 120px">{{ __('تاريخ السداد المتوقع') }}</th>
                                <th style="min-width: 120px">{{ __('الحالة') }}</th>
                                <th class="text-right pr-0" style="min-width: 100px">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myAdvances as $advance)
                                <tr>
                                    <td class="pl-0 font-weight-bolder text-muted">{{ $advance->id }}</td>
                                    <td>
                                        <span
                                            class="text-dark-75 font-weight-bolder d-block">{{ number_format($advance->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted font-weight-bold">{{ $advance->request_date }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted font-weight-bold">{{ $advance->expected_repayment_date ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="label label-lg label-light-{{ $advance->status_color }} label-inline font-weight-bold">
                                            {{ $advance->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-right pr-0">
                                        <a href="{{ route('employee-advances.show', $advance->id) }}"
                                            class="btn btn-sm btn-light-info btn-hover-info" title="{{ __('View') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-shield-alt display-4 text-muted"></i>
                                            <span class="text-muted mt-3 font-weight-bold">{{ __('no_records') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                    {!! $myAdvances->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>

        <!-- Pending Section Approvals Card -->
        @if ($pendingSection->count() > 0)
            <div class="card card-custom shadow-sm border-0 gutter-b">
                <div class="card-header border-0 py-5" style="background-color: #FFF8E1;">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('بانتظار موافقة مدير الدائرة') }}</span>
                        <span
                            class="text-muted mt-3 font-weight-bold font-size-sm">{{ __('طلبات سلفة تنتظر موافقتك كمدير دائرة') }}</span>
                    </h3>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-left text-uppercase">
                                    <th class="pl-0" style="width: 50px">#</th>
                                    <th style="min-width: 200px">{{ __('employee') }}</th>
                                    <th style="min-width: 120px">{{ __('المبلغ') }}</th>
                                    <th style="min-width: 120px">{{ __('تاريخ الطلب') }}</th>
                                    <th style="min-width: 200px">{{ __('السبب') }}</th>
                                    <th class="text-right pr-0" style="min-width: 200px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingSection as $advance)
                                    <tr>
                                        <td class="pl-0 font-weight-bolder text-muted">{{ $advance->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-info mr-3">
                                                    <span
                                                        class="symbol-label font-size-h5 font-weight-bold">{{ substr($advance->employee->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <a href="#"
                                                        class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $advance->employee->name }}</a>
                                                    <span class="text-muted font-weight-bold d-block">ID:
                                                        {{ $advance->employee->emp_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-dark-75 font-weight-bolder">{{ number_format($advance->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-weight-bold">{{ $advance->request_date }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-weight-bold d-block text-truncate"
                                                style="max-width: 200px;">{{ $advance->reason }}</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <a href="{{ route('employee-advances.show', $advance->id) }}"
                                                class="btn btn-sm btn-light-info btn-hover-info mr-2"
                                                title="{{ __('View') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light-success btn-hover-success mr-2"
                                                title="{{ __('Approve') }}" data-toggle="modal"
                                                data-target="#approveSectionModal{{ $advance->id }}">
                                                <i class="fa fa-check"></i> موافقة
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger btn-hover-danger"
                                                title="{{ __('Reject') }}" data-toggle="modal"
                                                data-target="#rejectModal{{ $advance->id }}">
                                                <i class="fa fa-times"></i> رفض
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pending Mayor Approvals Card -->
        @if ($pendingMayor->count() > 0)
            <div class="card card-custom shadow-sm border-0 gutter-b">
                <div class="card-header border-0 py-5" style="background-color: #E3F2FD;">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('بانتظار موافقة رئيس البلدية') }}</span>
                        <span
                            class="text-muted mt-3 font-weight-bold font-size-sm">{{ __('طلبات سلفة تنتظر موافقتك كرئيس بلدية') }}</span>
                    </h3>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-left text-uppercase">
                                    <th class="pl-0" style="width: 50px">#</th>
                                    <th style="min-width: 200px">{{ __('employee') }}</th>
                                    <th style="min-width: 120px">{{ __('المبلغ') }}</th>
                                    <th style="min-width: 120px">{{ __('تاريخ الطلب') }}</th>
                                    <th style="min-width: 200px">{{ __('ملاحظات مدير الدائرة') }}</th>
                                    <th class="text-right pr-0" style="min-width: 200px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingMayor as $advance)
                                    <tr>
                                        <td class="pl-0 font-weight-bolder text-muted">{{ $advance->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-info mr-3">
                                                    <span
                                                        class="symbol-label font-size-h5 font-weight-bold">{{ substr($advance->employee->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <a href="#"
                                                        class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $advance->employee->name }}</a>
                                                    <span class="text-muted font-weight-bold d-block">ID:
                                                        {{ $advance->employee->emp_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-dark-75 font-weight-bolder">{{ number_format($advance->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-weight-bold">{{ $advance->request_date }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted font-weight-bold d-block">{{ $advance->section_notes ?? '—' }}</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <a href="{{ route('employee-advances.show', $advance->id) }}"
                                                class="btn btn-sm btn-light-info btn-hover-info mr-2"
                                                title="{{ __('View') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light-success btn-hover-success mr-2"
                                                title="{{ __('Approve') }}" data-toggle="modal"
                                                data-target="#approveMayorModal{{ $advance->id }}">
                                                <i class="fa fa-check"></i> موافقة
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger btn-hover-danger"
                                                title="{{ __('Reject') }}" data-toggle="modal"
                                                data-target="#rejectModal{{ $advance->id }}">
                                                <i class="fa fa-times"></i> رفض
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pending Finance Approvals Card -->
        @if ($pendingFinance->count() > 0)
            <div class="card card-custom shadow-sm border-0 gutter-b">
                <div class="card-header border-0 py-5" style="background-color: #E8F5E9;">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('بانتظار موافقة دائرة المالية') }}</span>
                        <span
                            class="text-muted mt-3 font-weight-bold font-size-sm">{{ __('طلبات سلفة تنتظر موافقتك كمسؤول مالي') }}</span>
                    </h3>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-left text-uppercase">
                                    <th class="pl-0" style="width: 50px">#</th>
                                    <th style="min-width: 200px">{{ __('employee') }}</th>
                                    <th style="min-width: 120px">{{ __('المبلغ') }}</th>
                                    <th style="min-width: 120px">{{ __('تاريخ الطلب') }}</th>
                                    <th style="min-width: 200px">{{ __('ملاحظات رئيس البلدية') }}</th>
                                    <th class="text-right pr-0" style="min-width: 200px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingFinance as $advance)
                                    <tr>
                                        <td class="pl-0 font-weight-bolder text-muted">{{ $advance->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-info mr-3">
                                                    <span
                                                        class="symbol-label font-size-h5 font-weight-bold">{{ substr($advance->employee->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <a href="#"
                                                        class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $advance->employee->name }}</a>
                                                    <span class="text-muted font-weight-bold d-block">ID:
                                                        {{ $advance->employee->emp_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-dark-75 font-weight-bolder">{{ number_format($advance->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-weight-bold">{{ $advance->request_date }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted font-weight-bold d-block">{{ $advance->mayor_notes ?? '—' }}</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <a href="{{ route('employee-advances.show', $advance->id) }}"
                                                class="btn btn-sm btn-light-info btn-hover-info mr-2"
                                                title="{{ __('View') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light-success btn-hover-success mr-2"
                                                title="{{ __('Approve') }}" data-toggle="modal"
                                                data-target="#approveFinanceModal{{ $advance->id }}">
                                                <i class="fa fa-check"></i> موافقة
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger btn-hover-danger"
                                                title="{{ __('Reject') }}" data-toggle="modal"
                                                data-target="#rejectModal{{ $advance->id }}">
                                                <i class="fa fa-times"></i> رفض
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Approve Section Modals -->
    @foreach ($pendingSection as $advance)
        <div class="modal fade" id="approveSectionModal{{ $advance->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('employee-advances.approve-section', $advance->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">الموافقة على طلب سلفة #{{ $advance->id }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                            <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }}</strong></p>
                            <div class="form-group">
                                <label>ملاحظات (اختياري)</label>
                                <textarea name="section_notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية..."></textarea>
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
    @endforeach

    <!-- Approve Mayor Modals -->
    @foreach ($pendingMayor as $advance)
        <div class="modal fade" id="approveMayorModal{{ $advance->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('employee-advances.approve-mayor', $advance->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">الموافقة على طلب سلفة #{{ $advance->id }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                            <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }}</strong></p>
                            <div class="form-group">
                                <label>ملاحظات (اختياري)</label>
                                <textarea name="mayor_notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية..."></textarea>
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
    @endforeach

    <!-- Approve Finance Modals -->
    @foreach ($pendingFinance as $advance)
        <div class="modal fade" id="approveFinanceModal{{ $advance->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('employee-advances.approve-finance', $advance->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">الموافقة على طلب سلفة #{{ $advance->id }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                            <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }}</strong></p>
                            <div class="form-group">
                                <label>ملاحظات (اختياري)</label>
                                <textarea name="finance_notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية..."></textarea>
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
    @endforeach

    <!-- Reject Modals (for all pending types) -->
    @foreach (array_merge($pendingSection->all(), $pendingMayor->all(), $pendingFinance->all()) as $advance)
        <div class="modal fade" id="rejectModal{{ $advance->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('employee-advances.reject', $advance->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">رفض طلب سلفة #{{ $advance->id }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>الموظف: <strong>{{ $advance->employee->name }}</strong></p>
                            <p>المبلغ: <strong>{{ number_format($advance->amount, 2) }}</strong></p>
                            <div class="form-group">
                                <label>سبب الرفض <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder">أدخل سبب الرفض..."></textarea>
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
    @endforeach
@endsection
