@extends('layout.master')

@section('title')
    {{ __('Craft Licenses') }} - {{ __('Add License') }}
@endsection

@section('page_title')
    {{ __('Craft Licenses') }}
@endsection

@section('sub_main')
    {{ __('Craft Licenses') }}
@endsection

@section('sub_title')
    {{ __('Craft Licenses') }} - {{ __('Add License') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Craft Licenses') }} - {{ __('Add License') }}"
            subtitle="{{ __('Create a new craft license') }}"
        >
            <x-slot name="actions">
                <a href="{{ route('craft_licenses.index') }}" class="btn btn-light-primary font-weight-bolder">
                    <i class="fas fa-arrow-right"></i> {{ __('Back') }}
                </a>
            </x-slot>
        </x-page-header>

        <form action="{{ route('craft_licenses.store') }}" method="POST" id="createLicenseForm" enctype="multipart/form-data">
            @csrf

            {{-- الصف الأول: القسم 1 + القسم 2 --}}
            <div class="row">
                <div class="col-lg-6">
                    {{-- القسم 1: بيانات الملف الأساسية --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-primary">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">1. بيانات الملف الأساسية</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                {{-- حالة الملف ثابتة عند الإضافة (ترخيص جديد) ولا تظهر للمستخدم --}}
                                <div class="form-group col-md-4">
                                    <label>حالة الملف <span class="text-danger">*</span></label>
                                    <select name="status" id="status"
                                        class="form-control form-control-solid @error('status') is-invalid @enderror"
                                        required>
                                        <option value="">{{ __('اختر الحالة...') }}</option>
                                        <option value="closed" data-color="#F64E60"
                                            {{ old('status') == 'closed' ? 'selected' : '' }}>
                                            حرفة مغلق </option>
                                        <option value="new" data-color="#3699FF"
                                            {{ old('status') == 'new' ? 'selected' : '' }}>
                                            ترخيص جديد </option>
                                        <option value="renewal" data-color="#FFA800"
                                            {{ old('status') == 'renewal' ? 'selected' : '' }}>تجديد سنوي </option>
                                        <option value="undefined" data-color="#1BC5BD"
                                            {{ old('status') == 'undefined' ? 'selected' : '' }}>غير محدد </option>
                                        <option value="stopped" data-color="#F64E60"
                                            {{ old('status') == 'stopped' ? 'selected' : '' }}>حزمة متوقفة عن العمل

                                        </option>
                                        <option value="active_no_file" data-color="#8950FC"
                                            {{ old('status') == 'active_no_file' ? 'selected' : '' }}>حزمة قائمة بدون ملف
                                            </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الملف <span class="text-danger">*</span></label>
                                    <input type="text" name="file_number" id="file_number"
                                        class="form-control form-control-solid font-weight-bold @error('file_number') is-invalid @enderror"
                                        value="{{ old('file_number') }}" maxlength="6" required
                                        style="background-color: #fffac1; transition: background-color 0.3s ease;" >
                                    @error('file_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الجباية</label>
                                    <input type="text" name="tax_number" class="form-control form-control-solid"
                                        value="{{ old('tax_number') }}">
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label>تاريخ تسجيل الملف <span class="text-danger">*</span></label>
                                    <input type="date" name="registration_date"
                                        class="form-control form-control-solid @error('registration_date') is-invalid @enderror"
                                        value="{{ old('registration_date', now()->format('Y-m-d')) }}" required>
                                    @error('registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>سنة أول ترخيص</label>
                                    <input type="text" name="first_license_year" class="form-control form-control-solid"
                                        value="{{ old('first_license_year') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>آخر رخصة</label>
                                    <input type="text" name="last_license" class="form-control form-control-solid"
                                        value="{{ old('last_license') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- القسم 2: صاحب الحرفة ومالك العقار --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-info">
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
                                        value="{{ old('id_number') }}" maxlength="9">
                                    @error('id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-5">
                                    <label>اسم صاحب الحرفة</label>
                                    <input type="text" name="owner_name"
                                        class="form-control form-control-solid @error('owner_name') is-invalid @enderror"
                                        value="{{ old('owner_name') }}">
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الهاتف/الجوال</label>
                                    <input type="text" name="phone_number"
                                        class="form-control form-control-solid @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number') }}">
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
                                        value="{{ old('property_owner_id') }}" maxlength="9">
                                    @error('property_owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-5">
                                    <label>مالك العقار</label>
                                    <input type="text" name="property_owner"
                                        class="form-control form-control-solid @error('property_owner') is-invalid @enderror"
                                        value="{{ old('property_owner') }}">
                                    @error('property_owner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>جهة الملكية</label>
                                    <select name="ownership_entity" class="form-control form-control-solid">
                                        <option value="">{{ __('اختر...') }}</option>
                                        <option value="citizen"
                                            {{ old('ownership_entity') == 'citizen' ? 'selected' : '' }}>
                                            المواطن</option>
                                        <option value="municipality"
                                            {{ old('ownership_entity') == 'municipality' ? 'selected' : '' }}>بلدية جباليا
                                            النزلة
                                        </option>
                                        <option value="awqaf" {{ old('ownership_entity') == 'awqaf' ? 'selected' : '' }}>
                                            وزارة
                                            الأوقاف</option>
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
                        <div class="card-header py-4 bg-warning">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">3. تفاصيل الحرفة والموقع</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>تصنيف الحرفة <span class="text-danger">*</span></label>
                                    <select name="classification" id="classification"
                                        class="form-control form-control-solid selectpicker @error('classification') is-invalid @enderror"
                                        data-live-search="true" required>
                                        <option value="">{{ __('اختر التصنيف...') }}</option>
                                        <option value="مهنية" {{ old('classification') == 'مهنية' ? 'selected' : '' }}>1 - مهنية</option>
                                        <option value="مهنية مواصلات" {{ old('classification') == 'مهنية مواصلات' ? 'selected' : '' }}>2 - مهنية مواصلات</option>
                                        <option value="صناعية" {{ old('classification') == 'صناعية' ? 'selected' : '' }}>3 - صناعية</option>
                                        <option value="تجارية" {{ old('classification') == 'تجارية' ? 'selected' : '' }}>4 - تجارية</option>
                                        <option value="مطاعم/مطابخ" {{ old('classification') == 'مطاعم/مطابخ' ? 'selected' : '' }}>5 - مطاعم/مطابخ</option>
                                        <option value="مخابز" {{ old('classification') == 'مخابز' ? 'selected' : '' }}>6 - مخابز</option>
                                        <option value="محروقات" {{ old('classification') == 'محروقات' ? 'selected' : '' }}>7 - محروقات</option>
                                        <option value="صالات ومناسبات" {{ old('classification') == 'صالات ومناسبات' ? 'selected' : '' }}>8 - صالات ومناسبات</option>
                                        <option value="خدماتية" {{ old('classification') == 'خدماتية' ? 'selected' : '' }}>9 - خدماتية</option>
                                        <option value="غذائية A" {{ old('classification') == 'غذائية A' ? 'selected' : '' }}>10 - غذائية A</option>
                                        <option value="غذائية B" {{ old('classification') == 'غذائية B' ? 'selected' : '' }}>11 - غذائية B</option>
                                        <option value="تحلية مياه" {{ old('classification') == 'تحلية مياه' ? 'selected' : '' }}>12 - تحلية مياه</option>
                                        <option value="طبية" {{ old('classification') == 'طبية' ? 'selected' : '' }}>13 - طبية</option>
                                        <option value="زراعية" {{ old('classification') == 'زراعية' ? 'selected' : '' }}>14 - زراعية</option>
                                        <option value="مصانع غذائية" {{ old('classification') == 'مصانع غذائية' ? 'selected' : '' }}>15 - مصانع غذائية</option>
                                        <option value="يافطات و اعلانات" {{ old('classification') == 'يافطات و اعلانات' ? 'selected' : '' }}>16 - يافطات و اعلانات</option>
                                        <option value="مولدات" {{ old('classification') == 'مولدات' ? 'selected' : '' }}>17 - مولدات</option>
                                        <option value="مطاحن" {{ old('classification') == 'مطاحن' ? 'selected' : '' }}>0 - مطاحن</option>
                                    </select>
                                    @error('classification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group" style="display:none;">
                                    <label>نوع الحرفة <span class="text-danger">*</span></label>
                                    <input type="hidden" name="craft_type" id="craft_type" value="جاري">
                                    @error('craft_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>نوع الحرفة التفصيلي <span class="text-danger">*</span></label>
                                    <select name="detailed_craft_type" id="detailed_craft_type"
                                        class="form-control form-control-solid selectpicker @error('detailed_craft_type') is-invalid @enderror"
                                        data-live-search="true" required>
                                        <option value="">اختر التصنيف أولاً...</option>
                                    </select>
                                    @error('detailed_craft_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>الحي <span class="text-danger">*</span></label>
                                    <select name="neighborhood" class="form-control form-control-solid @error('neighborhood') is-invalid @enderror">
                                        <option value="">{{ __('اختر الحي...') }}</option>
                                        <option value="الكرامة" {{ old('neighborhood') == 'الكرامة' ? 'selected' : '' }}>الكرامة</option>
                                        <option value="عباد الرحمن" {{ old('neighborhood') == 'عباد الرحمن' ? 'selected' : '' }}>عباد الرحمن</option>
                                        <option value="النهضة" {{ old('neighborhood') == 'النهضة' ? 'selected' : '' }}>النهضة</option>
                                        <option value="الزهور" {{ old('neighborhood') == 'الزهور' ? 'selected' : '' }}>الزهور</option>
                                        <option value="الروضة" {{ old('neighborhood') == 'الروضة' ? 'selected' : '' }}>الروضة</option>
                                        <option value="النزهة" {{ old('neighborhood') == 'النزهة' ? 'selected' : '' }}>النزهة</option>
                                        <option value="السلام" {{ old('neighborhood') == 'السلام' ? 'selected' : '' }}>السلام</option>
                                        <option value="النور" {{ old('neighborhood') == 'النور' ? 'selected' : '' }}>النور</option>
                                        <option value="تل الزعتر" {{ old('neighborhood') == 'تل الزعتر' ? 'selected' : '' }}>تل الزعتر</option>
                                        <option value="معسكر جباليا" {{ old('neighborhood') == 'معسكر جباليا' ? 'selected' : '' }}>معسكر جباليا</option>
                                        <option value="البلدة القديمة" {{ old('neighborhood') == 'البلدة القديمة' ? 'selected' : '' }}>البلدة القديمة</option>
                                        <option value="المنطقة الشرقية" {{ old('neighborhood') == 'المنطقة الشرقية' ? 'selected' : '' }}>المنطقة الشرقية</option>
                                    </select>
                                    @error('neighborhood')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الشارع الرئيسي</label>
                                    <input type="text" name="main_street" class="form-control form-control-solid"
                                        value="{{ old('main_street') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>أقرب مسجد / معلم</label>
                                    <input type="text" name="nearest_mosque" class="form-control form-control-solid"
                                        value="{{ old('nearest_mosque') }}">

                                </div>
                                <div class="form-group col-md-3">
                                    <label>العنوان التفصيلي</label>
                                    <input type="text" name="address" class="form-control form-control-solid"
                                        value="{{ old('address') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>رقم المبنى (GIS)</label>
                                    <input type="text" name="gis_building_number"
                                        class="form-control form-control-solid" value="{{ old('gis_building_number') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رقم الشارع</label>
                                    <input type="text" name="street_number" class="form-control form-control-solid"
                                        value="{{ old('street_number') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الطابق</label>
                                    <input type="text" name="floor_number" class="form-control form-control-solid"
                                        value="{{ old('floor_number') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رقم الوحدة / المحل</label>
                                    <input type="text" name="unit_number" class="form-control form-control-solid"
                                        value="{{ old('unit_number') }}">
                                </div>
                            </div>
                            {{-- بيانات إضافية للرخصة --}}
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>أيام الإجازة</label>
                                    <select name="holiday_days" class="form-control form-control-solid">
                                        <option value="">اختر اليوم...</option>
                                        <option value="Friday"
                                            {{ old('holiday_days', 'Friday') == 'Friday' ? 'selected' : '' }}>الجمعة
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>المشغل المرخَّص</label>
                                    <input type="text" name="licensed_operator"
                                        class="form-control form-control-solid" value="{{ old('licensed_operator') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>هل الحرفة مرخَّصة؟</label>
                                    <select name="is_licensed" class="form-control form-control-solid">
                                        <option value="1" {{ old('is_licensed', '1') === '1' ? 'selected' : '' }}>نعم
                                        </option>
                                        <option value="0" {{ old('is_licensed') === '0' ? 'selected' : '' }}>لا
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>الاسم التجاري</label>
                                    <input type="text" name="commercial_name" class="form-control form-control-solid"
                                        value="{{ old('commercial_name') }}">
                                </div>
                            </div>
                            {{-- الشركاء --}}
                            <hr>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_partners">
                                    <label class="form-check-label font-weight-bold" for="has_partners">
                                        يوجد شركاء في الحرفة
                                    </label>
                                </div>
                                <small class="form-text text-muted">إذا كان هناك شركاء، يمكنك إضافة أرقام هوياتهم وسيتم جلب
                                    أسمائهم تلقائياً.</small>
                            </div>

                            <div id="partners_section" class="mt-4" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 font-weight-bold">بيانات الشركاء</h6>
                                    <button type="button" class="btn btn-sm btn-light-primary" id="add_partner_btn">
                                        <i class="fas fa-plus"></i> إضافة شريك
                                    </button>
                                </div>
                                <div id="partners_container"></div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-lg-6">
                    {{-- القسم 4: البيانات المالية والشركاء والمرفقات --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-danger">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">4. البيانات المالية والشركاء</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            {{-- الجزء المالي --}}
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>الرصيد المتبقي الحالي</label>
                                    <input type="text" id="remaining_display"
                                        class="form-control form-control-solid"
                                        value="{{ old('remaining', '0.00') }}" readonly>
                                    {{-- نرسل إجمالي الديون الحقيقي (ديون سابقة) في حقل مخفي، ويظل 0 في الترخيص الجديد --}}
                                    <input type="hidden" name="total_debts"
                                        value="{{ old('total_debts', 0) }}">
                                    @error('total_debts')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رسوم الحرفة <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="craft_fees" id="craft_fees"
                                        class="form-control form-control-solid @error('craft_fees') is-invalid @enderror"
                                        value="{{ old('craft_fees', 0) }}" required readonly>
                                    @error('craft_fees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label>رسوم اللوحة </label>
                                    <input type="number" step="0.01" name="signboard_fees"
                                        class="form-control form-control-solid @error('signboard_fees') is-invalid @enderror"
                                        value="{{ old('signboard_fees', 0) }}">
                                    @error('signboard_fees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label>نسبة الخصم %</label>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="discount_percent" id="discount_percent"
                                        class="form-control form-control-solid" value="{{ old('discount_percent', 0) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>مبلغ الخصم</label>
                                    <input type="text" id="discount_amount" class="form-control form-control-solid"
                                        value="0.00" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>إجمالي بعد الخصم</label>
                                    <input type="text" id="final_price_preview"
                                        class="form-control form-control-solid" value="0.00" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>قيمة الدفعة الآن</label>
                                    <input type="number" step="0.01" name="payment_amount"
                                        class="form-control form-control-solid @error('payment_amount') is-invalid @enderror"
                                        value="{{ old('payment_amount', 0) }}">
                                    @error('payment_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label>تاريخ آخر دفعة</label>
                                    <input type="date" name="last_payment_date"
                                        class="form-control form-control-solid" value="{{ old('last_payment_date') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label> السنوات المرصدة</label>
                                    <input type="number" name="remaining_years" min="0"
                                        class="form-control form-control-solid" value="{{ old('remaining_years') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>نوع اللوحة الإعلانية</label>
                                    <input type="text" name="advertisement_board"
                                        class="form-control form-control-solid" value="{{ old('advertisement_board') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>عدد المحلات</label>
                                    <input type="number" name="number_of_stores" min="0"
                                        class="form-control form-control-solid" value="{{ old('number_of_stores') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>طول اللوحة بالمتر</label>
                                    <input type="number" step="0.01" name="board_in_meters" min="0"
                                        class="form-control form-control-solid" value="{{ old('board_in_meters') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>الرصيد المتبقي</label>
                                    <input type="text" name="remaining" class="form-control form-control-solid"
                                        value="0.00" readonly>
                                </div>
                            </div>

                            {{-- المرفقات --}}
                            <hr>
                            <div class="form-group">
                                <label>المرفقات (اختياري)</label>
                                <input type="file" name="attachments[]" class="form-control form-control-solid"
                                    multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.zip,.rar">
                                <small class="form-text text-muted">يمكنك اختيار عدة ملفات متعلقة بملف الحرفة.</small>
                            </div>

                            {{-- الملاحظات --}}
                            <div class="form-group">
                                <label>ملاحظات</label>
                                <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('craft_licenses.index') }}"
                                class="btn btn-light-warning font-weight-bold px-10">
                                <i class="fas fa-arrow-right"></i> إلغاء
                            </a>
                            <button type="submit" id="saveBtn" class="btn btn-primary font-weight-bold px-10">
                                <i class="fas fa-check"></i> حفظ ملف الحرفة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @include('craft_licenses.partials.scripts', [
        'license' => new \App\Models\CraftLicense(),
        'next_file_number' => $next_file_number ?? '',
    ])

    <script>
        // ربط أحداث بسيطة ومباشرة بعد تحميل الصفحة
        jQuery(document).ready(function($) {
            // كل 200ms نتحقق من تغيير التصنيف يدوياً
            var lastClassification = $('#classification').val();
            setInterval(function() {
                var currentClassif = $('#classification').val();
                if (currentClassif !== lastClassification) {
                    lastClassification = currentClassif;
                    if (typeof loadDetailedCraftTypes === 'function') {
                        loadDetailedCraftTypes();
                    }
                    if (typeof validateForm === 'function') {
                        validateForm();
                    }
                    if (typeof calculateRemaining === 'function') {
                        calculateRemaining();
                    }
                }

                var currentDetailed = $('#detailed_craft_type').val();
                if (currentDetailed && currentDetailed !== '' && typeof updateFeesFromDetailedType === 'function') {
                    // للتحقق من تغيير النوع التفصيلي
                }
            }, 300);
        });

        // تغيير لون خلفية رقم الملف حسب الحالة المختارة
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const fileNumberInput = document.getElementById('file_number');

            if (statusSelect && fileNumberInput) {
                function updateFileNumberColor() {
                    const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                    if (selectedOption && selectedOption.dataset.color) {
                        fileNumberInput.style.backgroundColor = selectedOption.dataset.color;
                        fileNumberInput.style.color = '#ffffff';
                    } else {
                        fileNumberInput.style.backgroundColor = '#fffac1';
                        fileNumberInput.style.color = '#000000';
                    }
                }

                statusSelect.addEventListener('change', updateFileNumberColor);
                updateFileNumberColor();
            }
        });

        // حساب الخصم والقيم المالية (متوافق مع منطق CraftLicenseService::calculateFinancials)
        function calculateFinancialsWithDiscount() {
            const totalDebts = parseFloat(document.querySelector('input[name="total_debts"]')?.value) || 0;
            const craftFees = parseFloat(document.getElementById('craft_fees')?.value) || 0;
            const signboardFees = parseFloat(document.querySelector('input[name="signboard_fees"]')?.value) || 0;
            const discountPercent = parseFloat(document.getElementById('discount_percent')?.value) || 0;
            const paymentAmount = parseFloat(document.querySelector('input[name="payment_amount"]')?.value) || 0;

            // الخصم يُطبَّق فقط على رسوم السنة الحالية (رسوم الحرفة + رسوم اللوحة)
            const currentBase = craftFees + signboardFees;
            const discountAmount = currentBase * (discountPercent / 100);
            const currentAfterDiscount = currentBase - discountAmount;

            // إجمالي المستحق = الديون السابقة + رسوم السنة الحالية بعد الخصم
            const finalAfterDiscount = totalDebts + currentAfterDiscount;
            const remaining = finalAfterDiscount - paymentAmount;

            const discountAmountInput = document.getElementById('discount_amount');
            const finalPricePreviewInput = document.getElementById('final_price_preview');
            const remainingInput = document.querySelector('input[name="remaining"]');
            const remainingDisplay = document.getElementById('remaining_display');

            if (discountAmountInput) discountAmountInput.value = discountAmount.toFixed(2);
            if (finalPricePreviewInput) finalPricePreviewInput.value = finalAfterDiscount.toFixed(2);
            if (remainingInput) remainingInput.value = remaining.toFixed(2);
            if (remainingDisplay) remainingDisplay.value = remaining.toFixed(2);
        }

        // إدارة الشركاء
        function addPartnerRow() {
            const container = document.getElementById('partners_container');
            if (!container) return;

            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'form-row align-items-center mb-3';

            row.innerHTML = `
                <div class="form-group col-md-4 mb-0">
                    <label>رقم هوية الشريك</label>
                    <input type="text" name="partner_identity[]" class="form-control form-control-solid partner-identity" maxlength="9" placeholder="أدخل رقم الهوية">
                </div>
                <div class="form-group col-md-5 mb-0">
                    <label>اسم الشريك</label>
                    <input type="text" name="partner_name[]" class="form-control form-control-solid partner-name" placeholder="سيتم جلب الاسم تلقائياً" readonly>
                </div>
                <div class="form-group col-md-2 mb-0">
                    <button type="button" class="btn btn-light-danger btn-sm mt-7 remove-partner-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            container.appendChild(row);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ربط الأحداث المالية
            document.addEventListener('input', function(e) {
                const financialFields = ['total_debts', 'craft_fees', 'signboard_fees', 'discount_percent',
                    'payment_amount'
                ];
                if (financialFields.includes(e.target.name) || financialFields.includes(e.target.id)) {
                    calculateFinancialsWithDiscount();
                }
            });

            // تفعيل/إخفاء قسم الشركاء
            const hasPartnersCheckbox = document.getElementById('has_partners');
            const partnersSection = document.getElementById('partners_section');
            const addPartnerBtn = document.getElementById('add_partner_btn');

            if (hasPartnersCheckbox && partnersSection) {
                hasPartnersCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        partnersSection.style.display = 'block';
                        if (!document.querySelector('#partners_container .partner-identity')) {
                            addPartnerRow();
                        }
                    } else {
                        partnersSection.style.display = 'none';
                    }
                });
            }

            if (addPartnerBtn) {
                addPartnerBtn.addEventListener('click', function() {
                    addPartnerRow();
                });
            }

            // حذف شريك
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-partner-btn')) {
                    const row = e.target.closest('.form-row');
                    if (row) row.remove();
                }
            });

            // جلب بيانات الشريك من API الهوية
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('partner-identity')) {
                    const input = e.target;
                    const identity = input.value;

                    if (identity.length === 9) {
                        const row = input.closest('.form-row');
                        const nameInput = row.querySelector('.partner-name');

                        nameInput.value = 'جاري الجلب...';

                        $.ajax({
                            url: '{{ route('citizen.forMokalaf') }}',
                            method: 'GET',
                            data: {
                                czn_id: identity
                            },
                            success: function(response) {
                                if (response && !response.error) {
                                    var firstName = response.FNAME_ARB || response
                                        .first_name_ar || response.firstNameAr || response
                                        .first_name || '';
                                    var secondName = response.SNAME_ARB || response
                                        .second_name_ar || response.secondNameAr || response
                                        .second_name || '';
                                    var thirdName = response.TNAME_ARB || response
                                        .third_name_ar || response.thirdNameAr || response
                                        .third_name || '';
                                    var lastName = response.LNAME_ARB || response
                                        .last_name_ar || response.lastNameAr || response
                                        .last_name || '';
                                    var fullName = (firstName + ' ' + secondName + ' ' +
                                        thirdName + ' ' + lastName).trim();
                                    if (!fullName) {
                                        var fullNameAr = response.full_name_ar || response
                                            .fullNameAr || response.name_ar || response
                                            .nameAr || response.name || '';
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

            // حساب أولي عند تحميل الصفحة
            calculateFinancialsWithDiscount();

            // تعبئة الحقول تلقائياً من بارامترات الرابط (مثل رابط ملف المكلف)
            (function fillFromQuery() {
                try {
                    var params = new URLSearchParams(window.location.search);
                    var identity = params.get('identity_number') || params.get('czn_id');
                    var name = params.get('name');
                    var mobile = params.get('mobile') || params.get('mobile_number');
                    var address = params.get('address');

                    if (name) {
                        try { name = decodeURIComponent(name); } catch (e) {}
                    }
                    if (address) {
                        try { address = decodeURIComponent(address); } catch (e) {}
                    }

                    if (identity) {
                        var idInput = document.querySelector('input[name="id_number"]');
                        if (idInput && !idInput.value) {
                            idInput.value = identity;
                        }
                    }

                    if (mobile) {
                        var phoneInput = document.querySelector('input[name="phone_number"]');
                        if (phoneInput && !phoneInput.value) {
                            phoneInput.value = mobile;
                        }
                    }

                    if (name && name.trim()) {
                        var ownerInput = document.querySelector('input[name="owner_name"]');
                        if (ownerInput && !ownerInput.value) {
                            ownerInput.value = name.trim();
                        }
                    }

                    if (address && address !== '-' && address.trim()) {
                        var addressInput = document.querySelector('input[name="address"]');
                        if (addressInput && !addressInput.value) {
                            addressInput.value = address.trim();
                        }
                    }
                } catch (e) {
                    console && console.warn && console.warn('fillFromQuery error', e);
                }
            })();
        });
    </script>
@endsection
