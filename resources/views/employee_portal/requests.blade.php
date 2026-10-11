@extends('layout.master')

@section('title', 'طلباتي')
@section('page_title', 'إدارة الطلبات الشخصية')

@section('content')
    <div class="container">
        <div class="row">
            <!-- تقديم طلب جديد -->
            <div class="col-lg-6">
                <!-- طلب إجازة -->
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">طلب إجازة جديد</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee.holidays.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>نوع الإجازة</label>
                                <select name="holiday_id" class="form-control" required>
                                    @foreach ($holiday_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label>تاريخ الانتهاء</label>
                                <input type="date" name="end_date" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label>السبب</label>
                                <textarea name="reason" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary font-weight-bold btn-block">إرسال طلب
                                إجازة</button>
                        </form>
                    </div>
                </div>
                <!-- سجل الإجازات -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">سجل الإجازات</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 120px">النوع</th>
                                        <th class="p-0" style="min-width: 120px">الفترة</th>
                                        <th class="p-0" style="min-width: 100px">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($holidays as $holiday)
                                        <tr>
                                            <td class="pl-0 py-4">
                                                <span
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->holiday->name }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted font-weight-bold d-block">{{ $holiday->start_date }}</span>
                                                <span
                                                    class="text-muted font-weight-bold d-block">{{ $holiday->end_date }}</span>
                                            </td>
                                            <td>
                                                @if ($holiday->status == 'pending')
                                                    <span class="label label-lg label-light-warning label-inline">قيد
                                                        الانتظار</span>
                                                @elseif($holiday->status == 'accepted' || $holiday->status == 'Approved')
                                                    <span
                                                        class="label label-lg label-light-success label-inline">مقبول</span>
                                                @else
                                                    <span
                                                        class="label label-lg label-light-danger label-inline">مرفوض</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- قائمة الطلبات السابقة -->
            <div class="col-lg-6">

                <!-- طلب سلفة -->
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">طلب سلفة جديد</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee-advances.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>المبلغ</label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" min="1" step="0.01"
                                        required placeholder="أدخل المبلغ" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">شيكل</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>السبب</label>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="سبب طلب السلفة..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>تاريخ الطلب</label>
                                <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required />
                            </div>
                            <div class="form-group">
                                <label>تاريخ السداد المتوقع (اختياري)</label>
                                <input type="date" name="expected_repayment_date" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-info font-weight-bold btn-block">إرسال طلب سلفة</button>
                        </form>
                    </div>
                </div>
                <!-- سجل السلف -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">سجل السلف</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 100px">المبلغ</th>
                                        <th class="p-0" style="min-width: 120px">التاريخ</th>
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
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ number_format($advance->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted font-weight-bold d-block">{{ $advance->request_date }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-lg label-light-{{ $advance->status_color }} label-inline">{{ $advance->status_label }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($advances->count() == 0)
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">لا توجد سلف سابقة</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- طلب إذن -->
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">طلب إذن جديد</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee.permissions.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>الموضوع</label>
                                <input type="text" name="name" class="form-control" placeholder="مثلاً: ظرف عائلي"
                                    required />
                            </div>
                            <div class="form-group">
                                <label>النوع</label>
                                <select name="type" class="form-control" required>
                                    <option value="شخصي">شخصي</option>
                                    <option value="عمل">عمل</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>التاريخ</label>
                                <input type="date" name="date" class="form-control" required />
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>من ساعة</label>
                                        <input type="time" name="start_time" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>إلى ساعة</label>
                                        <input type="time" name="end_time" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success font-weight-bold btn-block">إرسال طلب
                                إذن</button>
                        </form>
                    </div>
                </div>
                <!-- سجل الأذونات -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">سجل الأذونات</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center">
                                <thead>
                                    <tr class="text-muted text-uppercase">
                                        <th class="p-0" style="min-width: 120px">الموضوع</th>
                                        <th class="p-0" style="min-width: 120px">التاريخ والوقت</th>
                                        <th class="p-0" style="min-width: 100px">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            <td class="pl-0 py-4">
                                                <span
                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $permission->name }}</span>
                                                <span class="text-muted font-weight-bold">{{ $permission->type }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted font-weight-bold d-block">{{ $permission->date }}</span>
                                                <span
                                                    class="text-muted font-weight-bold d-block">{{ $permission->start_time }}
                                                    - {{ $permission->end_time }}</span>
                                            </td>
                                            <td>
                                                @if ($permission->status == 'pending')
                                                    <span class="label label-lg label-light-warning label-inline">قيد
                                                        الانتظار</span>
                                                @elseif($permission->status == 'accepted')
                                                    <span
                                                        class="label label-lg label-light-success label-inline">مقبول</span>
                                                @else
                                                    <span
                                                        class="label label-lg label-light-danger label-inline">مرفوض</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
