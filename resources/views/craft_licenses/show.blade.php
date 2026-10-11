@extends('layout.master')

@section('title')
    {{ __('Craft Licenses') }} - عرض ملف حرفة رقم {{ $license->file_number }}
@endsection

@section('page_title')
    {{ __('Craft Licenses') }}
@endsection

@section('sub_title')
    عرض ملف حرفة
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="ملف حرفة رقم {{ $license->file_number }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('craft_licenses.print', $license->id) }}"
                                            class="btn btn-white font-weight-bold px-6 mr-2" target="_blank">
                                            <i class="fas fa-print text-primary"></i> طباعة الرخصة
                                        </a>
                                        <a href="{{ route('craft_licenses.edit', $license->id) }}"
                                            class="btn btn-transparent-white font-weight-bold px-6">
                                            <i class="fas fa-pen"></i> تعديل
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm">
            <div class="card-body">
                <div class="row">
                    {{-- القسم 1: بيانات الملف الأساسية --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">1. بيانات الملف الأساسية</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">حالة الملف</span>
                                        <span class="font-weight-bold">
                                            {{ $license->status }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رقم الملف</span>
                                        <span class="font-weight-bold">{{ $license->file_number }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رقم الجباية</span>
                                        <span class="font-weight-bold">{{ $license->tax_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">تاريخ تسجيل الملف</span>
                                        <span class="font-weight-bold">
                                            {{ optional($license->registration_date)->format('Y-m-d') ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">سنة أول ترخيص</span>
                                        <span class="font-weight-bold">{{ $license->first_license_year ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">آخر رخصة</span>
                                        <span class="font-weight-bold">{{ $license->last_license ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- القسم 2: صاحب الحرفة ومالك العقار --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">2. صاحب الحرفة ومالك العقار</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رقم هوية صاحب الحرفة</span>
                                        <span class="font-weight-bold">{{ $license->id_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">اسم صاحب الحرفة</span>
                                        <span class="font-weight-bold">{{ $license->owner_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رقم الهاتف/الجوال</span>
                                        <span class="font-weight-bold">{{ $license->phone_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رقم هوية مالك العقار</span>
                                        <span class="font-weight-bold">{{ $license->property_owner_id ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">اسم مالك العقار</span>
                                        <span class="font-weight-bold">{{ $license->property_owner ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">جهة الملكية</span>
                                        <span class="font-weight-bold">
                                            @switch($license->ownership_entity)
                                                @case('citizen') المواطن @break
                                                @case('municipality') بلدية جباليا النزلة @break
                                                @case('awqaf') وزارة الأوقاف @break
                                                @default -
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الشركاء في الحرفة --}}
                @if ($license->partners && $license->partners->count())
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">شركاء الحرفة</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th>رقم الهوية</th>
                                            <th>اسم الشريك</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($license->partners as $partner)
                                            <tr>
                                                <td>{{ $partner->identity_number }}</td>
                                                <td>{{ $partner->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    {{-- القسم 3: تفاصيل الحرفة والموقع --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">3. تفاصيل الحرفة والموقع</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">تصنيف الحرفة</span>
                                        <span class="font-weight-bold">
                                            {{ $license->classification ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">نوع الحرفة</span>
                                        <span class="font-weight-bold">{{ $license->craft_type ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <span class="text-muted d-block">نوع الحرفة التفصيلي</span>
                                        <span class="font-weight-bold">{{ $license->detailed_craft_type ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">الحي</span>
                                        <span class="font-weight-bold">{{ $license->neighborhood ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">الشارع الرئيسي</span>
                                        <span class="font-weight-bold">{{ $license->main_street ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">أقرب مسجد / معلم</span>
                                        <span class="font-weight-bold">{{ $license->nearest_mosque ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <span class="text-muted d-block">العنوان التفصيلي</span>
                                        <span class="font-weight-bold">{{ $license->address ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="text-muted d-block">رقم المبنى (GIS)</span>
                                        <span class="font-weight-bold">{{ $license->gis_building_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="text-muted d-block">رقم الشارع</span>
                                        <span class="font-weight-bold">{{ $license->street_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="text-muted d-block">الطابق</span>
                                        <span class="font-weight-bold">{{ $license->floor_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="text-muted d-block">رقم الوحدة / المحل</span>
                                        <span class="font-weight-bold">{{ $license->unit_number ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- القسم 4: البيانات المالية والرسوم --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">4. البيانات المالية والرسوم</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">إجمالي الديون السابقة</span>
                                        <span class="font-weight-bold">{{ number_format($license->total_debts ?? 0, 2) }}
                                            ₪</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رسوم الحرفة الحالية</span>
                                        <span class="font-weight-bold">{{ number_format($license->craft_fees ?? 0, 2) }}
                                            ₪</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رسوم اللوحة الإعلانية</span>
                                        <span class="font-weight-bold">
                                            {{ number_format($license->signboard_fees ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">إجمالي مستحق قبل الدفعات</span>
                                        <span class="font-weight-bold">
                                            {{ number_format(($license->total_debts ?? 0) + ($license->craft_fees ?? 0) + ($license->signboard_fees ?? 0), 2) }}
                                            ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">إجمالي المدفوعات</span>
                                        <span class="font-weight-bold">
                                            {{ number_format($license->payment_amount ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">الرصيد المتبقي</span>
                                        <span class="font-weight-bold text-danger">
                                            {{ number_format($license->remaining ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- جدول الدفعات --}}
                @if ($license->payments->count())
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">المدفوعات على هذه الرخصة</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>المبلغ</th>
                                            <th>الرصيد بعد الدفع</th>
                                            <th>طريقة الدفع</th>
                                            <th>المستخدم</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($license->payments as $payment)
                                            <tr>
                                                <td>{{ optional($payment->payment_date)->format('Y-m-d') ?? '-' }}</td>
                                                <td>{{ number_format($payment->amount, 2) }} ₪</td>
                                                <td>{{ number_format($payment->balance_after ?? 0, 2) }} ₪</td>
                                                <td>{{ $payment->payment_method ?? '-' }}</td>
                                                <td>{{ optional($payment->user)->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- المرفقات --}}
                @if ($license->attachments->count())
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">مرفقات الرخصة</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th>اسم الملف</th>
                                            <th>الحجم</th>
                                            <th>نوع الملف</th>
                                            <th>تحمبل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($license->attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->file_name }}</td>
                                                <td>
                                                    @if ($attachment->file_size)
                                                        {{ number_format($attachment->file_size / 1024, 2) }} KB
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $attachment->mime_type ?? '-' }}</td>
                                                <td>
                                                    @if ($attachment->file_path)
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                            class="btn btn-sm btn-light-primary" target="_blank">
                                                            <i class="fas fa-download"></i> تحميل
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- سجل الرسوم والتعديلات --}}
                @if (isset($histories) && $histories->count())
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">سجل الرسوم والتعديلات</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th>العملية</th>
                                            <th>نوع الحرفة</th>
                                            <th>القيمة</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($histories as $history)
                                            <tr>
                                                <td>{{ $history->operation_type }}</td>
                                                <td>{{ $history->craft_type }}</td>
                                                <td>{{ number_format($history->fee_amount, 2) }} ₪</td>
                                                <td>{{ $history->action_date }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

