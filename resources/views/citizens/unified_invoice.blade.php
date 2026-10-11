@extends('layout.master')

@section('title', 'فاتورة موحدة للمواطن')
@section('page_title', 'فاتورة موحدة للمواطن')
@section('sub_title', 'عرض إجمالي الديون والمدفوعات')

@section('content')
    <div class="container-fluid" dir="rtl">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if (!empty($warning))
            <div class="alert alert-warning">{{ $warning }}</div>
        @endif

        <div class="card card-custom gutter-b shadow-sm">
            <div class="card-header border-0 py-4">
                <h3 class="card-title font-weight-bolder">بحث عن فاتورة موحدة</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('citizen.unified_invoice') }}" class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">رقم الهوية</label>
                        <input type="text" name="identity_number" value="{{ $identityNumber }}" class="form-control"
                            placeholder="أدخل رقم الهوية">
                    </div>
                    <div class="col-md-8 mt-3 mt-md-0">
                        <button class="btn btn-primary">عرض الفاتورة</button>
                        @if ($identityNumber !== '' && $invoiceData)
                            <a href="{{ route('citizen.unified_invoice.print', ['identity_number' => $identityNumber]) }}"
                                target="_blank" class="btn btn-light-success ml-2">
                                طباعة الفاتورة الموحدة
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if ($identityNumber !== '' && $invoiceData)
            <div class="card card-custom gutter-b shadow-sm">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6"><strong>اسم المواطن:</strong> {{ $invoiceData['citizen_name'] }}</div>
                        <div class="col-md-6"><strong>رقم الهوية:</strong> {{ $identityNumber }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-primary mb-0">
                                <div>إجمالي ديون قلم الجمهور</div>
                                <strong>{{ number_format($invoiceData['totals']['requests_due'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-warning mb-0">
                                <div>إجمالي ديون الهندسة والتنظيم</div>
                                <strong>{{ number_format($invoiceData['totals']['engineering_due'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-info mb-0">
                                <div>إجمالي ديون الحرف والصناعات</div>
                                <strong>{{ number_format($invoiceData['totals']['craft_due'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-success mb-0">
                                <div>إجمالي الديون الكلي</div>
                                <strong>{{ number_format($invoiceData['totals']['all_due'], 2) }} ₪</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-primary mb-0">
                                <div>مدفوعات قلم الجمهور</div>
                                <strong>{{ number_format($invoiceData['totals']['requests_paid'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-warning mb-0">
                                <div>مدفوعات الهندسة والتنظيم</div>
                                <strong>{{ number_format($invoiceData['totals']['engineering_paid'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-info mb-0">
                                <div>مدفوعات الحرف والصناعات</div>
                                <strong>{{ number_format($invoiceData['totals']['craft_paid'], 2) }} ₪</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="alert alert-light-success mb-0">
                                <div>إجمالي المدفوعات الكلي</div>
                                <strong>{{ number_format($invoiceData['totals']['all_paid'], 2) }} ₪</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-head-custom">
                            <thead class="thead-light">
                                <tr>
                                    <th>القسم / الجهة</th>
                                    <th>المرجع</th>
                                    <th>رقم الدفعة</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>تاريخ الدفع</th>
                                    <th>المحصل</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoiceData['payments'] as $row)
                                    <tr>
                                        <td>{{ $row['section'] }}</td>
                                        <td>{{ $row['reference'] }}</td>
                                        <td>{{ $row['payment_number'] }}</td>
                                        <td>{{ number_format((float) $row['amount'], 2) }} ₪</td>
                                        <td>{{ $row['payment_method'] }}</td>
                                        <td>{{ $row['payment_date'] }}</td>
                                        <td>{{ $row['collector'] }}</td>
                                        <td>{{ $row['notes'] ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">لا توجد دفعات مسجلة لهذا المواطن.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
