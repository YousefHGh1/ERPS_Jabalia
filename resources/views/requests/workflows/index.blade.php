@extends('layout.master')

@section('title')
    {{ __('مسارات نوع الطلب') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('مسارات نوع الطلب: ') . $requestType->name }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('مسارات نوع الطلب: ') . $requestType->name }}"
                subtitle="{{ __('إدارة مسارات نوع الطلب') }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('request-types.index') }}" class="btn btn-light-danger font-weight-bolder mr-2">
                                            <i class="fas fa-arrow-right"></i> {{ __('عودة') }}
                                        </a>
                                        @can('store request_workflows')
                                            <a href="javascript:void(0)" id="create-workflow-btn" class="btn btn-primary font-weight-bolder">
                                                <i class="fas fa-plus"></i> {{ __('إضافة مسار') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                @if ($workflows->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-muted mb-5">لا توجد مسارات لهذا النوع بعد.</p>
                        @can('store request_workflows')
                            <a href="javascript:void(0)" id="create-workflow-btn-empty" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة أول مسار
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center table-hover border-0">
                            <thead>
                                <tr class="text-uppercase text-muted">
                                    <th class="pl-0" style="width: 80px">الترتيب</th>
                                    <th style="min-width: 200px">اسم المسار (الدائرة/القسم)</th>
                                    <th style="min-width: 200px">المسار السابق</th>
                                    <th style="min-width: 100px">الحالة</th>
                                    <th class="text-right" style="min-width: 120px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workflows as $workflow)
                                    <tr>
                                        <td class="pl-0">
                                            <span class="text-muted font-weight-bold">{{ $workflow->step_order }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-primary mr-3">
                                                    <span class="symbol-label font-size-h5 font-weight-bold">
                                                        {{ mb_substr($workflow->department->name ?? ($workflow->section->name ?? 'N'), 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        {{ $workflow->department->name ?? ($workflow->section->name ?? '-') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($workflow->previousWorkflow)
                                                <span class="label label-lg label-light-info label-inline font-weight-bold">
                                                    {{ $workflow->previousWorkflow->department->name ?? ($workflow->previousWorkflow->section->name ?? '-') }}
                                                </span>
                                            @else
                                                <span class="badge badge-success">مسار أولي</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($workflow->is_active)
                                                <span class="badge badge-success">نشط</span>
                                            @else
                                                <span class="badge badge-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td class="text-right pr-0">
                                            @can('update request_workflows')
                                                <a href="javascript:void(0)" onclick="editWorkflow({{ $workflow->id }})"
                                                    class="btn btn-icon btn-light-info btn-hover-info btn-sm mx-1"
                                                    title="تعديل">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('delete request_workflows')
                                                @if ($workflow->nextWorkflows->isEmpty())
                                                    <a href="javascript:void(0)" onclick="deleteWorkflow({{ $workflow->id }})"
                                                        class="btn btn-icon btn-light-danger btn-hover-danger btn-sm mx-1"
                                                        title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Add Workflow -->
    <div class="modal fade" id="workflow-modal" tabindex="-1" role="dialog" aria-labelledby="workflow-modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success py-4">
                    <h5 class="modal-title text-white font-weight-bolder" id="workflow-modal-label">
                        {{ __('إضافة مسار') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <form id="workflow-form" name="workflow-form">
                    <div class="modal-body p-10">
                        <input type="hidden" name="workflow_id" id="workflow_id">
                        <input type="hidden" name="request_type_id" value="{{ $requestType->id }}">

                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('الجهة المسؤولة') }} <span
                                    class="text-danger">*</span></label>
                            <select id="unit_id" class="form-control form-control-solid selectpicker w-100"
                                data-live-search="true" required>
                                <option value="">{{ __('اختر دائرة أو قسم') }}</option>
                                <optgroup label="الدوائر">
                                    @foreach ($departments as $department)
                                        <option value="department:{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="الأقسام">
                                    @foreach ($sections as $section)
                                        <option value="section:{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <div class="text-danger mt-1" id="unit_id_error"></div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('ترتيب الخطوة') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="step_order" id="step_order" class="form-control form-control-solid"
                                required min="1" placeholder="{{ __('أدخل ترتيب الخطوة') }}"
                                value="{{ $workflows->max('step_order') + 1 ?? 1 }}">
                            <div class="text-danger mt-1" id="step_order_error"></div>
                        </div>

                        <div class="form-group">
                            <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                                <i class="fas fa-info-circle text-primary mr-3"></i>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold">ملاحظة:</span>
                                    <span class="font-size-sm">يمكنك إضافة مسار جديد كمسار أولي (بدون مسار سابق) أو كمسار
                                        تالي (باختيار مسار سابق)</span>
                                </div>
                            </div>
                            <div class="checkbox-list">
                                <label class="checkbox checkbox-outline">
                                    <input type="checkbox" id="prev_workflow_check" name="has_previous" value="1">
                                    <span></span>
                                    {{ __('هذا المسار يتطلب موافقة مسار سابق') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group d-none" id="prev_workflow_group">
                            <label class="font-weight-bold">{{ __('المسار السابق') }} <span
                                    class="text-danger">*</span></label>
                            <select name="previous_workflow_id" id="previous_workflow_id"
                                class="form-control form-control-solid selectpicker w-100" data-live-search="true">
                                <option value="">{{ __('اختر المسار السابق') }}</option>
                                @foreach ($workflows as $workflow)
                                    <option value="{{ $workflow->id }}">
                                        {{ $workflow->department->name ?? ($workflow->section->name ?? '-') }} (ترتيب:
                                        {{ $workflow->step_order }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1" id="previous_workflow_id_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-warning font-weight-bold"
                            data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold px-10"
                            id="btn-save">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/features/miscellaneous/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        var STORE_URL = "{{ route('request-workflows.store') }}";
        var EDIT_URL = "{{ route('request-workflows.edit', ':id') }}";
        var UPDATE_URL = "{{ route('request-workflows.update', ':id') }}";
        var DELETE_URL = "{{ route('request-workflows.destroy', ':id') }}";
        var REQUEST_TYPE_ID = {{ $requestType->id }};

        // Initialize selectpicker when modal is shown
        $('#workflow-modal').on('shown.bs.modal', function() {
            $('.selectpicker').selectpicker('refresh');
        });

        // Open modal for create
        $('#create-workflow-btn, #create-workflow-btn-empty').click(function() {
            $('#workflow_id').val('');
            $('#workflow-form')[0].reset();
            $('#workflow-modal-label').html('{{ __('إضافة مسار') }}');
            $('.modal-header').removeClass('bg-info').addClass('bg-success');
            $('#btn-save').html('{{ __('حفظ') }}');
            $('#prev_workflow_group').addClass('d-none');
            $('#prev_workflow_check').prop('checked', false);
            $('#step_order').val({{ $workflows->max('step_order') + 1 ?? 1 }});
            $('.selectpicker').selectpicker('refresh');
            clearErrors();
            $('#workflow-modal').modal('show');
        });

        // Toggle previous workflow field
        $('#prev_workflow_check').change(function() {
            if ($(this).is(':checked')) {
                $('#prev_workflow_group').removeClass('d-none');
                $('#previous_workflow_id').prop('required', true);
            } else {
                $('#prev_workflow_group').addClass('d-none');
                $('#previous_workflow_id').prop('required', false).val('');
                $('.selectpicker').selectpicker('refresh');
            }
        });

        // Save/Update form
        $('#workflow-form').on('submit', function(e) {
            e.preventDefault();
            var workflowId = $('#workflow_id').val();
            var url = workflowId ? UPDATE_URL.replace(':id', workflowId) : STORE_URL;
            var method = workflowId ? 'PUT' : 'POST';

            // التحقق من البيانات قبل الإرسال
            var unit = $('#unit_id').val();
            var stepOrder = $('#step_order').val();

            if (!unit) {
                $('#unit_id_error').html('يرجى اختيار دائرة أو قسم');
                return;
            }

            if (!stepOrder || stepOrder < 1) {
                $('#step_order_error').html('يرجى إدخال ترتيب صحيح (أكبر من 0)');
                return;
            }

            var unitParts = unit.split(':');
            var formData = {
                request_type_id: REQUEST_TYPE_ID,
                unit_type: unitParts[0],
                unit_id: unitParts[1],
                step_order: parseInt(stepOrder),
                previous_workflow_id: null
            };

            // إضافة previous_workflow_id فقط إذا كان checkbox محدداً وكان هناك قيمة
            if ($('#prev_workflow_check').is(':checked')) {
                var prevWorkflowId = $('#previous_workflow_id').val();
                if (!prevWorkflowId || prevWorkflowId === '') {
                    $('#previous_workflow_id_error').html('يرجى اختيار المسار السابق');
                    $('#btn-save').html('{{ __('حفظ') }}').prop('disabled', false);
                    return;
                }
                formData.previous_workflow_id = prevWorkflowId;
            }

            if (method === 'PUT') {
                formData._method = 'PUT';
            }

            $('#btn-save').html('جاري الحفظ...').prop('disabled', true);
            clearErrors();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#workflow-modal').modal('hide');
                    $('#btn-save').html('{{ __('حفظ') }}').prop('disabled', false);
                    Swal.fire({
                        icon: "success",
                        title: workflowId ? "تم التحديث بنجاح!" : "تم الحفظ بنجاح!",
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    $('#btn-save').html('{{ __('حفظ') }}').prop('disabled', false);

                    if (xhr.status === 422) {
                        // Validation errors
                        var errors = xhr.responseJSON?.errors || {};
                        var errorMessages = [];

                        $.each(errors, function(key, value) {
                            var errorKey = key.replace(/\./g, '_');
                            $('#' + errorKey + '_error').html(Array.isArray(value) ? value[0] :
                                value);
                            errorMessages.push(Array.isArray(value) ? value[0] : value);
                        });

                        // إذا لم تكن هناك أخطاء في حقول محددة، عرض رسالة عامة
                        if (Object.keys(errors).length === 0 && xhr.responseJSON?.message) {
                            Swal.fire({
                                icon: "error",
                                title: "خطأ في التحقق!",
                                text: xhr.responseJSON.message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else if (errorMessages.length > 0) {
                            Swal.fire({
                                icon: "error",
                                title: "خطأ في التحقق!",
                                html: errorMessages.join('<br>'),
                                showConfirmButton: false,
                                timer: 4000
                            });
                        }
                    } else {
                        var message = xhr.responseJSON?.message || 'حدث خطأ أثناء الحفظ';
                        console.error('Error saving workflow:', xhr);
                        Swal.fire({
                            icon: "error",
                            title: "خطأ!",
                            text: message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            });
        });

        // Edit function
        function editWorkflow(id) {
            $.get(EDIT_URL.replace(':id', id), function(data) {
                // Update previous workflows dropdown (exclude current workflow)
                var previousWorkflowOptions = '<option value="">{{ __('اختر المسار السابق') }}</option>';
                @foreach ($workflows as $workflow)
                    if ({{ $workflow->id }} != data.id) {
                        previousWorkflowOptions +=
                            '<option value="{{ $workflow->id }}">{{ $workflow->department->name ?? ($workflow->section->name ?? '-') }} (ترتيب: {{ $workflow->step_order }})</option>';
                    }
                @endforeach
                $('#previous_workflow_id').html(previousWorkflowOptions);

                $('#workflow_id').val(data.id);
                $('#unit_id').val(data.department_id ? 'department:' + data.department_id : 'section:' + data
                    .section_id);
                $('#step_order').val(data.step_order);

                if (data.previous_workflow_id) {
                    $('#prev_workflow_check').prop('checked', true);
                    $('#previous_workflow_id').val(data.previous_workflow_id);
                    $('#prev_workflow_group').removeClass('d-none');
                } else {
                    $('#prev_workflow_check').prop('checked', false);
                    $('#prev_workflow_group').addClass('d-none');
                }

                $('.selectpicker').selectpicker('refresh');

                $('#workflow-modal-label').html('{{ __('تعديل مسار') }}');
                $('.modal-header').removeClass('bg-success').addClass('bg-info');
                $('#btn-save').html('{{ __('تحديث') }}');
                clearErrors();
                $('#workflow-modal').modal('show');
            });
        }

        // Delete function
        function deleteWorkflow(id) {
            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "سيتم حذف المسار!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "نعم, احذف",
                cancelButtonText: "إلغاء",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: DELETE_URL.replace(':id', id),
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        },
                        error: function(xhr) {
                            var message = xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف';
                            Swal.fire({
                                icon: "error",
                                title: "خطأ!",
                                text: message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    });
                }
            });
        }

        function clearErrors() {
            $('.text-danger').html('');
        }
    </script>
@endsection
