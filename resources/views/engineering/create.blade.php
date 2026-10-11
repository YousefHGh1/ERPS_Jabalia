@extends('layout.master')

@section('title')
    {{ __('Engineering Files') }} - {{ __('Add File') }}
@endsection

@section('page_title')
    {{ __('Engineering Files') }}
@endsection

@section('sub_main')
    {{ __('Engineering Files') }}
@endsection

@section('sub_title')
    {{ __('Engineering Files') }} - {{ __('Add File') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Engineering Files') }} - {{ __('Add File') }}"
            subtitle="{{ __('Create a new engineering file') }}"
        >
            <x-slot name="actions">
                <a href="{{ route('engineering.index') }}" class="btn btn-light-primary font-weight-bolder">
                    <i class="fas fa-arrow-right"></i> {{ __('Back') }}
                </a>
            </x-slot>
        </x-page-header>

        <form action="{{ route('engineering.store') }}" method="POST" id="createEngineeringForm">
            @csrf

            {{-- الصف الأول: القسم 1 + القسم 2 --}}
            <div class="row">
                <div class="col-lg-6">
                    {{-- القسم 1: بيانات الملف --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">1. بيانات الملف</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رقم الملف <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="file_number"
                                           id="file_number"
                                           class="form-control form-control-solid font-weight-bold @error('file_number') is-invalid @enderror"
                                           value="{{ old('file_number') }}"
                                           required>
                                    @error('file_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>تاريخ التسجيل <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="registration_date"
                                           class="form-control form-control-solid @error('registration_date') is-invalid @enderror"
                                           value="{{ old('registration_date', now()->format('Y-m-d')) }}"
                                           required>
                                    @error('registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>نوع المعاملة <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="transaction_type"
                                           class="form-control form-control-solid @error('transaction_type') is-invalid @enderror"
                                           value="{{ old('transaction_type') }}"
                                           required>
                                    @error('transaction_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>حالة الملف <span class="text-danger">*</span></label>
                                    <select name="status"
                                            class="form-control form-control-solid @error('status') is-invalid @enderror"
                                            required>
                                        <option value="">{{ __('اختر الحالة...') }}</option>
                                        <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>جديد</option>
                                        <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>قيد الدراسة</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- القسم 2: بيانات المواطن --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">2. بيانات المواطن</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رقم الهوية <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="national_id"
                                           class="form-control form-control-solid @error('national_id') is-invalid @enderror"
                                           value="{{ old('national_id') }}"
                                           maxlength="9"
                                           required>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-8">
                                    <label>اسم المواطن <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="citizen_name"
                                           class="form-control form-control-solid @error('citizen_name') is-invalid @enderror"
                                           value="{{ old('citizen_name') }}"
                                           required>
                                    @error('citizen_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رقم الهاتف</label>
                                    <input type="text"
                                           name="phone"
                                           class="form-control form-control-solid @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-8">
                                    <label>العنوان</label>
                                    <input type="text"
                                           name="address"
                                           class="form-control form-control-solid @error('address') is-invalid @enderror"
                                           value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الصف الثاني: القسم 3 + القسم 4 --}}
            <div class="row">
                <div class="col-lg-6">
                    {{-- القسم 3: بيانات الأرض --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">3. بيانات الأرض</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رقم القطعة <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="parcel_number"
                                           class="form-control form-control-solid @error('parcel_number') is-invalid @enderror"
                                           value="{{ old('parcel_number') }}"
                                           required>
                                    @error('parcel_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>رقم الحوض <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="block_number"
                                           class="form-control form-control-solid @error('block_number') is-invalid @enderror"
                                           value="{{ old('block_number') }}"
                                           required>
                                    @error('block_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>المساحة (م²) <span class="text-danger">*</span></label>
                                    <input type="number"
                                           step="0.01"
                                           name="area"
                                           class="form-control form-control-solid @error('area') is-invalid @enderror"
                                           value="{{ old('area') }}"
                                           required>
                                    @error('area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>الحي</label>
                                    <input type="text"
                                           name="neighborhood"
                                           class="form-control form-control-solid @error('neighborhood') is-invalid @enderror"
                                           value="{{ old('neighborhood') }}">
                                    @error('neighborhood')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>الشارع</label>
                                    <input type="text"
                                           name="street"
                                           class="form-control form-control-solid @error('street') is-invalid @enderror"
                                           value="{{ old('street') }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- القسم 4: البيانات المالية --}}
                    <div class="card card-custom gutter-b shadow-sm border-0 h-100">
                        <div class="card-header py-4 bg-light">
                            <h3 class="card-title mb-0">
                                <span class="card-label font-weight-bolder text-dark">4. البيانات المالية</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>رسوم المعاملة <span class="text-danger">*</span></label>
                                    <input type="number"
                                           step="0.01"
                                           name="fees"
                                           id="fees"
                                           class="form-control form-control-solid @error('fees') is-invalid @enderror"
                                           value="{{ old('fees', 0) }}"
                                           required>
                                    @error('fees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>نسبة الخصم (%)</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           name="discount_percent"
                                           id="discount_percent"
                                           class="form-control form-control-solid"
                                           value="{{ old('discount_percent', 0) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>قيمة الخصم</label>
                                    <input type="text"
                                           id="discount_amount"
                                           class="form-control form-control-solid"
                                           value="{{ old('discount_amount', 0) }}"
                                           readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>الإجمالي بعد الخصم</label>
                                    <input type="text"
                                           id="total_after_discount"
                                           class="form-control form-control-solid"
                                           value="{{ old('total_after_discount', 0) }}"
                                           readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>الدفعة الآن</label>
                                    <input type="number"
                                           step="0.01"
                                           name="payment_now"
                                           id="payment_now"
                                           class="form-control form-control-solid @error('payment_now') is-invalid @enderror"
                                           value="{{ old('payment_now', 0) }}">
                                    @error('payment_now')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>الرصيد المتبقي</label>
                                    <input type="text"
                                           name="remaining_balance"
                                           id="remaining_balance"
                                           class="form-control form-control-solid"
                                           value="{{ old('remaining_balance', 0) }}"
                                           readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('engineering.index') }}"
                               class="btn btn-light-warning font-weight-bold px-10">
                                <i class="fas fa-arrow-right"></i> إلغاء
                            </a>
                            <button type="submit"
                                    class="btn btn-primary font-weight-bold px-10">
                                <i class="fas fa-check"></i> حفظ الملف الهندسي
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

