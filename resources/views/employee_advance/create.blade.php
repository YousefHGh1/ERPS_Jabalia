@extends('layout.master')

@section('title')
    {{ __('New Advance Request') }}
@endsection

@section('page_title')
    {{ __('طلب سلفة جديدة') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('السلف') }}
@endsection

@section('content')
    <div class="container-fluid">
                    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
                    <x-page-header
                        title="{{ __('طلب سلفة جديدة') }}"
                        subtitle="{{ __('يرجى تعبئة جميع الحقول المطلوبة') }}"
                    ></x-page-header>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm border-0">

                    <div class="card-body pt-7">
                        <form action="{{ route('employee-advances.store') }}" method="POST">
                            @csrf

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-bold">{{ __('الموظف') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" value="{{ $employee->name }}" disabled />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-bold">{{ __('القسم') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"
                                        value="{{ $employee->section->name ?? ($employee->department->name ?? '—') }}"
                                        disabled />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label font-weight-bold required">{{ __('المبلغ') }}</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <input type="number" name="amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}" min="1" step="0.01" required
                                            placeholder="أدخل المبلغ المطلوب" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">شيكل</span>
                                        </div>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label font-weight-bold required">{{ __('السبب') }}</label>
                                <div class="col-lg-9">
                                    <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required
                                        placeholder="أدخل سبب طلب السلفة...">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label font-weight-bold required">{{ __('تاريخ الطلب') }}</label>
                                <div class="col-lg-9">
                                    <input type="date" name="request_date"
                                        class="form-control @error('request_date') is-invalid @enderror"
                                        value="{{ old('request_date', date('Y-m-d')) }}" required />
                                    @error('request_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label font-weight-bold">{{ __('تاريخ السداد المتوقع') }}</label>
                                <div class="col-lg-9">
                                    <input type="date" name="expected_repayment_date"
                                        class="form-control @error('expected_repayment_date') is-invalid @enderror"
                                        value="{{ old('expected_repayment_date') }}" />
                                    <span class="form-text text-muted">اختياري - تاريخ السداد المتوقع للسلفة</span>
                                    @error('expected_repayment_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-9 offset-lg-3">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        {{ __('سيتم إرسال طلب السلفة إلى مدير دائرتك للموافقة، ثم إلى رئيس البلدية، وأخيراً إلى دائرة المالية.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-9 offset-lg-3">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">
                                        <i class="fa fa-paper-plane"></i> {{ __('إرسال الطلب') }}
                                    </button>
                                    <a href="{{ route('employee-advances.index') }}"
                                        class="btn btn-light-secondary font-weight-bold">
                                        {{ __('إلغاء') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
