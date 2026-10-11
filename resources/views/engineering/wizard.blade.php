@extends('layout.master')

@section('title')
    {{ __('Engineering Files') }} - {{ $engineering->exists ? __('Edit File') : __('Add File') }}
@endsection

@section('page_title')
    {{ __('Engineering Files') }}
@endsection

@section('sub_title')
    {{ __('Engineering Files') }} - {{ $engineering->exists ? __('Edit File') : __('Add File') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ $engineering->exists ? 'تعديل ملف هندسة: ' . $engineering->file_number : 'إنشاء ملف هندسة جديد' }}"
            >
                <x-slot name="actions">
                    @if ($engineering->exists)
                                            <a href="{{ route('engineering.print', $engineering->id) }}"
                                                class="btn btn-white font-weight-bold px-6 mr-2" target="_blank">
                                                <i class="fas fa-print text-primary"></i> طباعة
                                            </a>
                                        @endif
                                        <a href="{{ route('engineering.index') }}"
                                            class="btn btn-transparent-white font-weight-bold px-6">عودة</a>
                </x-slot>
            </x-page-header>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-custom gutter-b shadow-sm">
            <!-- Header -->

            <div class="card-body">
                <!-- Form Start -->
                <form
                    action="{{ $engineering->exists ? route('engineering.update', $engineering->id) : route('engineering.store') }}"
                    method="POST" id="engineeringForm" class="p-6 p-md-10" novalidate>
                    @csrf
                    @if ($engineering->exists)
                        @method('PUT')
                    @endif

                    {{-- الصف الأول: القسم 1 + القسم 2 --}}
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- القسم 1: المعلومات الأساسية -->
                            <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                                <div class="card-header py-4 bg-light">
                                    <h3 class="card-title mb-0">
                                        <span class="card-label font-weight-bolder text-dark">1. المعلومات الأساسية</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم الملف <span class="text-danger">*</span></label>
                                                <input type="text" name="file_number" id="file_number"
                                                    class="form-control form-control-solid form-control-lg"
                                                    value="{{ old('file_number', $engineering->file_number ?? '') }}"
                                                    readonly style="background-color: #f3f6f9; cursor: not-allowed;"
                                                    placeholder="{{ $engineering->exists ? '' : 'يتم التوليد تلقائياً' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>سنة الملف <span class="text-danger">*</span></label>
                                                <input type="text" name="file_year" id="file_year"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    maxlength="4"
                                                    value="{{ old('file_year', $engineering->file_year ?? date('Y')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>الرقم الوطني <span class="text-danger">*</span></label>
                                                <input type="text" name="national_id" id="national_id"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    maxlength="9"
                                                    value="{{ old('national_id', $engineering->national_id ?? '') }}">
                                                <small class="text-muted" id="loading-name" style="display: none;">
                                                    <i class="fas fa-spinner fa-spin"></i> جاري البحث...
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>الاسم الأول <span class="text-danger">*</span></label>
                                                <input type="text" name="first_name" id="first_name"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('first_name', $engineering->first_name ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>اسم الأب <span class="text-danger">*</span></label>
                                                <input type="text" name="father_name" id="father_name"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('father_name', $engineering->father_name ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>اسم الجد</label>
                                                <input type="text" name="grandfather_name" id="grandfather_name"
                                                    class="form-control form-control-solid form-control-lg"
                                                    value="{{ old('grandfather_name', $engineering->grandfather_name ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>اسم العائلة <span class="text-danger">*</span></label>
                                                <input type="text" name="family_name" id="family_name"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('family_name', $engineering->family_name ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم الهاتف <span class="text-danger">*</span></label>
                                                <input type="text" name="phone_number" id="phone_number"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('phone_number', $engineering->phone_number ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم الموبايل <span class="text-danger">*</span></label>
                                                <input type="text" name="mobile_number" id="mobile_number"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('mobile_number', $engineering->mobile_number ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>نوع البناء <span class="text-danger">*</span></label>
                                                <select name="building_type" id="building_type"
                                                    class="form-control form-control-solid form-control-lg" required>
                                                    <option value="">اختر النوع...</option>
                                                    <option value="سكني"
                                                        {{ old('building_type', $engineering->building_type) == 'سكني' ? 'selected' : '' }}>
                                                        سكني</option>
                                                    <option value="تجاري"
                                                        {{ old('building_type', $engineering->building_type) == 'تجاري' ? 'selected' : '' }}>
                                                        تجاري</option>
                                                    <option value="مختلط"
                                                        {{ old('building_type', $engineering->building_type) == 'مختلط' ? 'selected' : '' }}>
                                                        مختلط</option>
                                                    <option value="صناعي"
                                                        {{ old('building_type', $engineering->building_type) == 'صناعي' ? 'selected' : '' }}>
                                                        صناعي</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>عدد الطوابق <span class="text-danger">*</span></label>
                                                @php
                                                    $floors = [
                                                        1 => 'بدروم',
                                                        2 => 'سدة 1',
                                                        3 => 'سدة 2',
                                                        4 => 'أرضي',
                                                        5 => 'طابق أول',
                                                        6 => 'طابق ثاني',
                                                        7 => 'طابق ثالث',
                                                        8 => 'طابق رابع',
                                                        9 => 'طابق خامس',
                                                        10 => 'طابق سادس',
                                                        11 => 'طابق سابع',
                                                        12 => 'طابق ثامن',
                                                        13 => 'رووف',
                                                    ];

                                                    $selectedFloors = old(
                                                        'floors_count',
                                                        $engineering->floors_count ?? [],
                                                    );
                                                @endphp

                                                <select
                                                    class="form-control selectpicker form-control-solid form-control-lg @error('floors_count') is-invalid @enderror"
                                                    data-live-search="true" data-dropup-auto="false" title="اختر الطوابق"
                                                    name="floors_count[]" multiple required>
                                                    @foreach ($floors as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ in_array($key, (array) $selectedFloors) ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('floors_count')
                                                    <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- القسم 2: الموقع الجغرافي والعنوان -->
                            <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                                <div class="card-header py-4 bg-light">
                                    <h3 class="card-title mb-0">
                                        <span class="card-label font-weight-bolder text-dark">2. الموقع الجغرافي
                                            والعنوان</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم القسيمة <span class="text-danger">*</span></label>
                                                <input type="text" name="plot_number" id="plot_number"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('plot_number', $engineering->plot_number ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم القطعة <span class="text-danger">*</span></label>
                                                <input type="text" name="parcel_number" id="parcel_number"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('parcel_number', $engineering->parcel_number ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>رقم المقسم <span class="text-danger">*</span></label>
                                                <input type="text" name="division_number" id="division_number"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('division_number', $engineering->division_number ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>مساحة الأرض <span class="text-danger">*</span></label>
                                                <input type="number" name="area_size" id="area_size"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    step="0.01"
                                                    value="{{ old('area_size', $engineering->area_size ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>الحي <span class="text-danger">*</span></label>
                                                <input type="text" name="neighborhood" id="neighborhood"
                                                    class="form-control form-control-solid form-control-lg" required
                                                    value="{{ old('neighborhood', $engineering->neighborhood ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>نوع الافراز <span class="text-danger">*</span></label>
                                                <select name="subdivision_type" id="subdivision_type"
                                                    class="form-control form-control-solid form-control-lg" required>
                                                    <option value="">اختر النوع...</option>
                                                    <option value="مفرز"
                                                        {{ old('subdivision_type', $engineering->subdivision_type) == 'مفرز' ? 'selected' : '' }}>
                                                        مفرز
                                                    </option>
                                                    <option value="غير مفرز"
                                                        {{ old('subdivision_type', $engineering->subdivision_type) == 'غير مفرز' ? 'selected' : '' }}>
                                                        غير مفرز
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>مشروع الافراز <span class="text-danger">*</span></label>
                                                <select name="subdivision_project" id="subdivision_project"
                                                    class="form-control form-control-solid form-control-lg"
                                                    style="display: none;">
                                                    <option value="">اختر المشروع...</option>
                                                    <option value="علمي"
                                                        {{ old('subdivision_project', $engineering->subdivision_project) == 'علمي' ? 'selected' : '' }}>
                                                        علمي
                                                    </option>
                                                    <option value="شنطي"
                                                        {{ old('subdivision_project', $engineering->subdivision_project) == 'شنطي' ? 'selected' : '' }}>
                                                        شنطي
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>العنوان <span class="text-danger">*</span></label>
                                                <textarea name="address" id="address" class="form-control form-control-solid form-control-lg" rows="3"
                                                    required>{{ old('address', $engineering->address ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                        <!-- القسم 3: الرسوم والبيانات المالية -->
                        <div class="mb-8">
                            <div class="card card-custom gutter-b shadow-sm border-0">
                                <div class="card-header py-4 bg-light">
                                    <h3 class="card-title mb-0">
                                        <span class="card-label font-weight-bolder text-dark">3. الرسوم والبيانات
                                            المالية</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row bg-light rounded p-8 mb-8">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>رسوم التطوير <span class="text-danger">*</span></label>
                                                <input type="number" name="gis_fees" id="gis_fees"
                                                    class="form-control form-control-lg font-weight-bolder text-primary"
                                                    value="{{ old('gis_fees', $engineering->gis_fees ?? 0) }}"
                                                    step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>الغرامات</label>
                                                <input type="number" name="penalties" id="penalties"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('penalties', $engineering->penalties ?? 0) }}"
                                                    step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>رسوم الترخيص <span class="text-danger">*</span></label>
                                                <input type="number" name="license_fees" id="license_fees"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('license_fees', $engineering->license_fees ?? 0) }}"
                                                    step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="text-success font-weight-bold">المبلغ المدفوع <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="paid_amount" id="paid_amount"
                                                    class="form-control form-control-lg border-success font-weight-bolder"
                                                    value="{{ old('paid_amount', $engineering->paid_amount ?? 0) }}"
                                                    step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>المبلغ المتبقي</label>
                                                <input type="text" id="remaining_amount" name="remaining_amount"
                                                    class="form-control form-control-solid form-control-lg bg-light-danger font-weight-bold"
                                                    readonly
                                                    value="{{ old('remaining_amount', $engineering->remaining_amount ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>المبلغ الإجمالي</label>
                                                <input type="number" id="total_amount" name="total_amount"
                                                    class="form-control form-control-solid form-control-lg font-weight-bold text-success"
                                                    readonly
                                                    value="{{ old('total_amount', $engineering->total_amount ?? 0) }}"
                                                    step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>تاريخ تقديم الطلب <span class="text-danger">*</span></label>
                                                <input type="date" name="application_date" id="application_date"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('application_date', $engineering->application_date ? \Carbon\Carbon::parse($engineering->application_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>تاريخ الجلسة <span class="text-danger">*</span></label>
                                                <input type="date" name="session_date" id="session_date"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('session_date', $engineering->session_date ? \Carbon\Carbon::parse($engineering->session_date)->format('Y-m-d') : '') }}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>تاريخ تسجيل الملف <span class="text-danger">*</span></label>
                                                <input type="date" name="file_registered_at" id="file_registered_at"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('file_registered_at', $engineering->file_registered_at ? \Carbon\Carbon::parse($engineering->file_registered_at)->format('Y-m-d') : date('Y-m-d')) }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>تاريخ آخر دفعة <span class="text-danger">*</span></label>
                                                <input type="date" name="last_payment_date" id="last_payment_date"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('last_payment_date', $engineering->last_payment_date ? \Carbon\Carbon::parse($engineering->last_payment_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>مرفقات الملف (مخططات، مستندات... اختيارية)</label>
                                                <input type="file" name="attachments[]" class="form-control" multiple
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.zip,.rar">
                                                <small class="form-text text-muted">
                                                    يمكنك إرفاق أكثر من ملف، سيتم حفظها باسم رقم الملف + اسم المرفق.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="col-12">
                        <!-- القسم 4: ملاحظات واعتماد -->
                        <div class="mb-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-custom gutter-b shadow-sm border-0">
                                        <div class="card-header py-4 bg-light">
                                            <h3 class="card-title mb-0">
                                                <span class="card-label font-weight-bolder text-dark">4. أخرى</span>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-6">
                                                <label for="notes" class="font-weight-bolder">ملاحظات إضافية</label>
                                                <textarea name="notes" id="notes" class="form-control form-control-lg" rows="4"
                                                    placeholder="اكتب أي ملاحظة إضافية هنا...">{{ old('notes', $engineering->notes ?? '') }}</textarea>
                                            </div>
                                            <div class="alert alert-light-success d-flex align-items-start mb-0 py-4 px-4 px-md-5">
                                                <label class="checkbox checkbox-outline checkbox-success mb-0 mt-1 mr-3 mr-md-4">
                                                    <input type="checkbox" id="agreement" name="agreement" required
                                                        {{ $engineering->exists ? 'checked' : '' }} />
                                                    <span></span>
                                                </label>
                                                <div>
                                                    <label for="agreement" class="mb-0 font-weight-bolder text-dark">
                                                        أقر بأن جميع البيانات المدخلة أعلاه صحيحة.
                                                    </label>
                                                    <div class="text-muted font-size-sm mt-1">يجب تأكيد الإقرار قبل حفظ
                                                        الملف.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الحفظ -->
                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between mt-8 border-top pt-6">
                                <a href="{{ route('engineering.index') }}"
                                    class="btn btn-light-warning font-weight-bold px-8 mb-3 mb-md-0">
                                    <i class="fas fa-arrow-right"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-success font-weight-bold px-8">
                                    <i class="fas fa-check"></i>
                                    {{ $engineering->exists ? 'تحديث البيانات' : 'حفظ ملف الهندسة' }}
                                </button>
                            </div>
                        </div>
                        </div>
                </form>
            </div>
        </div>

        @if ($engineering->exists)
            <!-- Payments Section -->
            <div class="card card-custom gutter-b shadow-sm mt-5" id="payments-section">
                <div class="card-header border-0 py-6 bg-success">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-white">سجل المدفوعات</span>
                        <span class="text-white opacity-70 mt-3 font-weight-bold font-size-sm">
                            إدارة مدفوعات الملف
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-white font-weight-bold px-6" data-toggle="modal"
                            data-target="#addPaymentModal">
                            <i class="fas fa-plus text-success"></i> إضافة دفعة
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center table-borderless">
                            <thead>
                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                    <th>رقم الإيصال</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>تاريخ الدفع</th>
                                    <th>الرصيد المتبقي</th>
                                    <th>الملاحظات</th>
                                    <th>الحالة</th>
                                    <th>المستخدم</th>
                                    <th class="text-right">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($engineering->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_number ?? '-' }}</td>
                                        <td class="font-weight-bolder">{{ number_format($payment->amount, 2) }} ₪</td>
                                        <td>
                                            @if ($payment->payment_method == 'cash')
                                                <span
                                                    class="label label-inline label-light-success label-lg font-weight-bold">نقدي</span>
                                            @elseif($payment->payment_method == 'bank')
                                                <span
                                                    class="label label-inline label-light-primary label-lg font-weight-bold">تحويل
                                                    بنكي</span>
                                            @elseif($payment->payment_method == 'check')
                                                <span
                                                    class="label label-inline label-light-warning label-lg font-weight-bold">شيك</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td class="font-weight-bolder">{{ number_format($payment->balance_after, 2) }} ₪
                                        </td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                        <td>
                                            @if ($payment->status == 'active')
                                                <span
                                                    class="label label-inline label-light-success label-lg font-weight-bold">نشط</span>
                                            @else
                                                <span
                                                    class="label label-inline label-light-danger label-lg font-weight-bold">ملغي</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->user->name ?? '-' }}</td>
                                        <td class="text-right pr-0">
                                            <a href="{{ route('receipts.payments.engineering', $payment) }}" target="_blank"
                                                class="btn btn-icon btn-light-primary btn-sm mx-1" title="طباعة الإيصال">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if ($payment->status == 'active')
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                                    onclick="openCancelModal({{ $payment->id }})" title="إلغاء الدفعة">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد مدفوعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fee Histories Section -->
            <div class="card card-custom gutter-b shadow-sm mt-5">
                <div class="card-header border-0 py-6 bg-info">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-white">سجل التعديلات</span>
                        <span class="text-white opacity-70 mt-3 font-weight-bold font-size-sm">
                            تاريخ التعديلات على الملف
                        </span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center table-borderless">
                            <thead>
                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                    <th>نوع العملية</th>
                                    <th>مبلغ الرسم</th>
                                    <th>تاريخ العملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories ?? [] as $history)
                                    <tr>
                                        <td>
                                            @if ($history->operation_type == 'إضافة')
                                                <span
                                                    class="label label-inline label-light-success label-lg font-weight-bold">إضافة</span>
                                            @elseif($history->operation_type == 'تعديل')
                                                <span
                                                    class="label label-inline label-light-primary label-lg font-weight-bold">تعديل</span>
                                            @elseif($history->operation_type == 'تجديد سنوي')
                                                <span
                                                    class="label label-inline label-light-warning label-lg font-weight-bold">تجديد
                                                    سنوي</span>
                                            @endif
                                            {{ $history->operation_type }}
                                        </td>
                                        <td class="font-weight-bolder">{{ number_format($history->fee_amount, 2) }} ₪</td>
                                        <td>{{ $history->action_date->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">لا توجد تعديلات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if ($engineering->exists)
            <!-- Add Payment Modal -->
            <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog"
                aria-labelledby="addPaymentModalLabel" aria-hidden="true" data-id="{{ $engineering->id }}">
                <div class="modal-dialog" role="document">
                    <form id="addPaymentForm" action="{{ route('engineering.addPayment', $engineering->id) }}"
                        method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addPaymentModalLabel">إضافة دفعة مالية جديدة</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>المبلغ <span class="text-danger">*</span></label>
                                            <input type="number" name="amount" class="form-control form-control-solid"
                                                step="0.01" required min="0.01"
                                                max="{{ $engineering->remaining_amount }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>تاريخ الدفع <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date"
                                                class="form-control form-control-solid" value="{{ date('Y-m-d') }}"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>طريقة الدفع</label>
                                            <select name="payment_method" class="form-control form-control-solid">
                                                <option value="cash">نقدي</option>
                                                <option value="bank">حوالة بنكية</option>
                                                <option value="check">شيك</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>رقم المرجع (السند/الشيك)</label>
                                            <input type="text" name="reference_number"
                                                class="form-control form-control-solid">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>ملاحظات</label>
                                    <textarea name="notes" class="form-control form-control-solid" rows="2"></textarea>
                                </div>
                                <div class="alert alert-info">
                                    <strong>المبلغ المتبقي:</strong> {{ number_format($engineering->remaining_amount, 2) }}
                                    ₪
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary">حفظ الدفعة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cancellation Modal -->
            <div class="modal fade" id="cancelPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form id="cancelPaymentForm" action="#" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">إلغاء دفعة مالية</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="alert-text">هل أنت متأكد من إلغاء هذه الدفعة؟ سيتم إعادة حساب المديونية
                                        تلقائياً.</div>
                                </div>
                                <div class="form-group">
                                    <label>سبب الإلغاء <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3" required minlength="5"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning" data-dismiss="modal">تراجع</button>
                                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Prevent Metronic wizard widgets from hijacking this plain form */
        #engineeringForm .wizard-nav,
        #engineeringForm .wizard-steps,
        #engineeringForm .wizard-step,
        #engineeringForm .wizard-actions,
        #engineeringForm .wizard-aside {
            display: none !important;
        }

        #engineeringForm .wizard,
        #engineeringForm .wizard-body,
        #engineeringForm .wizard-content,
        #engineeringForm [data-wizard-type="step-content"],
        #engineeringForm [data-wizard-type="step"] {
            display: block !important;
            width: 100% !important;
            opacity: 1 !important;
            transform: none !important;
            position: static !important;
            visibility: visible !important;
        }

        #engineeringForm .card {
            border-radius: .75rem;
        }

        #engineeringForm .card-header {
            border-bottom: 1px solid #eef0f8;
        }

        #engineeringForm .form-group label {
            font-weight: 600;
            margin-bottom: .5rem;
        }

        #engineeringForm .form-control,
        #engineeringForm .bootstrap-select .dropdown-toggle {
            min-height: calc(1.5em + 1.3rem + 2px);
        }

        #engineeringForm .form-control.form-control-lg {
            font-size: .96rem;
        }

        #engineeringForm .row>[class*="col-"] {
            margin-bottom: .25rem;
        }

        #engineeringForm .checkbox-inline {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        #engineeringForm .checkbox-inline .checkbox {
            margin: 0;
        }

        @media (max-width: 991.98px) {
            #engineeringForm {
                padding: 1rem !important;
            }
        }

        .is-invalid+.invalid-feedback {
            display: block;
        }

        .bg-light-o-primary {
            background-color: rgba(54, 153, 255, 0.05);
        }

        .bg-light-o-success {
            background-color: rgba(27, 197, 189, 0.05);
        }

        .bg-light-o-warning {
            background-color: rgba(255, 168, 0, 0.05);
        }
    </style>
@endsection

@section('scripts')
    <script>
        function populateReview() {
            $('#rev-file-number').text($('#file_number').val() + '/' + $('#file_year').val());
            $('#rev-full-name').text(
                ($('#first_name').val() || '') + ' ' +
                ($('#father_name').val() || '') + ' ' +
                ($('#grandfather_name').val() || '') + ' ' +
                ($('#family_name').val() || '')
            );
            $('#rev-national-id').text($('#national_id').val() || '-');
            $('#rev-phone').text($('#phone_number').val() || '-');
            $('#rev-mobile').text($('#mobile_number').val() || '-');
            $('#rev-building-type').text($('#building_type').val() || '-');
            $('#rev-plot').text($('#plot_number').val() || '-');
            $('#rev-parcel').text($('#parcel_number').val() || '-');
            $('#rev-area').text($('#area_size').val() ? $('#area_size').val() + ' م²' : '-');
            $('#rev-neighborhood').text($('#neighborhood').val() || '-');
            $('#rev-address').text($('#address').val() || '-');
            $('#rev-gis-fees').text($('#gis_fees').val() ? parseFloat($('#gis_fees').val()).toFixed(2) + ' ₪' : '-');
            $('#rev-license-fees').text($('#license_fees').val() ? parseFloat($('#license_fees').val()).toFixed(2) + ' ₪' :
                '-');
            $('#rev-paid').text($('#paid_amount').val() ? parseFloat($('#paid_amount').val()).toFixed(2) + ' ₪' : '-');
            $('#rev-total').text($('#total_amount').val() ? parseFloat($('#total_amount').val()).toFixed(2) + ' ₪' : '-');
            $('#rev-remaining').text($('#remaining_amount').val() ? parseFloat($('#remaining_amount').val()).toFixed(2) +
                ' ₪' : '-');
        }

        function calculateFinancials() {
            const gisFees = parseFloat($('#gis_fees').val()) || 0;
            const penalties = parseFloat($('#penalties').val()) || 0;
            const licenseFees = parseFloat($('#license_fees').val()) || 0;
            const paid = parseFloat($('#paid_amount').val()) || 0;

            const total = gisFees + penalties + licenseFees;
            $('#total_amount').val(total.toFixed(2));

            const remaining = total - paid;
            $('#remaining_amount').val(remaining.toFixed(2));
        }

        // التحكم في ظهور/إخفاء القائمة الثانية بناءً على الاختيار الأول
        function handleSubdivisionTypeChange() {
            const subdivisionType = $('#subdivision_type').val();
            const subdivisionProjectSelect = $('#subdivision_project');
            const subdivisionProjectContainer = subdivisionProjectSelect.closest('.form-group');

            if (subdivisionType === 'مفرز') {
                // إظهار القائمة الثانية وتعيين قيمة اختيار فارغة
                subdivisionProjectContainer.show();
                subdivisionProjectSelect.show().val('');
                // جعل الحقل مطلوب عند اختيار "مفرز"
                subdivisionProjectSelect.prop('required', true);
            } else if (subdivisionType === 'غير مفرز') {
                // إخفاء القائمة الثانية وتعيين قيمة "غير مفرز"
                subdivisionProjectContainer.hide();
                subdivisionProjectSelect.hide().val('غير مفرز');
                // إزالة الحقل المطلوب عند اختيار "غير مفرز"
                subdivisionProjectSelect.prop('required', false);
            } else {
                // إخفاء عند عدم الاختيار
                subdivisionProjectContainer.hide();
                subdivisionProjectSelect.hide().val('');
                // جعل الحقل مطلوب افتراضياً
                subdivisionProjectSelect.prop('required', true);
            }
        }

        $(document).ready(function() {
            $('#gis_fees, #penalties, #license_fees, #paid_amount').on('input', function() {
                calculateFinancials();
            });

            calculateFinancials();

            // التحكم في قائمة الافراز عند تغييرها
            $('#subdivision_type').on('change', function() {
                handleSubdivisionTypeChange();
            });

            // تنفيذ التحكم عند تحميل الصفحة للمرة الأولى
            handleSubdivisionTypeChange();
            // تعبئة كرت المراجعة مبدئياً وعند أي تغيير مهم
            populateReview();
            $('#engineeringForm').on('input change', 'input, select, textarea', function() {
                populateReview();
            });

            // تعبئة الحقول من معلمات الرابط عند التحميل (مثل الرابط من صفحة المكلف)
            (function fillFromQuery() {
                var params = new URLSearchParams(window.location.search);
                var nationalId = params.get('national_id') || params.get('identity_number');
                var name = params.get('name');
                var mobile = params.get('mobile') || params.get('mobile_number');
                var address = params.get('address');
                if (name) try {
                    name = decodeURIComponent(name);
                } catch (e) {}
                if (address) try {
                    address = decodeURIComponent(address);
                } catch (e) {}
                if (nationalId) $('#national_id').val(nationalId);
                if (mobile) {
                    $('#mobile_number').val(mobile);
                    $('#phone_number').val(mobile);
                }
                if (name && name.trim()) {
                    var parts = name.trim().split(/\s+/);
                    $('#first_name').val(parts[0] || '');
                    $('#father_name').val(parts[1] || '');
                    $('#grandfather_name').val(parts[2] || '');
                    $('#family_name').val(parts[3] || parts[2] || '');
                }
            })();

            // استرجاع الاسم من API عند إدخال الرقم الوطني
            $('#national_id').on('blur', function() {
                const nationalId = ($(this).val() || '').toString().trim();
                console.log('National ID entered:', nationalId);
                console.log('National ID length:', nationalId.length);
                console.log('National ID type:', typeof nationalId);
                console.log('Is length 9?', nationalId.length === 9);
                if (nationalId.length === 9) {
                    $('#loading-name').show();
                    console.log('Fetching citizen data...');

                    $.ajax({
                        url: '{{ route('citizen.forMokalaf') }}',
                        method: 'GET',
                        timeout: 15000,
                        data: {
                            czn_id: nationalId
                        },
                        success: function(response) {
                            console.log('API Response:', response);
                            if (response && !response.error) {
                                const c = response;
                                let first = c.FNAME_ARB || c.first_name_ar || c.firstNameAr ||
                                    '';
                                let second = c.SNAME_ARB || c.second_name_ar || c
                                    .secondNameAr || '';
                                let third = c.TNAME_ARB || c.third_name_ar || c.thirdNameAr ||
                                    '';
                                let last = c.LNAME_ARB || c.last_name_ar || c.lastNameAr || '';
                                if (!first && !second && !third && !last) {
                                    const full = (c.FULLNAME || c.full_name_ar || c
                                        .fullNameAr || c.name_ar || c.name || '').trim();
                                    if (full) {
                                        const parts = full.split(/\s+/);
                                        first = parts[0] || '';
                                        second = parts[1] || '';
                                        third = parts[2] || '';
                                        last = parts[3] || '';
                                    }
                                }
                                $('#first_name').val(first);
                                $('#father_name').val(second);
                                $('#grandfather_name').val(third);
                                $('#family_name').val(last);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('API Error:', error);
                            console.error('Status:', status);
                            console.error('Response:', xhr.responseText);
                            console.error('XHR:', xhr);
                            if (status === 'timeout') {
                                toastr.error('انتهت مهلة الاتصال بخدمة المواطن. حاول مرة أخرى.');
                            } else {
                                toastr.error('لم يتم العثور على المواطن: ' + error);
                            }
                        },
                        complete: function() {
                            $('#loading-name').hide();
                        }
                    });
                }
            });

            // فتح مودال إلغاء الدفعة مع ضبط الـ action الصحيح
            window.openCancelModal = function(paymentId) {
                var $form = $('#cancelPaymentForm');
                // نولّد رابط الإلغاء مع قيمة افتراضية ثم نستبدلها بالـ ID الفعلي
                var actionTemplate =
                    "{{ route('engineering.cancelPayment', ['payment' => 'PAYMENT_ID_PLACEHOLDER']) }}";
                var action = actionTemplate.replace('PAYMENT_ID_PLACEHOLDER', paymentId);
                $form.attr('action', action);
                $('#cancelPaymentModal').modal('show');
            };

            // إرسال نموذج إضافة الدفعة عبر AJAX للبقاء في نفس الصفحة
            $('#addPaymentForm').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response && response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'تمت إضافة الدفعة بنجاح');
                            }
                            $('#addPaymentModal').modal('hide');
                            // إعادة تحميل الصفحة لتحديث جدول الدفعات مع البقاء على نفس الرابط
                            setTimeout(function() {
                                window.location.reload();
                            }, 600);
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'حدث خطأ أثناء حفظ الدفعة');
                            }
                        }
                    },
                    error: function(xhr) {
                        var msg = 'حدث خطأ أثناء حفظ الدفعة';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    }
                });
            });

            // إرسال نموذج إلغاء الدفعة عبر AJAX للبقاء في نفس الصفحة
            $('#cancelPaymentForm').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response && response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'تم إلغاء الدفعة بنجاح');
                            }
                            $('#cancelPaymentModal').modal('hide');
                            setTimeout(function() {
                                window.location.reload();
                            }, 600);
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'حدث خطأ أثناء إلغاء الدفعة');
                            }
                        }
                    },
                    error: function(xhr) {
                        var msg = 'حدث خطأ أثناء إلغاء الدفعة';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    }
                });
            });
        });
    </script>
@endsection
