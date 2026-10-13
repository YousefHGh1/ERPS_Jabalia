@extends('layout.master')

@section('title', 'طلباتي')
@section('page_title', 'إدارة الطلبات الشخصية')
@section('sub_main', 'البوابة الشخصية')
@section('sub_title', 'طلباتي')

@section('content')
    <div class="container-fluid">
        <x-page-header
            title="{{ __('إدارة الطلبات الشخصية') }}"
            subtitle="{{ __('Submit and track your holidays, advances and permissions') }}"
            icon="fa-paper-plane"
            module="administrative"
        >
            <x-slot name="actions">
                <a href="{{ route('employee.portal') }}" class="btn btn-light-primary font-weight-bolder">
                    <i class="fa fa-arrow-right"></i> العودة للبوابة
                </a>
            </x-slot>
        </x-page-header>

        {{-- ملخص سريع لحالة الطلبات --}}
        <div class="row">
            <div class="col-md-4 mb-5">
                <x-admin.stat-card
                    title="إجازات قيد الانتظار"
                    :value="$holidays->where('status', 'pending')->count()"
                    icon="fa-hourglass-half"
                    color="warning"
                    link="#"
                    linkText=""
                    :border="false"
                />
            </div>
            <div class="col-md-4 mb-5">
                <x-admin.stat-card
                    title="أذونات قيد الانتظار"
                    :value="$permissions->where('status', 'pending')->count()"
                    icon="fa-clock"
                    color="info"
                    link="#"
                    linkText=""
                    :border="false"
                />
            </div>
            <div class="col-md-4 mb-5">
                <x-admin.stat-card
                    title="طلبات مقبولة"
                    :value="$holidays->where('status', 'accepted')->count() + $permissions->where('status', 'accepted')->count()"
                    icon="fa-check-circle"
                    color="success"
                    link="#"
                    linkText=""
                    :border="false"
                />
            </div>
        </div>

        <div class="row">
            {{-- العمود الأيمن: الإجازات --}}
            <div class="col-lg-6">
                {{-- طلب إجازة --}}
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-umbrella-beach text-primary mr-2"></i>طلب إجازة جديد
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <form action="{{ route('employee.holidays.store') }}" method="POST" class="portal-request-form">
                            @csrf
                            <div class="form-group">
                                <label><i class="fa fa-list text-primary mr-1"></i> نوع الإجازة</label>
                                <select name="holiday_id" class="form-control" required>
                                    @foreach ($holiday_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-day text-primary mr-1"></i> تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-day text-primary mr-1"></i> تاريخ الانتهاء</label>
                                <input type="date" name="end_date" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-comment text-primary mr-1"></i> السبب</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="سبب طلب الإجازة..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary font-weight-bold btn-block"><i class="fa fa-paper-plane ml-1"></i> إرسال طلب إجازة</button>
                        </form>
                    </div>
                </div>
                {{-- سجل الإجازات --}}
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-history text-primary mr-2"></i>سجل الإجازات
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center portal-table">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 120px">النوع</th>
                                        <th class="p-0" style="min-width: 120px">الفترة</th>
                                        <th class="p-0" style="min-width: 100px">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($holidays as $holiday)
                                        <tr>
                                            <td class="pl-0 py-4">
                                                <span
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->holiday->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted font-weight-bold d-block" dir="ltr">{{ $holiday->start_date }}</span>
                                                <span class="text-muted font-weight-bold d-block" dir="ltr">{{ $holiday->end_date }}</span>
                                            </td>
                                            <td>
                                                @if ($holiday->status == 'pending')
                                                    <span class="label label-lg label-light-warning label-inline font-weight-bold"><span class="portal-status-dot is-late mr-1"></span>قيد الانتظار</span>
                                                @elseif($holiday->status == 'accepted' || $holiday->status == 'Approved')
                                                    <span class="label label-lg label-light-success label-inline font-weight-bold"><span class="portal-status-dot is-present mr-1"></span>مقبول</span>
                                                @else
                                                    <span class="label label-lg label-light-danger label-inline font-weight-bold"><span class="portal-status-dot is-absent mr-1"></span>مرفوض</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-6 text-muted font-weight-bold">
                                                <i class="fa fa-umbrella-beach fa-2x d-block mb-3 text-warning"></i>
                                                لا توجد طلبات إجازة — قدّم طلبك الأول من النموذج أعلاه
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- العمود الأيسر: السلف والأذونات --}}
            <div class="col-lg-6">

                {{-- طلب سلفة --}}
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-hand-holding-usd text-info mr-2"></i>طلب سلفة جديد
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <form action="{{ route('employee-advances.store') }}" method="POST" class="portal-request-form">
                            @csrf
                            <div class="form-group">
                                <label><i class="fa fa-money-bill-wave text-info mr-1"></i> المبلغ</label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" min="1" step="0.01"
                                        required placeholder="أدخل المبلغ" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">شيكل</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-comment text-info mr-1"></i> السبب</label>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="سبب طلب السلفة..."></textarea>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-day text-info mr-1"></i> تاريخ الطلب</label>
                                <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required />
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-day text-info mr-1"></i> تاريخ السداد المتوقع (اختياري)</label>
                                <input type="date" name="expected_repayment_date" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-info font-weight-bold btn-block"><i class="fa fa-paper-plane ml-1"></i> إرسال طلب سلفة</button>
                        </form>
                    </div>
                </div>
                <!-- سجل السلف -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-receipt text-info mr-2"></i>سجل السلف
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center portal-table">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 100px">المبلغ</th>
                                        <th class="p-0" style="min-width: 120px">تاريخ الطلب</th>
                                        <th class="p-0" style="min-width: 100px">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $advances = $employee->employeeAdvances()->latest()->get() ?? collect();
                                    @endphp
                                    @foreach ($advances as $advance)
                                        <tr>
                                            <td class="pl-0 py-4">
                                                <span
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg" dir="ltr">{{ number_format($advance->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted font-weight-bold d-block" dir="ltr">{{ $advance->request_date }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-lg label-light-{{ $advance->status_color }} label-inline font-weight-bold">{{ $advance->status_label }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($advances->count() == 0)
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted font-weight-bold">
                                                <i class="fa fa-wallet fa-2x d-block mb-3 text-info"></i>
                                                لا توجد سلف سابقة
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- طلب إذن --}}
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-file-signature text-success mr-2"></i>طلب إذن جديد
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <form action="{{ route('employee.permissions.store') }}" method="POST" class="portal-request-form">
                            @csrf
                            <div class="form-group">
                                <label><i class="fa fa-tag text-success mr-1"></i> الموضوع</label>
                                <input type="text" name="name" class="form-control" placeholder="مثلاً: ظرف عائلي"
                                    required />
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-list text-success mr-1"></i> النوع</label>
                                <select name="type" class="form-control" required>
                                    <option value="شخصي">شخصي</option>
                                    <option value="عمل">عمل</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-day text-success mr-1"></i> التاريخ</label>
                                <input type="date" name="date" class="form-control" required />
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-clock text-success mr-1"></i> من ساعة</label>
                                        <input type="time" name="start_time" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-clock text-success mr-1"></i> إلى ساعة</label>
                                        <input type="time" name="end_time" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success font-weight-bold btn-block"><i class="fa fa-paper-plane ml-1"></i> إرسال طلب إذن</button>
                        </form>
                    </div>
                </div>
                {{-- سجل الأذونات --}}
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-history text-success mr-2"></i>سجل الأذونات
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center portal-table">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 120px">الموضوع</th>
                                        <th class="p-0" style="min-width: 120px">التاريخ والوقت</th>
                                        <th class="p-0" style="min-width: 100px">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permissions as $permission)
                                        <tr>
                                            <td class="pl-0 py-4">
                                                <span
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $permission->name }}</span>
                                                <span class="text-muted font-weight-bold">{{ $permission->type }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted font-weight-bold d-block" dir="ltr">{{ $permission->date }}</span>
                                                <span
                                                    class="text-muted font-weight-bold d-block" dir="ltr">{{ $permission->start_time }}
                                                    - {{ $permission->end_time }}</span>
                                            </td>
                                            <td>
                                                @if ($permission->status == 'pending')
                                                    <span class="label label-lg label-light-warning label-inline font-weight-bold"><span class="portal-status-dot is-late mr-1"></span>قيد الانتظار</span>
                                                @elseif($permission->status == 'accepted')
                                                    <span
                                                        class="label label-lg label-light-success label-inline font-weight-bold"><span class="portal-status-dot is-present mr-1"></span>مقبول</span>
                                                @else
                                                    <span
                                                        class="label label-lg label-light-danger label-inline font-weight-bold"><span class="portal-status-dot is-absent mr-1"></span>مرفوض</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-6 text-muted font-weight-bold">
                                                <i class="fa fa-umbrella-beach fa-2x d-block mb-3 text-success"></i>
                                                لا توجد طلبات أذونات — قدّم طلبك الأول من النموذج أعلاه
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
