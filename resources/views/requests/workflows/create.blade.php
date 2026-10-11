@extends('layout.master')

@section('title')
    {{ __('إضافة مسار') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('إضافة مسار لنوع الطلب: ') . $requestType->name }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('إضافة مسار') }}"
                subtitle="{{ __('إضافة مسار جديد لنوع الطلب: ') . $requestType->name }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('request-workflows.index', $requestType->id) }}" class="btn btn-warning font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> {{ __('رجوع') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <form action="{{ route('request-workflows.store') }}" method="POST" id="workflow-form">
                    @csrf
                    <input type="hidden" name="request_type_id" value="{{ $requestType->id }}">
                    <input type="hidden" name="unit_type" id="unit_type">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('اسم المسار') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="{{ __('أدخل اسم المسار') }}" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('ترتيب الخطوة') }} <span class="text-danger">*</span></label>
                                <input type="number" name="step_order" class="form-control" required min="1" placeholder="{{ __('أدخل ترتيب الخطوة') }}" value="{{ old('step_order', $existingWorkflows->max('step_order') + 1 ?? 1) }}">
                                @error('step_order')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('الوصف') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="{{ __('أدخل وصف المسار') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('الجهة المسؤولة') }} <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-control select2" required style="width: 100%">
                            <option value="">{{ __('اختر دائرة أو قسم') }}</option>
                            <optgroup label="الدوائر">
                                @foreach($departments as $department)
                                    <option value="department:{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="الأقسام">
                                @foreach($sections as $section)
                                    <option value="section:{{ $section->id }}" {{ old('unit_id') == 'section:' . $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('unit_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="checkbox-list">
                            <label class="checkbox checkbox-outline">
                                <input type="checkbox" id="prev_workflow_check" name="has_previous" value="1">
                                <span></span>
                                يتطلب موافقة مسار سابق
                            </label>
                        </div>
                    </div>

                    <div class="form-group d-none" id="prev_workflow_group">
                        <label class="font-weight-bold">{{ __('المسار السابق') }}</label>
                        <select name="previous_workflow_id" class="form-control select2" style="width: 100%">
                            <option value="">{{ __('اختر المسار السابق') }}</option>
                            @foreach($existingWorkflows as $workflow)
                                <option value="{{ $workflow->id }}" {{ old('previous_workflow_id') == $workflow->id ? 'selected' : '' }}>
                                    {{ $workflow->name }} ({{ $workflow->section->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('previous_workflow_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-check"></i> {{ __('حفظ') }}
                        </button>
                        <a href="{{ route('request-workflows.index', $requestType->id) }}" class="btn btn-warning font-weight-bolder">
                            {{ __('إلغاء') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('assets/plugins/custom/select2/css/select2.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'اختر...',
                dir: 'rtl'
            });

            $('#workflow-form').submit(function() {
                var unit = $('#unit_id').val().split(':');
                $('#unit_type').val(unit[0]);
                $('#unit_id').val(unit[1]);
            });

            $('#prev_workflow_check').change(function() {
                if ($(this).is(':checked')) {
                    $('#prev_workflow_group').removeClass('d-none');
                    $('select[name="previous_workflow_id"]').prop('required', true);
                } else {
                    $('#prev_workflow_group').addClass('d-none');
                    $('select[name="previous_workflow_id"]').prop('required', false).val('');
                }
            });
        });
    </script>
@endsection
