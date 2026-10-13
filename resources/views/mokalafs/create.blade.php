@extends('layout.master')

@section('title')
    {{ __('إضافة مكلف جديد') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('المكلفين') }} / {{ __('إضافة جديد') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('إضافة مكلف جديد') }}"
                subtitle="{{ __('إدخال بيانات المكلف الجديد') }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('mokalafs.index') }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> {{ __('عودة') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body py-8">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('mokalafs.store') }}" id="mokalaf-form">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card card-body bg-light border-0 shadow-sm mb-8" style="border-radius: 12px;">
                                <h4 class="text-dark font-weight-bolder mb-6">{{ __('البيانات الأساسية') }}</h4>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('رقم الهوية') }} <span class="text-danger">*</span></label>
                                            <div class="input-icon">
                                                <input name="identity_number" type="text" class="form-control form-control-solid"
                                                    id="identity_number" placeholder="{{ __('أدخل رقم الهوية') }}"
                                                    value="{{ old('identity_number', $identity_number ?? '') }}" />
                                                <span><i class="fas fa-id-card text-muted"></i></span>
                                            </div>
                                            <small class="form-text text-muted">{{ __('أو رقم الترخيص للمشتغلين المرخصين') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('رقم الترخيص') }}</label>
                                            <div class="input-icon">
                                                <input name="license_number" type="text" class="form-control form-control-solid"
                                                    id="license_number" placeholder="{{ __('أدخل رقم الترخيص') }}" />
                                                <span><i class="fas fa-file-alt text-muted"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="manual-input" style="display: none;">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('الاسم الكامل') }} <span class="text-danger">*</span></label>
                                            <input name="name" type="text" class="form-control form-control-solid"
                                                placeholder="{{ __('أدخل الاسم الكامل للمكلف') }}"
                                                value="{{ old('name', $name ?? '') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('رقم الجوال') }}</label>
                                            <div class="input-icon">
                                                <input name="mobile" type="text" class="form-control form-control-solid"
                                                    placeholder="{{ __('رقم الجوال') }}"
                                                    value="{{ old('mobile', $mobile ?? '') }}" />
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
                                                placeholder="{{ __('أدخل العنوان الكامل') }}">{{ old('address', $address ?? '') }}</textarea>
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
                                    <i class="fas fa-check"></i> {{ __('حفظ') }}
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
        $('#identity_number, #license_number').on('blur', function() {
            var identityNumber = $('#identity_number').val();

            if (identityNumber) {
                $.ajax({
                    url: '{{ route("mokalafs.check-exists") }}',
                    type: 'GET',
                    data: {
                        identity_number: identityNumber
                    },
                    success: function(response) {
                        if (response.exists) {
                            if (confirm('{{ __('هذا الشخص مضاف بالفعل كمكلف. هل تريد تعديل بياناته؟') }}')) {
                                window.location.href = response.edit_url;
                            } else {
                                $('#identity_number, #license_number').val('');
                            }
                        } else if (identityNumber) {
                            // Try to get citizen data
                            $.ajax({
                                url: '{{ route("mokalafs.get-citizen-data") }}',
                                type: 'GET',
                                data: { identity_number: identityNumber },
                                success: function(citizenResponse) {
                                    if (citizenResponse.first_name_ar) {
                                        var fullName = (citizenResponse.first_name_ar || '') + ' ' +
                                                     (citizenResponse.second_name_ar || '') + ' ' +
                                                     (citizenResponse.third_name_ar || '') + ' ' +
                                                     (citizenResponse.last_name_ar || '');
                                        $('input[name="name"]').val(fullName.trim());
                                        $('input[name="mobile"]').val(citizenResponse.mobile || '');
                                        $('textarea[name="address"]').val(citizenResponse.street_ar || '');
                                        $('#manual-input').show();
                                    }
                                },
                                error: function() {
                                    $('#manual-input').show();
                                }
                            });
                        } else {
                            $('#manual-input').show();
                        }
                    },
                    error: function() {
                        $('#manual-input').show();
                    }
                });
            } else {
                $('#manual-input').show();
            }
        });

        $('#mokalaf-form').on('submit', function(e) {
            var identityNumber = $('#identity_number').val();
            var licenseNumber = $('#license_number').val();

            if (!identityNumber && !licenseNumber) {
                e.preventDefault();
                alert('{{ __('يجب إدخال رقم الهوية أو رقم الترخيص') }}');
                return false;
            }
        });
    });
</script>
@endpush
