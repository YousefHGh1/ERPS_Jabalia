@extends('layout.master')

@section('title')
    {{ __('Engineering Files') }} - {{ __('Edit File') }}
@endsection

@section('page_title')
    {{ __('Engineering Files') }}
@endsection

@section('sub_title')
    {{ __('Engineering Files') }} - {{ __('Edit File') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="تعديل ملف هندسي"
            >
                <x-slot name="actions">
                    <a href="{{ route('engineering.print', $engineering->id) }}"
                                           class="btn btn-primary font-weight-bolder font-size-sm" target="_blank">
                                            <i class="fas fa-print"></i> طباعة الملف
                                        </a>
                                        <a href="{{ route('engineering.index') }}"
                                           class="btn btn-light-primary font-weight-bolder font-size-sm ml-2">
                                            <i class="fas fa-arrow-right"></i> عودة
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm">
            <div class="card-body">
                <form action="{{ route('engineering.update', $engineering->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- الصف الأول: بيانات الملف + بيانات المواطن --}}
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
                                                   class="form-control form-control-solid font-weight-bold @error('file_number') is-invalid @enderror"
                                                   value="{{ old('file_number', $engineering->file_number) }}"
                                                   required>
                                            @error('file_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>تاريخ التسجيل <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   name="file_registered_at"
                                                   class="form-control form-control-solid @error('file_registered_at') is-invalid @enderror"
                                                   value="{{ old('file_registered_at', optional($engineering->file_registered_at)->format('Y-m-d')) }}"
                                                   required>
                                            @error('file_registered_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>سنة الملف <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="file_year"
                                                   class="form-control form-control-solid @error('file_year') is-invalid @enderror"
                                                   value="{{ old('file_year', $engineering->file_year) }}"
                                                   required>
                                            @error('file_year')
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
                                                   value="{{ old('national_id', $engineering->national_id) }}"
                                                   maxlength="9"
                                                   required>
                                            @error('national_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label>الاسم الكامل <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control form-control-solid"
                                                   value="{{ trim($engineering->first_name.' '.$engineering->father_name.' '.($engineering->grandfather_name ?? '').' '.$engineering->family_name) }}"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>رقم الهاتف <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="phone_number"
                                                   class="form-control form-control-solid @error('phone_number') is-invalid @enderror"
                                                   value="{{ old('phone_number', $engineering->phone_number) }}"
                                                   required>
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>رقم الموبايل <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="mobile_number"
                                                   class="form-control form-control-solid @error('mobile_number') is-invalid @enderror"
                                                   value="{{ old('mobile_number', $engineering->mobile_number) }}"
                                                   required>
                                            @error('mobile_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>الحي</label>
                                            <input type="text"
                                                   name="neighborhood"
                                                   class="form-control form-control-solid @error('neighborhood') is-invalid @enderror"
                                                   value="{{ old('neighborhood', $engineering->neighborhood) }}">
                                            @error('neighborhood')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- الصف الثاني: بيانات الأرض + البيانات المالية --}}
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
                                                   value="{{ old('parcel_number', $engineering->parcel_number) }}"
                                                   required>
                                            @error('parcel_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>رقم الحوض <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="division_number"
                                                   class="form-control form-control-solid @error('division_number') is-invalid @enderror"
                                                   value="{{ old('division_number', $engineering->division_number) }}"
                                                   required>
                                            @error('division_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>المساحة (م²) <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="area_size"
                                                   class="form-control form-control-solid @error('area_size') is-invalid @enderror"
                                                   value="{{ old('area_size', $engineering->area_size) }}"
                                                   required>
                                            @error('area_size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>العنوان <span class="text-danger">*</span></label>
                                            <textarea name="address"
                                                      class="form-control form-control-solid @error('address') is-invalid @enderror"
                                                      rows="2"
                                                      required>{{ old('address', $engineering->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>نوع البناء <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="building_type"
                                                   class="form-control form-control-solid @error('building_type') is-invalid @enderror"
                                                   value="{{ old('building_type', $engineering->building_type) }}"
                                                   required>
                                            @error('building_type')
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
                                            <label>رسوم GIS <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="gis_fees"
                                                   class="form-control form-control-solid @error('gis_fees') is-invalid @enderror"
                                                   value="{{ old('gis_fees', $engineering->gis_fees) }}"
                                                   required>
                                            @error('gis_fees')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>الغرامات</label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="penalties"
                                                   class="form-control form-control-solid @error('penalties') is-invalid @enderror"
                                                   value="{{ old('penalties', $engineering->penalties) }}">
                                            @error('penalties')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>رسوم الترخيص <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="license_fees"
                                                   class="form-control form-control-solid @error('license_fees') is-invalid @enderror"
                                                   value="{{ old('license_fees', $engineering->license_fees) }}"
                                                   required>
                                            @error('license_fees')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>الإجمالي</label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="total_amount"
                                                   class="form-control form-control-solid @error('total_amount') is-invalid @enderror"
                                                   value="{{ old('total_amount', $engineering->total_amount) }}"
                                                   required>
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>المدفوع <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="paid_amount"
                                                   class="form-control form-control-solid @error('paid_amount') is-invalid @enderror"
                                                   value="{{ old('paid_amount', $engineering->paid_amount) }}"
                                                   required>
                                            @error('paid_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>المتبقي</label>
                                            <input type="number"
                                                   step="0.01"
                                                   name="remaining_amount"
                                                   class="form-control form-control-solid @error('remaining_amount') is-invalid @enderror"
                                                   value="{{ old('remaining_amount', $engineering->remaining_amount) }}"
                                                   required>
                                            @error('remaining_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <a href="{{ route('engineering.index') }}"
                                       class="btn btn-light-warning font-weight-bold px-10">
                                        <i class="fas fa-arrow-right"></i> إلغاء
                                    </a>
                                    <button type="submit"
                                            class="btn btn-success font-weight-bold px-10">
                                        <i class="fas fa-check"></i> تحديث البيانات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($histories))
            <div class="card card-custom gutter-b shadow-sm">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title">
                        <span class="card-label font-weight-bolder text-dark">سجل الرسوم والتعديلات (للقراءة فقط)</span>
                    </h3>
                </div>
                <div class="card-body pt-7">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-left">
                                    <th style="min-width: 150px">العملية</th>
                                    <th style="min-width: 150px">القيمة</th>
                                    <th style="min-width: 150px">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($histories as $history)
                                    <tr>
                                        <td>{{ $history->operation_type }}</td>
                                        <td>{{ number_format($history->fee_amount, 2) }} ₪</td>
                                        <td>{{ $history->action_date }}</td>
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
    </div>
@endsection

