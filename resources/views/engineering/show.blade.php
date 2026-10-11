@extends('layout.master')

@section('title')
    {{ __('Engineering Files') }} - عرض ملف رقم {{ $engineering->file_number }}
@endsection

@section('page_title')
    {{ __('Engineering Files') }}
@endsection

@section('sub_title')
    عرض ملف هندسة وتنظيم
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="ملف هندسة رقم {{ $engineering->file_number }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('engineering.print', $engineering->id) }}"
                                            class="btn btn-white font-weight-bold px-6 mr-2" target="_blank">
                                            <i class="fas fa-print text-primary"></i> طباعة الرخصة
                                        </a>
                                        <a href="{{ route('engineering.edit', $engineering->id) }}"
                                            class="btn btn-transparent-white font-weight-bold px-6">
                                            <i class="fas fa-pen"></i> تعديل
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm">
            <div class="card-body">
                <div class="row">
                    {{-- بيانات المالك --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">1. بيانات مالك الملف</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">الاسم الأول</span>
                                        <span class="font-weight-bold">{{ $engineering->first_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">اسم الأب</span>
                                        <span class="font-weight-bold">{{ $engineering->father_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">اسم الجد</span>
                                        <span class="font-weight-bold">{{ $engineering->grandfather_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">العائلة</span>
                                        <span class="font-weight-bold">{{ $engineering->family_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">الرقم الوطني</span>
                                        <span class="font-weight-bold">{{ $engineering->national_id ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">سنة الملف</span>
                                        <span class="font-weight-bold">{{ $engineering->file_year ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات الأرض --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">2. بيانات القطعة</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">رقم الحوض</span>
                                        <span class="font-weight-bold">{{ $engineering->plot_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">رقم القطعة</span>
                                        <span class="font-weight-bold">{{ $engineering->parcel_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <span class="text-muted d-block">المقسم</span>
                                        <span class="font-weight-bold">{{ $engineering->division_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">المساحة (م²)</span>
                                        <span class="font-weight-bold">{{ $engineering->area_size ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">الحي</span>
                                        <span class="font-weight-bold">{{ $engineering->neighborhood ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <span class="text-muted d-block">العنوان</span>
                                        <span class="font-weight-bold">{{ $engineering->address ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- بيانات البناء --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">3. بيانات البناء</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">نوع البناء</span>
                                        <span class="font-weight-bold">{{ $engineering->building_type ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">عدد الطوابق</span>
                                        <span class="font-weight-bold">
                                            @if(is_array($engineering->floors_count))
                                                {{ implode('، ', $engineering->floors_count) }}
                                            @else
                                                {{ $engineering->floors_count ?? '-' }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <span class="text-muted د-block">مشروع الإفراز</span>
                                        <span class="font-weight-bold">{{ $engineering->subdivision_project ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- البيانات المالية --}}
                    <div class="col-lg-6">
                        <div class="card card-custom shadow-none border mb-5">
                            <div class="card-header py-3 bg-light">
                                <h5 class="card-title mb-0 font-weight-bold text-dark">4. البيانات المالية</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رسوم GIS</span>
                                        <span class="font-weight-bold">{{ number_format($engineering->gis_fees ?? 0, 2) }} ₪</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">رسوم الترخيص</span>
                                        <span class="font-weight-bold">{{ number_format($engineering->license_fees ?? 0, 2) }} ₪</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">المبلغ الإجمالي</span>
                                        <span class="font-weight-bold text-success">
                                            {{ number_format($engineering->total_amount ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">المبلغ المدفوع</span>
                                        <span class="font-weight-bold">
                                            {{ number_format($engineering->paid_amount ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">المبلغ المتبقي</span>
                                        <span class="font-weight-bold text-danger">
                                            {{ number_format($engineering->remaining_amount ?? 0, 2) }} ₪
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <span class="text-muted d-block">تاريخ آخر دفعة</span>
                                        <span class="font-weight-bold">
                                            {{ optional($engineering->last_payment_date)->format('Y-m-d') ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- المرفقات --}}
                @if ($engineering->attachments->count())
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">مرفقات الملف الهندسي</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th>اسم الملف</th>
                                            <th>الحجم</th>
                                            <th>نوع الملف</th>
                                            <th>تحميل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($engineering->attachments as $attachment)
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

                {{-- الملاحظات --}}
                @if($engineering->notes)
                    <div class="card card-custom gutter-b shadow-none border">
                        <div class="card-header py-3 bg-light">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">ملاحظات</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $engineering->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

