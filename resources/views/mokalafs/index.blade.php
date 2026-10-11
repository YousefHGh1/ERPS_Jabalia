@extends('layout.master')

@section('title')
    {{ __('المكلفين') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('المكلفين') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('المكلفين') }}"
                subtitle="{{ __('إدارة وتصفح سجلات المكلفين') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        @can('store mokalafs')
                                            <a href="{{route('mokalafs.create')}}" class="btn btn-primary font-weight-bolder d-none" id="btn-mokalaf-create-page">
                                                <i class="fas fa-plus"></i> {{ __('إضافة جديد (صفحة)') }}
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder" id="btn-mokalaf-create-modal" data-toggle="modal" data-target="#mokalaf-create-modal">
                                                <i class="fas fa-plus"></i> {{ __('إضافة جديد') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('identity_number') || request()->filled('address') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form" method="GET" action="{{ route('mokalafs.index') }}">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الاسم') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('الاسم') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('رقم الهوية') }}</label>
                                    <div class="input-icon">
                                        <input name="identity_number" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('رقم الهوية') }}" value="{{ request('identity_number') }}" />
                                        <span><i class="fas fa-id-card text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('العنوان') }}</label>
                                    <div class="input-icon">
                                        <input name="address" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('العنوان') }}" value="{{ request('address') }}" />
                                        <span><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark opacity-0">{{ __('إجراءات') }}</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </button>
                                        <a href="{{ route('mokalafs.index') }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-sync-alt"></i> {{ __('إعادة تعيين') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <div class="table-responsive" data-responsive="true">
                    <table class="table table-head-custom table-vertical-center" id="mokalaf_table">
                        <thead>
                            <tr>
                                <th>{{ __('رقم المكلف') }}</th>
                                <th>{{ __('اسم المكلف') }}</th>
                                <th>{{ __('هوية | معرف | مشتغل مرخص') }}</th>
                                <th>{{ __('العنوان') }}</th>
                                <th>{{ __('رقم الجوال') }}</th>
                                <th>{{ __('تاريخ الإضافة') }}</th>
                                <th>{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mokalafs as $mokalaf)
                                <tr>
                                    <td data-label="{{ __('رقم المكلف') }}">{{ $mokalaf->id }}</td>
                                    <td data-label="{{ __('اسم المكلف') }}">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40 symbol-light-success mr-3">
                                                <span class="symbol-label font-weight-bolder">{{ substr($mokalaf->name ?? 'N/A', 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <a href="{{ route('mokalafs.show', $mokalaf) }}" class="text-dark-75 font-weight-bolder text-hover-primary">
                                                    {{ $mokalaf->name ?? 'N/A' }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('هوية | معرف | مشتغل مرخص') }}">
                                        <span class="badge badge-{{ $mokalaf->identity_number ? 'info' : 'warning' }}">
                                            {{ $mokalaf->identifier }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $mokalaf->identifier_type }}</small>
                                    </td>
                                    <td data-label="{{ __('العنوان') }}">{{ $mokalaf->address ?? '-' }}</td>
                                    <td data-label="{{ __('رقم الجوال') }}">{{ $mokalaf->mobile ?? '-' }}</td>
                                    <td data-label="{{ __('تاريخ الإضافة') }}">{{ $mokalaf->created_at->format('d-m-Y') }}</td>
                                    <td data-label="{{ __('إجراءات') }}" class="table-actions">
                                        <div class="dropdown dropdown-inline">
                                            <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                @can('update mokalafs')
                                                    <a href="javascript:;" class="dropdown-item edit-mokalaf" data-id="{{ $mokalaf->id }}">
                                                        <i class="fas fa-pen text-primary"></i> {{ __('تعديل') }}
                                                    </a>
                                                @endcan
                                                @can('delete mokalafs')
                                                    <a href="javascript:;" class="dropdown-item delete-mokalaf" data-id="{{ $mokalaf->id }}">
                                                        <i class="fas fa-trash text-danger"></i> {{ __('حذف') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('لا توجد بيانات') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap table-pagination-wrapper">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">{{ __('عرض') }} {{ $mokalafs->firstItem() }} {{ __('إلى') }} {{ $mokalafs->lastItem() }} {{ __('من') }} {{ $mokalafs->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {!! $mokalafs->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال إضافة مكلف -->
    <div class="modal fade" id="mokalaf-create-modal" tabindex="-1" role="dialog" aria-labelledby="mokalaf-create-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" style="padding: 10px 18px;">
                    <h5 class="modal-title font-weight-bolder" id="mokalaf-create-modal-label" style="font-size: 16px;">
                        <i class="fas fa-plus text-primary"></i> {{ __('إضافة مكلف جديد') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 0; margin: 0;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mokalaf-form-container" style="padding: 12px 18px;">
                    <!-- سيتم تحميل الفورم هنا عبر AJAX -->
                    <div class="text-center" style="padding: 20px 0;">
                        <div class="spinner spinner-primary spinner-lg"></div>
                        <p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال تعديل مكلف -->
    <div class="modal fade" id="mokalaf-edit-modal" tabindex="-1" role="dialog" aria-labelledby="mokalaf-edit-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" style="padding: 10px 18px;">
                    <h5 class="modal-title font-weight-bolder" id="mokalaf-edit-modal-label" style="font-size: 16px;">
                        <i class="fas fa-pen text-primary"></i> {{ __('تعديل بيانات المكلف') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 0; margin: 0;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mokalaf-edit-form-container" style="padding: 12px 18px;">
                    <!-- سيتم تحميل الفورم هنا عبر AJAX -->
                    <div class="text-center" style="padding: 20px 0;">
                        <div class="spinner spinner-primary spinner-lg"></div>
                        <p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    #mokalaf-create-modal .modal-dialog {
        margin: 1.5rem auto;
    }
    
    #mokalaf-create-modal .modal-content {
        border-radius: 8px;
    }
    
    #mokalaf-create-modal .modal-body {
        overflow-y: visible !important;
        max-height: none !important;
    }
    
    #mokalaf-create-modal .modal-dialog {
        max-height: 90vh;
    }
    
    #mokalaf-create-modal .modal-content {
        max-height: 90vh;
        overflow-y: visible;
    }
    
    #mokalaf-form .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    #mokalaf-form .input-icon input {
        padding-right: 35px !important;
    }
    
    #mokalaf-form .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize modal events - load form when modal is shown
        $('#mokalaf-create-modal').on('show.bs.modal', function(e) {
            // Only load form if container is empty or has loading spinner
            var container = $('#mokalaf-form-container');
            if (container.find('.spinner').length > 0 || container.html().trim() === '' || container.find('form').length === 0) {
                loadMokalafForm();
            } else {
                // Reset form fields when modal is reopened
                setTimeout(function() {
                    var nameInput = $('#mokalaf-form-container').find('input[name="name"]');
                    if (nameInput.length) {
                        nameInput.removeAttr('readonly');
                        nameInput.css({
                            'background-color': '',
                            'cursor': '',
                            'color': ''
                        });
                        nameInput.removeAttr('title');
                    }
                }, 100);
            }
        });

        $('#mokalaf-create-modal').on('hidden.bs.modal', function() {
            // Reset form container when modal is closed
            $('#mokalaf-form-container').html('<div class="text-center" style="padding: 20px 0;"><div class="spinner spinner-primary spinner-lg"></div><p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p></div>');
        });

        function loadMokalafForm() {
            $.ajax({
                url: "{{ route('mokalafs.create') }}?modal=1",
                type: 'GET',
                success: function(response) {
                    $('#mokalaf-form-container').html(response);
                    initializeMokalafFormScripts();
                },
                error: function() {
                    $('#mokalaf-form-container').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px;">{{ __('حدث خطأ أثناء تحميل الفورم') }}</div>');
                }
            });
        }

        function initializeMokalafFormScripts() {
            console.log('Initializing form scripts...');
            
            // Remove any existing handlers and add new one
            $('#mokalaf-form').off('submit');
            
            // Handle form submission via AJAX
            $(document).off('submit', '#mokalaf-form').on('submit', '#mokalaf-form', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Form submitted via AJAX');
                
                var form = $(this);
                var formData = form.serialize();
                
                // Validation
                var identityNumber = $('#identity_number').val().trim();
                var licenseNumber = $('#license_number').val().trim();
                var nameValue = $('input[name="name"]').val().trim();
                
                if (!identityNumber && !licenseNumber) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('خطأ') }}',
                            text: '{{ __('يجب إدخال رقم الهوية أو رقم الترخيص') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert('{{ __('يجب إدخال رقم الهوية أو رقم الترخيص') }}');
                    }
                    return false;
                }
                
                if (!nameValue) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('خطأ') }}',
                            text: '{{ __('يجب إدخال الاسم الكامل') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert('{{ __('يجب إدخال الاسم الكامل') }}');
                    }
                    return false;
                }

                // Show loading indicator
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="spinner spinner-sm spinner-primary"></i> {{ __('جاري الحفظ...') }}');

                console.log('Sending AJAX request...');
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        submitBtn.prop('disabled', false).html(originalText);
                        $('#mokalaf-create-modal').modal('hide');
                        
                        // Check if response is JSON and has success message
                        var message = '{{ __('تمت العملية بنجاح') }}';
                        if (response && response.message) {
                            message = response.message;
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ 
                                icon: 'success', 
                                title: message,
                                confirmButtonText: '{{ __('حسناً') }}',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            alert(message);
                            window.location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        // Check if response is actually a success (sometimes 200 comes as error)
                        if (xhr.status === 200 && xhr.responseJSON && xhr.responseJSON.success) {
                            $('#mokalaf-create-modal').modal('hide');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: xhr.responseJSON.message || '{{ __('تمت العملية بنجاح') }}',
                                    confirmButtonText: '{{ __('حسناً') }}',
                                    timer: 2000,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                alert(xhr.responseJSON.message || '{{ __('تمت العملية بنجاح') }}');
                                window.location.reload();
                            }
                            return;
                        }
                        
                        if (xhr.status === 422) {
                            var response = xhr.responseJSON || {};
                            
                            // Check if it's a duplicate entry error
                            if (response.error_type === 'duplicate') {
                                var duplicateMsg = response.details || '{{ __('هذا المكلف مضاف مسبقاً في النظام') }}';
                                if (response.mokalaf_name) {
                                    duplicateMsg += '<br><br><strong>اسم المكلف:</strong> ' + response.mokalaf_name;
                                }
                                
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({ 
                                        icon: 'warning', 
                                        title: response.message || '{{ __('المكلف مضاف مسبقاً') }}',
                                        html: duplicateMsg,
                                        showCancelButton: response.edit_url ? true : false,
                                        confirmButtonText: response.edit_url ? '{{ __('تعديل') }}' : '{{ __('حسناً') }}',
                                        cancelButtonText: '{{ __('إلغاء') }}',
                                        reverseButtons: true
                                    }).then(function(result) {
                                        if (result.isConfirmed && response.edit_url) {
                                            window.location.href = response.edit_url;
                                        }
                                    });
                                } else {
                                    if (response.edit_url && confirm(duplicateMsg + '\n\nهل تريد تعديل بيانات المكلف؟')) {
                                        window.location.href = response.edit_url;
                                    }
                                }
                                return;
                            }
                            
                            // Validation errors - show field-specific errors
                            var errors = response.errors || {};
                            var errorMessages = [];
                            var fieldNames = {
                                'identity_number': '{{ __('رقم الهوية') }}',
                                'license_number': '{{ __('رقم الترخيص') }}',
                                'name': '{{ __('الاسم الكامل') }}',
                                'mobile': '{{ __('رقم الجوال') }}',
                                'address': '{{ __('العنوان') }}'
                            };
                            
                            $.each(errors, function(key, value) {
                                var fieldName = fieldNames[key] || key;
                                var errorText = '';
                                if (Array.isArray(value) && value.length > 0) {
                                    errorText = value[0];
                                } else if (typeof value === 'string') {
                                    errorText = value;
                                }
                                
                                if (errorText) {
                                    errorMessages.push('<strong>' + fieldName + ':</strong> ' + errorText);
                                    
                                    // Highlight the field with error
                                    var fieldSelector = 'input[name="' + key + '"], textarea[name="' + key + '"], select[name="' + key + '"]';
                                    $(fieldSelector).addClass('is-invalid').css('border-color', '#f64e60');
                                    
                                    // Remove error highlight after 5 seconds
                                    setTimeout(function() {
                                        $(fieldSelector).removeClass('is-invalid').css('border-color', '');
                                    }, 5000);
                                }
                            });
                            
                            var errorMsg = '<div style="text-align: right; direction: rtl;">' +
                                         '<strong>{{ __('يرجى تصحيح الأخطاء التالية:') }}</strong><br><br>' +
                                         errorMessages.join('<br>') +
                                         '</div>';
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: response.message || '{{ __('خطأ في التحقق') }}', 
                                    html: errorMsg,
                                    confirmButtonText: '{{ __('حسناً') }}',
                                    width: '500px'
                                });
                            } else {
                                alert(errorMessages.join('\n'));
                            }
                        } else if (xhr.status === 419) {
                            // CSRF token mismatch
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: '{{ __('انتهت صلاحية الجلسة') }}',
                                    text: '{{ __('يرجى إعادة تحميل الصفحة والمحاولة مرة أخرى') }}',
                                    confirmButtonText: '{{ __('حسناً') }}' 
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                alert('{{ __('انتهت صلاحية الجلسة. يرجى إعادة تحميل الصفحة') }}');
                                window.location.reload();
                            }
                        } else {
                            var response = xhr.responseJSON || {};
                            var msg = response.message || response.error || xhr.responseText || '{{ __('حدث خطأ أثناء الحفظ') }}';
                            
                            // Check if it's a duplicate entry error
                            if (response.error_type === 'duplicate') {
                                var duplicateMsg = response.details || '{{ __('هذا المكلف مضاف مسبقاً في النظام') }}';
                                if (response.mokalaf_name) {
                                    duplicateMsg += '<br><br><strong>اسم المكلف:</strong> ' + response.mokalaf_name;
                                }
                                
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({ 
                                        icon: 'warning', 
                                        title: response.message || '{{ __('المكلف مضاف مسبقاً') }}',
                                        html: duplicateMsg,
                                        showCancelButton: response.edit_url ? true : false,
                                        confirmButtonText: response.edit_url ? '{{ __('تعديل') }}' : '{{ __('حسناً') }}',
                                        cancelButtonText: '{{ __('إلغاء') }}',
                                        reverseButtons: true
                                    }).then(function(result) {
                                        if (result.isConfirmed && response.edit_url) {
                                            window.location.href = response.edit_url;
                                        }
                                    });
                                } else {
                                    if (response.edit_url && confirm(duplicateMsg + '\n\nهل تريد تعديل بيانات المكلف؟')) {
                                        window.location.href = response.edit_url;
                                    }
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({ 
                                        icon: 'error', 
                                        title: msg,
                                        confirmButtonText: '{{ __('حسناً') }}' 
                                    });
                                } else {
                                    alert(msg);
                                }
                            }
                        }
                    }
                });
            });
        }

        // Edit mokalaf function - open modal
        $(document).on('click', '.edit-mokalaf', function(e) {
            e.preventDefault();
            var mokalafId = $(this).data('id');
            $('#mokalaf-edit-modal').modal('show');
            loadMokalafEditForm(mokalafId);
        });

        // Initialize edit modal events
        $('#mokalaf-edit-modal').on('show.bs.modal', function(e) {
            // Form will be loaded by edit-mokalaf click handler
        });

        $('#mokalaf-edit-modal').on('hidden.bs.modal', function() {
            // Reset form container when modal is closed
            $('#mokalaf-edit-form-container').html('<div class="text-center" style="padding: 20px 0;"><div class="spinner spinner-primary spinner-lg"></div><p class="text-muted mt-3" style="font-size: 13px;">{{ __('جاري التحميل...') }}</p></div>');
        });

        function loadMokalafEditForm(mokalafId) {
            $.ajax({
                url: "{{ route('mokalafs.edit', ':id') }}".replace(':id', mokalafId) + '?modal=1',
                type: 'GET',
                success: function(response) {
                    $('#mokalaf-edit-form-container').html(response);
                    initializeMokalafEditFormScripts(mokalafId);
                },
                error: function() {
                    $('#mokalaf-edit-form-container').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px;">{{ __('حدث خطأ أثناء تحميل الفورم') }}</div>');
                }
            });
        }

        function initializeMokalafEditFormScripts(mokalafId) {
            console.log('Initializing edit form scripts for mokalaf:', mokalafId);
            
            // Remove any existing handlers and add new one
            $('#mokalaf-edit-form').off('submit');
            
            // Handle form submission via AJAX
            $(document).off('submit', '#mokalaf-edit-form').on('submit', '#mokalaf-edit-form', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Edit form submitted via AJAX');
                
                var form = $(this);
                var formData = form.serialize();
                
                // Validation
                var nameValue = $('input[name="name"]').val().trim();
                
                if (!nameValue) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('خطأ') }}',
                            text: '{{ __('يجب إدخال الاسم الكامل') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert('{{ __('يجب إدخال الاسم الكامل') }}');
                    }
                    return false;
                }

                // Show loading indicator
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="spinner spinner-sm spinner-primary"></i> {{ __('جاري التحديث...') }}');

                console.log('Sending AJAX update request...');
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        submitBtn.prop('disabled', false).html(originalText);
                        $('#mokalaf-edit-modal').modal('hide');
                        
                        var message = '{{ __('تم تحديث بيانات المكلف بنجاح') }}';
                        if (response && response.message) {
                            message = response.message;
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ 
                                icon: 'success', 
                                title: message,
                                confirmButtonText: '{{ __('حسناً') }}',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            alert(message);
                            window.location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        if (xhr.status === 422) {
                            var response = xhr.responseJSON || {};
                            var errors = response.errors || {};
                            var errorMessages = [];
                            var fieldNames = {
                                'name': '{{ __('الاسم الكامل') }}',
                                'mobile': '{{ __('رقم الجوال') }}',
                                'address': '{{ __('العنوان') }}'
                            };
                            
                            $.each(errors, function(key, value) {
                                var fieldName = fieldNames[key] || key;
                                var errorText = '';
                                if (Array.isArray(value) && value.length > 0) {
                                    errorText = value[0];
                                } else if (typeof value === 'string') {
                                    errorText = value;
                                }
                                
                                if (errorText) {
                                    errorMessages.push('<strong>' + fieldName + ':</strong> ' + errorText);
                                    var fieldSelector = 'input[name="' + key + '"], textarea[name="' + key + '"]';
                                    $(fieldSelector).addClass('is-invalid').css('border-color', '#f64e60');
                                    setTimeout(function() {
                                        $(fieldSelector).removeClass('is-invalid').css('border-color', '');
                                    }, 5000);
                                }
                            });
                            
                            var errorMsg = '<div style="text-align: right; direction: rtl;">' +
                                         '<strong>{{ __('يرجى تصحيح الأخطاء التالية:') }}</strong><br><br>' +
                                         errorMessages.join('<br>') +
                                         '</div>';
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: response.message || '{{ __('خطأ في التحقق') }}', 
                                    html: errorMsg,
                                    confirmButtonText: '{{ __('حسناً') }}',
                                    width: '500px'
                                });
                            } else {
                                alert(errorMessages.join('\n'));
                            }
                        } else {
                            var response = xhr.responseJSON || {};
                            var msg = response.message || response.error || xhr.responseText || '{{ __('حدث خطأ أثناء التحديث') }}';
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: msg,
                                    confirmButtonText: '{{ __('حسناً') }}' 
                                });
                            } else {
                                alert(msg);
                            }
                        }
                    }
                });
            });
        }

        // Delete mokalaf function
        $('.delete-mokalaf').click(function() {
            var id = $(this).data('id');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('هل أنت متأكد؟') }}',
                    text: '{{ __('سيتم حذف هذا المكلف نهائياً') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('نعم، احذف') }}',
                    cancelButtonText: '{{ __('إلغاء') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteMokalafAjax(id);
                    }
                });
            } else {
                if(confirm('{{ __('هل أنت متأكد من حذف هذا المكلف؟') }}')) {
                    deleteMokalafAjax(id);
                }
            }
        });

        function deleteMokalafAjax(id) {
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
            
            $.ajax({
                url: '{{ route("mokalafs.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: csrfToken
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('تم الحذف بنجاح') }}',
                            confirmButtonText: '{{ __('حسناً') }}'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert('{{ __('تم الحذف بنجاح') }}');
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    var message = '{{ __('حدث خطأ أثناء الحذف') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        message = '{{ __('انتهت صلاحية الجلسة. يرجى إعادة تحميل الصفحة') }}';
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: message,
                            confirmButtonText: '{{ __('حسناً') }}'
                        });
                    } else {
                        alert(message);
                    }
                }
            });
        }
    });
</script>
@endsection
