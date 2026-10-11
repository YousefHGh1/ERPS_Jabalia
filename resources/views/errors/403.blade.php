@extends('layout.master')

@section('title')
    {{ __('عدم السماح بالوصول') }}
@endsection

@section('page_title')
    {{ __('لوحة التحكم') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('صلاحيات الوصول') }}
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom shadow-sm">
                    <div class="card-header bg-danger-o-70 border-0 py-5">
                        <div class="card-title">
                            <h3 class="card-label text-danger font-weight-bolder">
                                {{ __('ليس لديك صلاحية للوصول إلى هذه الصفحة') }}
                            </h3>
                        </div>
                    </div>
                    <div class="card-body text-center py-10">
                        <div class="mb-6">
                            <span class="display-3 font-weight-bold text-danger">403</span>
                        </div>
                        <p class="font-size-h5 text-muted mb-6">
                            {{ __('يبدو أنك تحاول الوصول إلى صفحة أو عملية غير مسموح بها لحسابك.') }}
                        </p>
                        <p class="text-muted mb-10">
                            {{ __('إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع مسؤول النظام لطلب صلاحية الوصول المناسبة.') }}
                        </p>
                        <a href="{{ url()->previous() }}" class="btn btn-light-primary font-weight-bold px-6 mr-3">
                            {{ __('رجوع للصفحة السابقة') }}
                        </a>
                        <a href="{{ route('home.index') }}" class="btn btn-primary font-weight-bold px-6">
                            {{ __('الانتقال إلى الصفحة الرئيسية') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

