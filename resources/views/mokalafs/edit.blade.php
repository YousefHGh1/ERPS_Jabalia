@extends('layout.master')

@section('title')
    {{ __('تعديل بيانات المكلف') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('المكلفين') }} / {{ __('تعديل') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('تعديل بيانات المكلف') }}"
                subtitle="{{ __('تحديث بيانات المكلف: ' . ($mokalaf->name ?? 'N/A')) }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('mokalafs.index') }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> {{ __('عودة') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body py-8">
                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <form method="POST" action="{{ route('mokalafs.update', $mokalaf) }}" id="mokalaf-form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card card-body bg-light border-0 shadow-sm mb-8" style="border-radius: 12px;">
                                <h4 class="text-dark font-weight-bolder mb-6">{{ __('البيانات الأساسية') }}</h4>
                                
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('هوية | معرف | مشتغل مرخص') }}</label>
                                            <div class="input-icon">
                                                <input type="text" class="form-control form-control-solid" 
                                                    value="{{ $mokalaf->identifier }}" readonly />
                                                <span><i class="fas fa-id-card text-muted"></i></span>
                                            </div>
                                            <small class="form-text text-muted">{{ $mokalaf->identifier_type }}</small>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('الاسم الكامل') }} <span class="text-danger">*</span></label>
                                            <input name="name" type="text" class="form-control form-control-solid" 
                                                value="{{ $mokalaf->name }}" placeholder="{{ __('الاسم الكامل') }}" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('رقم الجوال') }}</label>
                                            <div class="input-icon">
                                                <input name="mobile" type="text" class="form-control form-control-solid" 
                                                    value="{{ $mokalaf->mobile ?? '' }}" placeholder="{{ __('رقم الجوال') }}" />
                                                <span><i class="fas fa-phone text-muted"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card card-body bg-light border-0 shadow-sm mb-8" style="border-radius: 12px;">
                                <h4 class="text-dark font-weight-bolder mb-6">{{ __('بيانات العنوان') }}</h4>
                                
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('العنوان') }}</label>
                                            <textarea name="address" class="form-control form-control-solid" rows="3" 
                                                placeholder="{{ __('أدخل العنوان الكامل') }}">{{ $mokalaf->address }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('mokalafs.index') }}" class="btn btn-warning font-weight-bolder">
                                    <i class="fas fa-times-circle"></i> {{ __('إلغاء') }}
                                </a>
                                <button type="submit" class="btn btn-primary font-weight-bolder">
                                    <i class="fas fa-check"></i> {{ __('تحديث') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#mokalaf-form').on('submit', function(e) {
            // Form validation can be added here if needed
            return true;
        });
    });
</script>
@endpush
