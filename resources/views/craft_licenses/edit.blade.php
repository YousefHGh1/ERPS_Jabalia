@extends('layout.master')

@section('title')
     {{ __('Edit License') }} {{ $license->file_number }}
@endsection

@section('page_title')
    {{ __('Craft Licenses') }}
@endsection

@section('sub_title')
    {{ __('Edit License') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        <a href="{{ route('craft_licenses.index') }}" class="btn btn-light-warning mb-4">
            <i class="fas fa-arrow-right"></i> {{ __('Back to list') }}
        </a>
        <form action="{{ route('craft_licenses.update', $license->id) }}" method="POST" id="editLicenseForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- الصف الأول: القسم 1 + القسم 2 --}}
            <div class="row">
                <div class="col-lg-6">
                    {{-- القسم 1: بيانات الملف الأساسية + زر تغيير الحالة --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">
                                    <span class="card-label font-weight-bolder text-dark">1. بيانات الملف الأساسية</span>
                                </h3>
                                <span class="text-muted font-size-sm">رقم الملف: {{ $license->file_number }}</span>
                            </div>
                            <div class="text-left">
                                <div>
                                    <span class="text-muted mb-1">حالة الملف : </span>
                                    <span
                                        class="badge badge-pill @switch($license->status)
                                            @case('new') badge-primary @break
                                            @case('renewal') badge-success @break
                                            @case('renewal_pending') badge-warning @break
                                            @case('closed') badge-danger @break
                                            @case('stopped') badge-secondary @break
                                            @default badge-light
                                        @endswitch">
                                        @switch($license->status)
                                            @case('new')
                                                ترخيص جديد
                                            @break

                                            @case('renewal')
                                                تجديد سنوي
                                            @break

                                            @case('renewal_pending')
                                                غير مجدد
                                            @break

                                            @case('closed')
                                                حرفة مغلقة
                                            @break

                                            @case('stopped')
                                                حرفة متوقفة
                                            @break

                                            @default
                                                {{ $license->status }}
                                        @endswitch
                                    </span>
                                </div>
                                <button type="button" id="toggle-status-edit"
                                    class="btn btn-xs btn-light-primary mt-2 font-weight-bold">
                                    <i class="fas fa-pen"></i> تغيير الحالة
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="status-edit-wrapper" class="form-group mb-4 d-none">
                                <label>تغيير حالة الملف</label>
                                <select name="status" id="status"
                                    class="form-control form-control-solid @error('status') is-invalid @enderror">
                                    <option value="new"
                                        {{ old('status', $license->status) == 'new' ? 'selected' : '' }}>
                                        ترخيص
                                        جديد</option>
                                    <option value="renewal"
                                        {{ old('status', $license->status) == 'renewal' ? 'selected' : '' }}>تجديد سنوي
                                    </option>
                                    <option value="renewal_pending"
                                        {{ old('status', $license->status) == 'renewal_pending' ? 'selected' : '' }}>غير
                                        مجدد
                                    </option>
                                    <option value="undefined"
                                        {{ old('status', $license->status) == 'undefined' ? 'selected' : '' }}>غير محدد
                                    </option>
                                    <option value="active_no_file"
                                        {{ old('status', $license->status) == 'active_no_file' ? 'selected' : '' }}>حزمة
                                        قائمة
                                        بدون ملف</option>
                                    <option value="stopped"
                                        {{ old('status', $license->status) == 'stopped' ? 'selected' : '' }}>حرفة متوقفة عن
                                        العمل</option>
                                    <option value="closed"
                                        {{ old('status', $license->status) == 'closed' ? 'selected' : '' }}>حرفة مغلق
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    لا يمكن إغلاق أو إيقاف الحرفة إذا كان هناك مبالغ متبقية غير مدفوعة.
                                </small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رقم الملف</label>
                                    <input type="text" id="file_number"
                                        class="form-control form-control-solid font-weight-bold" readonly
                                        value="{{ $license->file_number }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الجباية</label>
                                    <input type="text" name="tax_number" class="form-control form-control-solid"
                                        value="{{ old('tax_number', $license->tax_number) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>تاريخ تسجيل الملف</label>
                                    <input type="date" name="registration_date"
                                        class="form-control form-control-solid @error('registration_date') is-invalid @enderror"
                                        value="{{ old('registration_date', optional($license->registration_date)->format('Y-m-d')) }}">
                                    @error('registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>سنة أول ترخيص</label>
                                    <input type="text" name="first_license_year" class="form-control form-control-solid"
                                        value="{{ old('first_license_year', $license->first_license_year) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>آخر رخصة</label>
                                    <input type="text" name="last_license" class="form-control form-control-solid"
                                        value="{{ old('last_license', $license->last_license) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- القسم 2: صاحب الحرفة ومالك العقار --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">2. صاحب الحرفة ومالك العقار</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">هوية صاحب الحرفة</label>
                                    <input type="text" name="id_number"
                                        class="form-control form-control-solid bg-light-info @error('id_number') is-invalid @enderror"
                                        value="{{ old('id_number', $license->id_number) }}" maxlength="9">
                                    @error('id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-5">
                                    <label>اسم صاحب الحرفة</label>
                                    <input type="text" name="owner_name"
                                        class="form-control form-control-solid @error('owner_name') is-invalid @enderror"
                                        value="{{ old('owner_name', $license->owner_name) }}">
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الهاتف/الجوال</label>
                                    <input type="text" name="phone_number"
                                        class="form-control form-control-solid @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number', $license->phone_number) }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">هوية مالك العقار</label>
                                    <input type="text" name="property_owner_id"
                                        class="form-control form-control-solid bg-light-info @error('property_owner_id') is-invalid @enderror"
                                        value="{{ old('property_owner_id', $license->property_owner_id) }}" maxlength="9">
                                    @error('property_owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-5">
                                    <label>مالك العقار</label>
                                    <input type="text" name="property_owner"
                                        class="form-control form-control-solid @error('property_owner') is-invalid @enderror"
                                        value="{{ old('property_owner', $license->property_owner) }}">
                                    @error('property_owner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>جهة الملكية</label>
                                    <select name="ownership_entity" class="form-control form-control-solid">
                                        <option value="">{{ __('اختر...') }}</option>
                                        <option value="citizen"
                                            {{ old('ownership_entity', $license->ownership_entity) == 'citizen' ? 'selected' : '' }}>
                                            المواطن</option>
                                        <option value="municipality"
                                            {{ old('ownership_entity', $license->ownership_entity) == 'municipality' ? 'selected' : '' }}>
                                            بلدية جباليا النزلة</option>
                                        <option value="awqaf"
                                            {{ old('ownership_entity', $license->ownership_entity) == 'awqaf' ? 'selected' : '' }}>
                                            وزارة الأوقاف</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الصف الثاني: القسم 3 + القسم 4 --}}
            <div class="row">
                <div class="col-lg-6">
                    {{-- القسم 3: تفاصيل الحرفة والموقع --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">3. تفاصيل الحرفة والموقع</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>تصنيف الحرفة <span class="text-danger">*</span></label>
                                    <select name="classification" id="classification"
                                        class="form-control form-control-solid selectpicker @error('classification') is-invalid @enderror"
                                        data-live-search="true" required>
                                        <option value="">{{ __('اختر التصنيف...') }}</option>
                                        <option value="مهنية" {{ old('classification', $license->classification) == 'مهنية' ? 'selected' : '' }}>1 - مهنية</option>
                                        <option value="مهنية مواصلات" {{ old('classification', $license->classification) == 'مهنية مواصلات' ? 'selected' : '' }}>2 - مهنية مواصلات</option>
                                        <option value="صناعية" {{ old('classification', $license->classification) == 'صناعية' ? 'selected' : '' }}>3 - صناعية</option>
                                        <option value="تجارية" {{ old('classification', $license->classification) == 'تجارية' ? 'selected' : '' }}>4 - تجارية</option>
                                        <option value="مطاعم/مطابخ" {{ old('classification', $license->classification) == 'مطاعم/مطابخ' ? 'selected' : '' }}>5 - مطاعم/مطابخ</option>
                                        <option value="مخابز" {{ old('classification', $license->classification) == 'مخابز' ? 'selected' : '' }}>6 - مخابز</option>
                                        <option value="محروقات" {{ old('classification', $license->classification) == 'محروقات' ? 'selected' : '' }}>7 - محروقات</option>
                                        <option value="صالات ومناسبات" {{ old('classification', $license->classification) == 'صالات ومناسبات' ? 'selected' : '' }}>8 - صالات ومناسبات</option>
                                        <option value="خدماتية" {{ old('classification', $license->classification) == 'خدماتية' ? 'selected' : '' }}>9 - خدماتية</option>
                                        <option value="غذائية A" {{ old('classification', $license->classification) == 'غذائية A' ? 'selected' : '' }}>10 - غذائية A</option>
                                        <option value="غذائية B" {{ old('classification', $license->classification) == 'غذائية B' ? 'selected' : '' }}>11 - غذائية B</option>
                                        <option value="تحلية مياه" {{ old('classification', $license->classification) == 'تحلية مياه' ? 'selected' : '' }}>12 - تحلية مياه</option>
                                        <option value="طبية" {{ old('classification', $license->classification) == 'طبية' ? 'selected' : '' }}>13 - طبية</option>
                                        <option value="زراعية" {{ old('classification', $license->classification) == 'زراعية' ? 'selected' : '' }}>14 - زراعية</option>
                                        <option value="مصانع غذائية" {{ old('classification', $license->classification) == 'مصانع غذائية' ? 'selected' : '' }}>15 - مصانع غذائية</option>
                                        <option value="يافطات و اعلانات" {{ old('classification', $license->classification) == 'يافطات و اعلانات' ? 'selected' : '' }}>16 - يافطات و اعلانات</option>
                                        <option value="مولدات" {{ old('classification', $license->classification) == 'مولدات' ? 'selected' : '' }}>17 - مولدات</option>
                                        <option value="مطاحن" {{ old('classification', $license->classification) == 'مطاحن' ? 'selected' : '' }}>0 - مطاحن</option>
                                    </select>
                                    @error('classification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3" style="display:none;">
                                    <label>نوع الحرفة <span class="text-danger">*</span></label>
                                    <input type="hidden" name="craft_type" id="craft_type" value="{{ old('craft_type', $license->craft_type ?? 'جاري') }}">
                                    @error('craft_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>نوع الحرفة التفصيلي <span class="text-danger">*</span></label>
                                    <select name="detailed_craft_type" id="detailed_craft_type"
                                        class="form-control form-control-solid selectpicker @error('detailed_craft_type') is-invalid @enderror"
                                        data-live-search="true" required>
                                        <option value="{{ old('detailed_craft_type', $license->detailed_craft_type) }}">
                                            {{ old('detailed_craft_type', $license->detailed_craft_type) ?: 'اختر التصنيف أولاً...' }}
                                        </option>
                                    </select>
                                    @error('detailed_craft_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>الحي</label>
                                    <input type="text" name="neighborhood" class="form-control form-control-solid"
                                        value="{{ old('neighborhood', $license->neighborhood) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الشارع الرئيسي</label>
                                    <input type="text" name="main_street" class="form-control form-control-solid"
                                        value="{{ old('main_street', $license->main_street) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>أقرب مسجد / معلم</label>
                                    <input type="text" name="nearest_mosque" class="form-control form-control-solid"
                                        value="{{ old('nearest_mosque', $license->nearest_mosque) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>العنوان التفصيلي</label>
                                    <input type="text" name="address" class="form-control form-control-solid"
                                        value="{{ old('address', $license->address) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>رقم المبنى (GIS)</label>
                                    <input type="text" name="gis_building_number"
                                        class="form-control form-control-solid"
                                        value="{{ old('gis_building_number', $license->gis_building_number) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رقم الشارع</label>
                                    <input type="text" name="street_number" class="form-control form-control-solid"
                                        value="{{ old('street_number', $license->street_number) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الطابق</label>
                                    <input type="text" name="floor_number" class="form-control form-control-solid"
                                        value="{{ old('floor_number', $license->floor_number) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رقم الوحدة / المحل</label>
                                    <input type="text" name="unit_number" class="form-control form-control-solid"
                                        value="{{ old('unit_number', $license->unit_number) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>حالة الترخيص</label>
                                    <select name="is_licensed" class="form-control form-control-solid">
                                        <option value="1"
                                            {{ old('is_licensed', (string) ($license->is_licensed ? 1 : 0)) === '1' ? 'selected' : '' }}>
                                            مرخّص</option>
                                        <option value="0"
                                            {{ old('is_licensed', (string) ($license->is_licensed ? 1 : 0)) === '0' ? 'selected' : '' }}>
                                            غير مرخّص</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>أيام العطلة</label>
                                    <select name="holiday_days" class="form-control form-control-solid">
                                        <option value="">اختر اليوم...</option>
                                        <option value="Saturday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Saturday' ? 'selected' : '' }}>
                                            السبت</option>
                                        <option value="Sunday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Sunday' ? 'selected' : '' }}>
                                            الأحد</option>
                                        <option value="Monday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Monday' ? 'selected' : '' }}>
                                            الاثنين</option>
                                        <option value="Tuesday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Tuesday' ? 'selected' : '' }}>
                                            الثلاثاء</option>
                                        <option value="Wednesday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Wednesday' ? 'selected' : '' }}>
                                            الأربعاء</option>
                                        <option value="Thursday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Thursday' ? 'selected' : '' }}>
                                            الخميس</option>
                                        <option value="Friday"
                                            {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Friday' ? 'selected' : '' }}>
                                            الجمعة</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>المشغل المرخَّص</label>
                                    <input type="text" name="licensed_operator"
                                        class="form-control form-control-solid"
                                        value="{{ old('licensed_operator', $license->licensed_operator ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الاسم التجاري</label>
                                    <input type="text" name="commercial_name" class="form-control form-control-solid"
                                        value="{{ old('commercial_name', $license->commercial_name ?? '') }}">
                                </div>
                            </div>
                            <hr>

                            <div class="form-group">
                                <label>ملاحظات</label>
                                <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes', $license->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- القسم 4: البيانات المالية وحالة الترخيص --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">4. البيانات المالية وحالة
                                    الترخيص</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            {{-- الجزء المالي (نفس حقول صفحة الإضافة) --}}
                            <div class="form-row">
                                {{-- <div class="form-group col-md-3">
                                    <label>إجمالي الديون</label>
                                    <input type="number" step="0.01" name="total_debts"
                                        class="form-control form-control-solid"
                                        value="{{ old('total_debts', $license->total_debts ?? 0) }}">
                                </div> --}}
                                <div class="form-group col-md-3">
                                    <label>رسوم الحرفة</label>
                                    <input type="number" name="craft_fees" id="craft_fees" step="0.01"
                                        class="form-control form-control-solid"
                                        value="{{ old('craft_fees', $license->craft_fees ?? 0) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رسوم اللوحة</label>
                                    <input type="number" name="signboard_fees" id="signboard_fees" step="0.01"
                                        class="form-control form-control-solid"
                                        value="{{ old('signboard_fees', $license->signboard_fees ?? 0) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>نسبة الخصم %</label>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="discount_percent" id="discount_percent"
                                        class="form-control form-control-solid"
                                        value="{{ old('discount_percent', $license->discount_percent ?? 0) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>مبلغ الخصم</label>
                                    <input type="text" id="discount_amount" class="form-control form-control-solid"
                                        value="0.00" readonly>
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group col-md-3">
                                    <label>إجمالي بعد الخصم</label>
                                    <input type="text" id="final_price_preview"
                                        class="form-control form-control-solid" value="0.00" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>المبلغ المدفوع (مجموع الدفعات)</label>
                                    <input type="number" step="0.01" name="payment_amount" readonly
                                        class="form-control form-control-solid bg-light"
                                        value="{{ old('payment_amount', $license->payment_amount ?? 0) }}"
                                        title="محسوب تلقائياً من جدول الدفعات">
                                    <small class="form-text text-muted">يُحدَّث من سجل الدفعات ولا يُعدّل يدوياً</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>تاريخ آخر دفعة</label>
                                    <input type="date" name="last_payment_date"
                                        class="form-control form-control-solid"
                                        value="{{ old('last_payment_date', optional($license->last_payment_date)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>السنوات المرصدة</label>
                                    <input type="number" name="remaining_years" min="0"
                                        class="form-control form-control-solid"
                                        value="{{ old('remaining_years', $license->remaining_years ?? 0) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>نوع اللوحة الإعلانية</label>
                                    <input type="text" name="advertisement_board"
                                        class="form-control form-control-solid"
                                        value="{{ old('advertisement_board', $license->advertisement_board ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>عدد المحلات</label>
                                    <input type="number" name="number_of_stores" min="0"
                                        class="form-control form-control-solid"
                                        value="{{ old('number_of_stores', $license->number_of_stores ?? 0) }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>طول اللوحة بالمتر</label>
                                    <input type="number" step="0.01" name="board_in_meters" min="0"
                                        class="form-control form-control-solid"
                                        value="{{ old('board_in_meters', $license->board_in_meters ?? 0) }}">
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-12">
                                    <label>الرصيد المتبقي</label>
                                    <input type="text" name="remaining" class="form-control form-control-solid"
                                        value="{{ old('remaining', $license->remaining ?? 0) }}" readonly>
                                    @if (($license->remaining ?? 0) < 0)
                                        <small class="form-text text-success">
                                            يوجد رصيد لصالح صاحب الحرفة بقيمة
                                            {{ number_format(abs($license->remaining), 2) }} ₪
                                        </small>
                                    @endif
                                </div>
                            </div>
                            {{-- المرفقات  (مثل صفحة الإضافة) --}}
                            <hr>
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">المرفقات الحالية</label>
                                @if ($license->attachments->count() > 0)
                                    <div class="mt-2">
                                        @foreach ($license->attachments as $attachment)
                                            <div
                                                class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                                <span class="text-dark">{{ $attachment->file_name }}</span>
                                                <div class="d-flex align-items-center">
                                                    @if ($attachment->file_path)
                                                        <a href="{{ route('craft_licenses.download-attachment', [$license->id, $attachment->id]) }}"
                                                            class="btn btn-sm btn-light-primary mr-2" target="_blank">
                                                            <i class="fas fa-download"></i> تحميل
                                                        </a>
                                                    @endif
                                                    <a href="javascript:;"
                                                        class="btn btn-sm btn-light-danger delete-attachment"
                                                        data-id="{{ $attachment->id }}"
                                                        data-license-id="{{ $license->id }}">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">لا توجد مرفقات حالية</span>
                                @endif
                            </div>
                            <div class="form-group"><label class="font-weight-bold text-dark">إضافة مرفقات جديدة</label>
                                <input type="file" name="attachments[]" class="form-control form-control-solid"
                                    multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.zip,.rar">
                                <small class="form-text text-muted">يمكنك إضافة مرفقات جديدة لملف الحرفة.</small></div>

                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('craft_licenses.show', $license->id) }}"
                                class="btn btn-light-info font-weight-bold px-10">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <button type="submit" id="saveBtn" class="btn btn-primary font-weight-bold px-10">
                                <i class="fas fa-check"></i> حفظ التعديلات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- قسم المدفوعات --}}
        <div class="card card-custom gutter-b shadow-none border mt-5" id="payments-section">
            <div class="card-header py-3 bg-success d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 font-weight-bold text-white">المدفوعات على هذه الرخصة</h5>
                    <span class="text-white-50 font-size-sm">إدارة الدفعات وإلغاء الدفعات مع توثيق السبب</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-white font-weight-bold px-6" data-toggle="modal"
                        data-target="#addPaymentModal">
                        <i class="fas fa-plus text-success"></i> إضافة دفعة
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center mb-0">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th>التاريخ</th>
                                <th>المبلغ</th>
                                {{-- <th>الرصيد بعد الدفع</th> --}}
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>المستخدم</th>
                                <th>الملاحظات</th>
                                <th class="text-right">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // نحسب الرصيد المستحق بعد الخصم من بيانات الترخيص الحالية
                                $baseTotal =
                                    ($license->total_debts ?? 0) +
                                    ($license->craft_fees ?? 0) +
                                    ($license->signboard_fees ?? 0);
                                $discountPercent = (float) ($license->discount_percent ?? 0);
                                $discountAmount = $discountPercent > 0 ? $baseTotal * ($discountPercent / 100) : 0;
                                $baseAfterDiscount = $baseTotal - $discountAmount;
                                $paidSoFar = 0;
                            @endphp
                            @forelse ($license->payments as $payment)
                                @php
                                    if ($payment->status === 'active') {
                                        $paidSoFar += $payment->amount;
                                        $rowBalanceAfter = $baseAfterDiscount - $paidSoFar;
                                    } else {
                                        $rowBalanceAfter = null;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ optional($payment->payment_date)->format('Y-m-d') ?? '-' }}</td>
                                    <td class="font-weight-bolder">{{ number_format($payment->amount, 2) }} ₪</td>
                                    {{-- <td class="font-weight-bolder">
                                        @if ($rowBalanceAfter === null)
                                            -
                                        @else
                                            {{ number_format($rowBalanceAfter, 2) }} ₪
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if ($payment->payment_method == 'cash')
                                            <span
                                                class="label label-inline label-light-success label-lg font-weight-bold">نقدي</span>
                                        @elseif($payment->payment_method == 'bank')
                                            <span
                                                class="label label-inline label-light-primary label-lg font-weight-bold">حوالة
                                                بنكية</span>
                                        @elseif($payment->payment_method == 'check')
                                            <span
                                                class="label label-inline label-light-warning label-lg font-weight-bold">شيك</span>
                                        @else
                                            <span class="label label-inline label-light label-lg font-weight-bold">غير
                                                محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->status === 'active')
                                            <span
                                                class="label label-inline label-light-success label-lg font-weight-bold">نشط</span>
                                        @else
                                            <span
                                                class="label label-inline label-light-danger label-lg font-weight-bold">ملغي</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($payment->user)->name ?? '-' }}</td>
                                    <td>
                                        {{ $payment->notes ?? '-' }}
                                        @if ($payment->status === 'cancelled' && $payment->cancellation_reason)
                                            <br>
                                            <small class="text-danger">سبب الإلغاء:
                                                {{ $payment->cancellation_reason }}</small>
                                        @endif
                                    </td>
                                    <td class="text-right pr-0">
                                        <a href="{{ route('receipts.payments.craft', $payment) }}" target="_blank"
                                            class="btn btn-icon btn-light-primary btn-sm mx-1" title="طباعة الإيصال">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if ($payment->status === 'active')
                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                                onclick="openCancelPaymentModal({{ $payment->id }})"
                                                title="إلغاء الدفعة">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-6">لا توجد مدفوعات مسجلة لهذه الرخصة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- مودال إضافة دفعة جديدة --}}
        <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog"
            aria-labelledby="addPaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="addPaymentForm" action="{{ route('craft_licenses.add_payment', $license->id) }}"
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
                                            step="0.01" required min="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>تاريخ الدفع <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" class="form-control form-control-solid"
                                            value="{{ date('Y-m-d') }}" required>
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
                                <strong>الرصيد المتبقي الحالي:</strong>
                                {{ number_format($license->remaining ?? 0, 2) }} ₪
                                @if (($license->remaining ?? 0) < 0)
                                    <br>
                                    <span class="text-success">يوجد رصيد لصالح صاحب الحرفة، ويمكن تسجيل دفعات إضافية كرصيد
                                        لصالحه.</span>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ الدفعة</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- مودال إلغاء دفعة --}}
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
                                <div class="alert-text">
                                    هل أنت متأكد من إلغاء هذه الدفعة؟ سيتم إعادة حساب المديونية تلقائياً، وسيتم حفظ سبب
                                    الإلغاء في السجل.
                                </div>
                            </div>
                            <div class="form-group">
                                <label>سبب الإلغاء <span class="text-danger">*</span></label>
                                <textarea name="cancellation_reason" class="form-control" rows="3" required minlength="5"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">تراجع</button>
                            <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- الشركاء --}}
        <div class="card card-custom gutter-b shadow-none border">
            <div class="card-header py-3 bg-info d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 font-weight-bold text-white">شركاء الحرفة</h5>
                    <span class="text-white-50 font-size-sm">إدارة الشركاء (إضافة / حذف) مثل نظام الدفعات</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-white font-weight-bold px-6" data-toggle="modal"
                        data-target="#addPartnerModal">
                        <i class="fas fa-user text-info"></i> إضافة شريك
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center mb-0">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th>رقم الهوية</th>
                                <th>اسم الشريك</th>
                                <th>الحالة</th>
                                <th>سبب الإلغاء</th>
                                <th class="text-right">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($license->partners as $partner)
                                <tr>
                                    <td>{{ $partner->identity_number }}</td>
                                    <td>{{ $partner->name ?? '-' }}</td>
                                    <td>
                                        @if ($partner->status === 'cancelled')
                                            <span
                                                class="label label-inline label-light-danger label-lg font-weight-bold">ملغي</span>
                                        @else
                                            <span
                                                class="label label-inline label-light-success label-lg font-weight-bold">نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->status === 'cancelled' && $partner->cancellation_reason)
                                            <small class="text-danger">{{ $partner->cancellation_reason }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right pr-0">
                                        @if ($partner->status === 'active')
                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                                onclick="openCancelPartnerModal({{ $partner->id }})"
                                                title="إلغاء / حذف الشريك">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6">لا يوجد شركاء مسجلين لهذه الحرفة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- مودال إضافة شريك --}}
        <div class="modal fade" id="addPartnerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="addPartnerForm" action="{{ route('craft_licenses.add_partner', $license->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">إضافة شريك جديد للحرفة</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>رقم هوية الشريك <span class="text-danger">*</span></label>
                                <input type="text" name="identity_number"
                                    class="form-control form-control-solid partner-identity" maxlength="9" required
                                    placeholder="أدخل رقم الهوية">
                            </div>
                            <div class="form-group">
                                <label>اسم الشريك</label>
                                <input type="text" name="name" class="form-control form-control-solid partner-name"
                                    placeholder="سيتم جلب الاسم تلقائياً بعد إدخال الهوية (إن توفرت خدمة الهوية)">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ الشريك</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- مودال إلغاء شريك --}}
        <div class="modal fade" id="cancelPartnerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="cancelPartnerForm" action="#" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">إلغاء شريك من الحرفة</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="alert-text">
                                    هل أنت متأكد من إلغاء هذا الشريك؟ سيتم الاحتفاظ به في السجل مع سبب الإلغاء.
                                </div>
                            </div>
                            <div class="form-group">
                                <label>سبب الإلغاء <span class="text-danger">*</span></label>
                                <textarea name="cancellation_reason" class="form-control" rows="3" required minlength="5"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">تراجع</button>
                            <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
@endsection

@section('scripts')
    @include('craft_licenses.partials.scripts', [
        'license' => $license,
        'next_file_number' => '',
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.getElementById('toggle-status-edit');
            var wrapper = document.getElementById('status-edit-wrapper');
            if (toggleBtn && wrapper) {
                toggleBtn.addEventListener('click', function() {
                    wrapper.classList.toggle('d-none');
                });
            }

            // تحميل الأنواع التفصيلية عند فتح صفحة التعديل
            var classificationEl = document.getElementById('classification');
            if (classificationEl && classificationEl.value) {
                var currentDetailed = "{{ $license->detailed_craft_type ?? '' }}";
                setTimeout(function() {
                    if (typeof loadDetailedCraftTypes === 'function') {
                        loadDetailedCraftTypes(currentDetailed);
                    }
                }, 500);
            }
        });

        function openCancelPaymentModal(paymentId) {
            var form = document.getElementById('cancelPaymentForm');
            if (!form) return;

            var actionTemplate =
                '{{ route('craft_licenses.cancel_payment', ['payment' => '__PAYMENT_ID__']) }}';
            form.action = actionTemplate.replace('__PAYMENT_ID__', paymentId);

            $('#cancelPaymentModal').modal('show');
        }

        $(document).ready(function() {
            // حذف مرفق من رخصة الحرفة
            $('.delete-attachment').click(function() {
                var attachmentId = $(this).data('id');
                var licenseId = $(this).data('license-id');
                if (confirm('هل أنت متأكد من حذف هذا المرفق؟')) {
                    $.ajax({
                        url: '{{ route('craft_licenses.delete-attachment', [':license', ':attachment']) }}'
                            .replace(':license', licenseId)
                            .replace(':attachment', attachmentId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('حدث خطأ أثناء حذف المرفق');
                        }
                    });
                }
            });
        });

        // جلب اسم الشريك تلقائياً من خدمة الهوية عند إدخال رقم الهوية (في جدول الشركاء أو المودال)
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('partner-identity')) {
                const input = e.target;
                const identity = input.value;

                if (identity.length === 9) {
                    // المودال: حقل الاسم في form-group شقيق وليس داخل نفس form-group، نستخدم .modal-body
                    let container = input.closest('.modal-body');
                    if (!container) {
                        container = input.closest('.form-row');
                    }
                    if (!container) {
                        container = input.closest('tr');
                    }
                    const nameInput = container ? container.querySelector('.partner-name') : null;
                    if (!nameInput) return;

                    nameInput.value = 'جاري الجلب...';

                    $.ajax({
                        url: '{{ route('citizen.forMokalaf') }}',
                        method: 'GET',
                        data: {
                            czn_id: identity
                        },
                        success: function(response) {
                            if (response && !response.error) {
                                var firstName = response.FNAME_ARB || response.first_name_ar || response
                                    .firstNameAr || response.first_name || '';
                                var secondName = response.SNAME_ARB || response.second_name_ar ||
                                    response.secondNameAr || response.second_name || '';
                                var thirdName = response.TNAME_ARB || response.third_name_ar || response
                                    .thirdNameAr || response.third_name || '';
                                var lastName = response.LNAME_ARB || response.last_name_ar || response
                                    .lastNameAr || response.last_name || '';
                                var fullName = (firstName + ' ' + secondName + ' ' + thirdName + ' ' +
                                    lastName).trim();
                                if (!fullName) {
                                    var fullNameAr = response.full_name_ar || response.fullNameAr ||
                                        response.name_ar || response.nameAr || response.name || '';
                                    fullName = fullNameAr || '';
                                }
                                nameInput.value = fullName || 'لم يتم العثور على الاسم';
                            } else {
                                nameInput.value = 'لم يتم العثور على بيانات';
                            }
                        },
                        error: function() {
                            nameInput.value = 'خطأ في جلب البيانات';
                        }
                    });
                }
            }
        });

        function openCancelPartnerModal(partnerId) {
            var form = document.getElementById('cancelPartnerForm');
            if (!form) return;

            var actionTemplate =
                '{{ route('craft_licenses.delete_partner', ['craft_license' => $license->id, 'partner' => '__PARTNER_ID__']) }}';
            form.action = actionTemplate.replace('__PARTNER_ID__', partnerId);

            $('#cancelPartnerModal').modal('show');
        }
    </script>
@endsection
