@extends('layout.master')

@section('title', 'الاستعلام عن فواتير الجباية')
@section('page_title', 'قلم الجمهور')
@section('sub_title', 'الاستعلام عن فواتير الجباية')

@section('content')
    <div class="container-fluid" dir="rtl">
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-header border-0 py-4">
                <h3 class="card-title font-weight-bolder text-dark">
                    <i class="fas fa-file-invoice-dollar text-warning mr-2"></i> الاستعلام عن فواتير الجباية
                </h3>
            </div>
            <div class="card-body pt-0">
                <form method="GET" action="{{ route('collection.inquiry') }}" class="row align-items-end">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">البحث</label>
                        <input type="text" name="search" value="{{ $search }}"
                            class="form-control form-control-solid"
                            placeholder="أدخل الاسم أو رقم الجوال أو رقم السند أو رقم الاشتراك">
                        <span class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            البحث بالاسم لا يتأثر باختلاف الحروف (أ/إ/آ = ا، ة = ه، ى = ي)،
                            وعند البحث بعدة كلمات تُجلب الأسماء التي تحتويها متجاورة وبالتسلسل نفسه.
                        </span>
                    </div>
                    <div class="col-md-6 mb-3 text-left">
                        <button class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-search"></i> استعلام
                        </button>
                        @if ($search !== '')
                            <a href="{{ route('collection.inquiry.print', ['search' => $search]) }}" target="_blank"
                                class="btn btn-light-success font-weight-bolder mr-2">
                                <i class="fas fa-print"></i> طباعة
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if ($search !== '')
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-custom gutter-b shadow-sm border-0 bg-light-primary">
                        <div class="card-body text-center py-5">
                            <div class="text-muted font-weight-bold">عدد الفواتير</div>
                            <div class="font-size-h2 font-weight-bolder text-primary">{{ $totals['count'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-custom gutter-b shadow-sm border-0 bg-light-warning">
                        <div class="card-body text-center py-5">
                            <div class="text-muted font-weight-bold">إجمالي المستحق</div>
                            <div class="font-size-h2 font-weight-bolder text-warning">{{ number_format($totals['amt'], 2) }}
                                ₪</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-custom gutter-b shadow-sm border-0 bg-light-info">
                        <div class="card-body text-center py-5">
                            <div class="text-muted font-weight-bold">صافي المستحق</div>
                            <div class="font-size-h2 font-weight-bolder text-info">{{ number_format($totals['net'], 2) }} ₪
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-custom gutter-b shadow-sm border-0 bg-light-danger">
                        <div class="card-body text-center py-5">
                            <div class="text-muted font-weight-bold">المتأخرات</div>
                            <div class="font-size-h2 font-weight-bolder text-danger">{{ number_format($totals['late'], 2) }}
                                ₪</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom gutter-b shadow-sm border-0">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-file-invoice text-warning mr-2"></i> نتائج الاستعلام
                        <span class="badge badge-warning ml-2">{{ $totals['count'] }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    @if ($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>رقم الاشتراك</th>
                                        <th>رقم السند</th>
                                        <th>التاريخ</th>
                                        <th>الشهر</th>
                                        <th class="text-center">المستحق</th>
                                        <th class="text-center">الصافي</th>
                                        <th class="text-center">المتأخرات</th>
                                        <th class="text-center">الحسم</th>
                                        <th>الجوال</th>
                                        <th class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $inv)
                                        <tr>
                                            <td>{{ $inv->arabic_name }}</td>
                                            <td>{{ $inv->agreement_id ?? '-' }}</td>
                                            <td>{{ $inv->voucher_no ?? '-' }}</td>
                                            <td>{{ optional($inv->voucher_date)->format('Y-m-d') ?? '-' }}</td>
                                            <td>{{ $inv->voucher_month ?? '-' }}</td>
                                            <td class="text-center">{{ number_format($inv->voucher_amt, 2) }}</td>
                                            <td class="text-center">{{ number_format($inv->net_amt, 2) }}</td>
                                            <td class="text-center">{{ number_format($inv->total_late, 2) }}</td>
                                            <td class="text-center">{{ number_format($inv->auto_discount_ratio, 2) }}</td>
                                            <td>{{ $inv->mobile_no ?: '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('collection.inquiry.show', $inv->id) }}" target="_blank"
                                                    class="btn btn-sm btn-light-primary font-weight-bolder">
                                                    <i class="fas fa-print"></i> طباعة
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{-- {{ $invoices->appends(['search' => $search])->links() }} --}}
                            {!! $invoices->withQueryString()->links('pagination::bootstrap-5') !!}

                        </div>
                    @else
                        <p class="text-muted mb-0">
                            لا توجد فواتير جباية مطابقة للبحث.
                            <span class="d-block mt-1 small">
                                تحقق من تهجئة الاسم أو جرّب البحث بكلمة واحدة أو برقم السند / رقم الاشتراك.
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
