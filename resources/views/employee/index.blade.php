    @extends('layout.master')

    @section('title')
        {{ __('employees') }}
    @endsection

    @section('page_title')
        {{ __('controll_panel') }}
    @endsection
    @section('sub_main')
        {{ __('home') }}
    @endsection
    @section('sub_title')
        {{ __('employees') }}
    @endsection

    @section('css')
        @include('components.admin.structure-styles')
    @endsection

    @section('content')
        <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('employees') }}"
                subtitle="{{ __('Manage and filter employee records') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder btn-sm mr-2" data-toggle="collapse"
                        data-target="#collapseSearch">
                        <i class="fas fa-search ml-1"></i> {{ __('Search') }}
                    </a>
                    <a href="{{ route('tawjihi.export') }}" class="btn btn-info font-weight-bolder btn-sm mr-2">
                        <i class="fas fa-graduation-cap ml-1"></i> تقرير التوجيهي
                    </a>
                    @can('store employees')
                        <a class="btn btn-primary font-weight-bolder btn-sm mr-2" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus ml-1"></i> {{ __('Create') }}
                        </a>
                        <a class="btn btn-success font-weight-bolder btn-sm" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import ml-1"></i> {{ __('Import') }}
                        </a>
                    @endcan
                </x-slot>
            </x-page-header>

            <div class="card card-custom gutter-b shadow-sm border-0">
                <div class="card-body pt-7">
                    <div id="collapseSearch"
                        class="collapse mb-7 {{ request()->filled('name') || request()->filled('czn_id') || request()->filled('department_id') || request()->filled('section_id') || request()->filled('branch_id') || request()->filled('job_id') ? 'show' : '' }}">
                        <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                            <form id="filter-form" class="form">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('name') }}</label>
                                        <div class="input-icon">
                                            <input name="name" type="text" class="form-control form-control-solid"
                                                placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                                            <span><i class="fas fa-user text-muted"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('czn_id') }}</label>
                                        <div class="input-icon">
                                            <input name="czn_id" type="text" class="form-control form-control-solid"
                                                placeholder="{{ __('czn_id') }}" value="{{ request('czn_id') }}" />
                                            <span><i class="fas fa-id-badge text-muted"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('departments') }}</label>
                                        <select id="department_filter" class="form-control selectpicker form-control-solid" name="department_id"
                                            data-live-search="true" title="{{ __('All') }}">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('sections') }}</label>
                                        <select id="section_filter" class="form-control selectpicker form-control-solid" name="section_id"
                                            data-live-search="true" title="{{ __('All') }}">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('branches') }}</label>
                                        <select id="branch_filter" class="form-control selectpicker form-control-solid" name="branch_id"
                                            data-live-search="true" title="{{ __('All') }}">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark">{{ __('jobs') }}</label>
                                        <select class="form-control selectpicker form-control-solid" name="job_id"
                                            data-live-search="true" title="{{ __('All') }}">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}"
                                                    {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-8 col-md-6 mt-8">
                                        <button type="submit" class="btn btn-primary font-weight-bolder px-8 py-3">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </button>
                                        <a href="#" id="export-excel"
                                            class="btn btn-success font-weight-bolder px-8 py-3 ml-2">
                                            <i class="fas fa-file-excel"></i> {{ __('Export Excel') }}
                                        </a>
                                        <a href="#" id="export-csv"
                                            class="btn btn-light-success font-weight-bolder px-8 py-3 ml-2">
                                            <i class="fas fa-download"></i> {{ __('Export CSV') }}
                                        </a>
                                        <a href="{{ route('employees.index') }}"
                                            class="btn btn-light-danger font-weight-bolder px-8 py-3 ml-2">
                                            <i class="fas fa-sync-alt"></i> {{ __('Reset') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive" id="table-container">
                        @include('employee.partials._cards')
                    </div>

                    <div id="create-modals-container">
                        @include('employee.partials._modals')
                    </div>
                    <div id="edit-modals-container">
                        @include('employee.partials._edit_modals', ['employees' => $employees])
                    </div>
                </div>
            </div>
        </div>
    @endsection


    @section('scripts')
        <script>
            $(document).ready(function() {
                const filterForm = $('#filter-form');

                // initialize bootstrap-select on page load with specific options to prevent jitter
                if ($.fn.selectpicker) {
                    $('.selectpicker').selectpicker({
                        width: '100%',
                        container: 'body',
                        dropdownAlignRight: true
                    });
                }

                filterForm.on('submit', function(e) {
                    e.preventDefault();
                    fetchEmployees($(this).serialize());
                });

                $('#export-csv').on('click', function(e) {
                    e.preventDefault();
                    let params = filterForm.serialize();
                    window.location.href = "{{ route('employees.export') }}?" + params;
                });

                $('#export-excel').on('click', function(e) {
                    e.preventDefault();
                    let params = filterForm.serialize();
                    const separator = params ? '&' : '';
                    window.location.href = "{{ route('employees.export') }}?" + params + separator + 'format=xlsx';
                });

                // Pagination AJAX (robust page extraction + update URL)
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();

                    const href = $(this).attr('href');
                    if (!href) return;

                    const url = new URL(href, window.location.origin);
                    const page = url.searchParams.get('page');
                    if (!page) return;

                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('page', page);
                    const params = currentParams.toString();

                    // Update URL for back/forward (keep it in sync with the current AJAX table)
                    const newUrl = "{{ route('employees.index') }}" + '?' + params;
                    window.history.pushState({}, '', newUrl);

                    fetchEmployees(params);
                });

                // Keep browser back/forward working with AJAX pagination/filtering
                window.addEventListener('popstate', function() {
                    const search = window.location.search || '';
                    const params = search.startsWith('?') ? search.substring(1) : search;
                    // Avoid empty calls
                    if (params.trim() === '') return;
                    fetchEmployees(params);
                });

                let employeesRequest = null;

                function fetchEmployees(params) {
                    $('#table-container').css('opacity', 0.5);

                    // speed fix: avoid stacking multiple ajax calls while user clicks fast
                    if (employeesRequest && employeesRequest.readyState !== 4) {
                        employeesRequest.abort();
                    }

                    employeesRequest = $.ajax({
                        url: "{{ route('employees.index') }}?" + params,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(data) {
                            if (data && data.table !== undefined) {
                                $('#table-container').html(data.table).css('opacity', 1);

                                if (data.edit_modals !== undefined) {
                                    $('#edit-modals-container').html(data.edit_modals);

                                    // re-init selectpicker for newly injected edit modals (except job_id which we removed)
                                    if ($.fn.selectpicker) {
                                        $('#edit-modals-container .selectpicker').selectpicker({
                                            width: '100%',
                                            container: 'body',
                                            dropdownAlignRight: true
                                        });
                                    }
                                }
                            } else {
                                $('#table-container').html(data).css('opacity', 1);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.statusText === 'abort') return;
                            $('#table-container').css('opacity', 1);
                        }
                    });
                }



                // Cascading dropdowns for create modal
                $('#department_id_create').on('change', function() {
                    var deptId = $(this).val();
                    if (deptId) {
                        $.ajax({
                            url: '/get_sections_list/' + deptId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                $('#section_id_create').empty();
                                $('#section_id_create').append(data.options);
                                if ($.fn.selectpicker) {
                                    $('#section_id_create').selectpicker('refresh');
                                    $('#branch_id_create').selectpicker('refresh');
                                }
                                $('#branch_id_create').empty();
                                $('#branch_id_create').append(
                                    '<option value="">{{ __('Select') }}</option>');
                            }
                        });
                    } else {
                        $('#section_id_create').empty();
                        $('#section_id_create').append('<option value="">{{ __('Select') }}</option>');
                        if ($.fn.selectpicker) {
                            $('#section_id_create').selectpicker('refresh');
                            $('#branch_id_create').selectpicker('refresh');
                        }
                        $('#branch_id_create').empty();
                        $('#branch_id_create').append('<option value="">{{ __('Select') }}</option>');
                    }
                });

                // Cascading dropdowns for filters
                $('#department_filter').on('change changed.bs.select', function() {
                    var deptId = $(this).val();
                    var sectionFilter = $('#section_filter');
                    var branchFilter = $('#branch_filter');
                    var allText = '{{ __('All') }}';
                    
                    if (deptId) {
                        $.ajax({
                            url: '/get_sections_list/' + deptId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                sectionFilter.find('option').remove();
                                sectionFilter.append('<option value="">' + allText + '</option>');
                                sectionFilter.append(data.options);
                                sectionFilter.selectpicker('refresh');
                                            
                                branchFilter.find('option').remove();
                                branchFilter.append('<option value="">' + allText + '</option>');
                                branchFilter.selectpicker('refresh');
                            }
                        });
                    } else {
                        // Reset to show all sections and branches
                        sectionFilter.find('option').remove();
                        sectionFilter.append('<option value="">' + allText + '</option>');
                        @foreach ($sections as $section)
                            sectionFilter.append('<option value="{{ $section->id }}">{{ $section->name }}</option>');
                        @endforeach
                        sectionFilter.selectpicker('refresh');
                                    
                        branchFilter.find('option').remove();
                        branchFilter.append('<option value="">' + allText + '</option>');
                        @foreach ($branches as $branch)
                            branchFilter.append('<option value="{{ $branch->id }}">{{ $branch->name }}</option>');
                        @endforeach
                        branchFilter.selectpicker('refresh');
                    }
                });
                
                $('#section_filter').on('change changed.bs.select', function() {
                    var sectionId = $(this).val();
                    var branchFilter = $('#branch_filter');
                    var allText = '{{ __('All') }}';
                                
                    if (sectionId) {
                        $.ajax({
                            url: '/get_branches_list/' + sectionId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                branchFilter.find('option').remove();
                                branchFilter.append('<option value="">' + allText + '</option>');
                                branchFilter.append(data.options);
                                branchFilter.selectpicker('refresh');
                            }
                        });
                    } else {
                        // Reset to show all branches
                        branchFilter.find('option').remove();
                        branchFilter.append('<option value="">' + allText + '</option>');
                        @foreach ($branches as $branch)
                            branchFilter.append('<option value="{{ $branch->id }}">{{ $branch->name }}</option>');
                        @endforeach
                        branchFilter.selectpicker('refresh');
                    }
                });

                $('#section_id_create').on('change', function() {
                    var sectionId = $(this).val();
                    if (sectionId) {
                        $.ajax({
                            url: '/get_branches_list/' + sectionId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                $('#branch_id_create').empty();
                                $('#branch_id_create').append(data.options);
                                if ($.fn.selectpicker) {
                                    $('#branch_id_create').selectpicker('refresh');
                                }
                            }
                        });
                    } else {
                        $('#branch_id_create').empty();
                        $('#branch_id_create').append('<option value="">{{ __('Select') }}</option>');
                        if ($.fn.selectpicker) {
                            $('#branch_id_create').selectpicker('refresh');
                        }
                    }
                });

                function refreshSelect(select) {
                    if ($.fn.selectpicker && select.hasClass('selectpicker')) {
                        select.selectpicker('refresh');
                    }
                }

                function setSelectValue(select, value) {
                    var selectedValue = value ? String(value) : '';
                    select.val(selectedValue);

                    if ($.fn.selectpicker && select.hasClass('selectpicker')) {
                        select.selectpicker('refresh');
                    }
                }

                function resetSelect(select) {
                    select.empty().append('<option value="">{{ __('Select') }}</option>');
                    setSelectValue(select, '');
                }

                function loadEditBranches(sectionId, branchSelect, selectedBranch) {
                    if (!sectionId) {
                        resetSelect(branchSelect);
                        return;
                    }

                    $.ajax({
                        url: '/get_branches_list/' + sectionId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            branchSelect.empty().append(data.options);
                            setSelectValue(branchSelect, selectedBranch);
                        }
                    });
                }

                function loadEditSections(deptId, sectionSelect, branchSelect, selectedSection, selectedBranch) {
                    if (!deptId) {
                        resetSelect(sectionSelect);
                        resetSelect(branchSelect);
                        return;
                    }

                    $.ajax({
                        url: '/get_sections_list/' + deptId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            sectionSelect.empty().append(data.options);
                            setSelectValue(sectionSelect, selectedSection);
                            loadEditBranches(sectionSelect.val(), branchSelect, selectedBranch);
                        }
                    });
                }

                // Cascading dropdowns for edit modals
                $(document).on('shown.bs.modal', '[id^="editModal"]', function() {
                    var modal = $(this);
                    var departmentSelect = modal.find('.department-edit');
                    var sectionSelect = modal.find('.section-edit');
                    var branchSelect = modal.find('.branch-edit');
                    var currentDepartment = String(departmentSelect.val() || departmentSelect.data(
                        'initial-department') || '');
                    var currentSection = String(sectionSelect.data('current') || '');
                    var currentBranch = String(branchSelect.data('current') || '');

                    modal.data('initializing-edit-selects', true);

                    // Delay initialization to prevent shaking
                    requestAnimationFrame(function() {
                        if (currentDepartment) {
                            setSelectValue(departmentSelect, currentDepartment);
                        }

                        setSelectValue(sectionSelect, currentSection);
                        setSelectValue(branchSelect, currentBranch);

                        setTimeout(function() {
                            modal.data('initializing-edit-selects', false);
                        }, 50);
                    });
                });

                $(document).on('change', '.department-edit', function() {
                    if ($(this).closest('.modal').data('initializing-edit-selects')) {
                        return;
                    }

                    var deptId = $(this).val();
                    var empId = $(this).data('id');
                    var sectionSelect = $('#section_id_edit_' + empId);
                    var branchSelect = $('#branch_id_edit_' + empId);

                    if (deptId) {
                        loadEditSections(deptId, sectionSelect, branchSelect, '', '');
                    } else {
                        resetSelect(sectionSelect);
                        resetSelect(branchSelect);
                    }
                });

                $(document).on('change', '.section-edit', function() {
                    if ($(this).closest('.modal').data('initializing-edit-selects')) {
                        return;
                    }

                    var sectionId = $(this).val();
                    var empId = $(this).data('id');
                    var branchSelect = $('#branch_id_edit_' + empId);

                    if (sectionId) {
                        loadEditBranches(sectionId, branchSelect, '');
                    } else {
                        resetSelect(branchSelect);
                    }
                });

                // CZN fetching logic for create modal
                $('#czn_id_create').on('change', function() {
                    var cznId = $(this).val();
                    if (cznId && cznId.length >= 9) {
                        $.ajax({
                            url: "{{ route('getCzn') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                czn_id: cznId
                            },
                            dataType: "json",
                            success: function(data) {
                                if (data && data.error) {
                                    alert(data.error);
                                    return;
                                }
                                if (data) {
                                    $('#name_create').val(data.FULLNAME || data.full_name_ar || data
                                        .fullNameAr || data.name_ar || data.nameAr || data
                                        .name || (data.FNAME_ARB || data.SNAME_ARB || data
                                            .TNAME_ARB || data.LNAME_ARB ? [data.FNAME_ARB, data
                                                .SNAME_ARB, data.TNAME_ARB, data.LNAME_ARB
                                            ].filter(Boolean).join(' ') : '') || '');
                                    $('#address_create').val(data.STREET_NAME || data.street_ar ||
                                        data.address || '');
                                    $('#birthplace_create').val(data.BIRTH_PLACE || data
                                        .birth_place_ar || '');
                                    $('#createModal input[name="mobile"]').val(data.mobile || data
                                        .MOBILE || data.phone || data.PHONE || '');

                                    var birthDt = data.BIRTH_DT || data.birth_dt || data
                                        .birthdate || '';
                                    if (birthDt) {
                                        var parts = birthDt.split('/');
                                        if (parts.length === 3) {
                                            $('#birthdate_create').val(parts[2] + '-' + parts[1] +
                                                '-' + parts[0]);
                                        } else {
                                            $('#birthdate_create').val(birthDt);
                                        }
                                    }

                                    if (data.SEX === 'ذكر' || data.gender === 'male') {
                                        $('#gender_create').val('male');
                                    } else if (data.SEX === 'أنثى' || data.gender === 'female') {
                                        $('#gender_create').val('female');
                                    }
                                }
                            },
                            error: function(xhr) {
                                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr
                                    .responseJSON.error : (xhr.statusText ||
                                        'حدث خطأ أثناء جلب البيانات');
                                alert(msg);
                            }
                        });
                    }
                });

                // CZN fetching logic for edit modals
                $(document).on('change', 'input[name="czn_id"]', function() {
                    var cznId = $(this).val();
                    var modal = $(this).closest('.modal');
                    if (cznId && cznId.length >= 9) {
                        $.ajax({
                            url: "{{ route('getCzn') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                czn_id: cznId
                            },
                            dataType: "json",
                            success: function(data) {
                                if (data && data.error) {
                                    alert(data.error);
                                    return;
                                }
                                if (data) {
                                    modal.find('input[name="name"]').val(data.FULLNAME || data
                                        .full_name_ar || data.fullNameAr || data.name_ar || data
                                        .nameAr || data.name || (data.FNAME_ARB || data
                                            .SNAME_ARB || data.TNAME_ARB || data.LNAME_ARB ? [
                                                data.FNAME_ARB, data.SNAME_ARB, data.TNAME_ARB,
                                                data.LNAME_ARB
                                            ].filter(Boolean).join(' ') : '') || '');
                                    modal.find('input[name="address"]').val(data.STREET_NAME || data
                                        .street_ar || data.address || '');
                                    modal.find('input[name="birthplace"]').val(data.BIRTH_PLACE ||
                                        data.birth_place_ar || '');
                                    modal.find('input[name="mobile"]').val(data.mobile || data
                                        .MOBILE || data.phone || data.PHONE || '');

                                    var birthDt = data.BIRTH_DT || data.birth_dt || data
                                        .birthdate || '';
                                    if (birthDt) {
                                        var parts = birthDt.split('/');
                                        if (parts.length === 3) {
                                            modal.find('input[name="birthdate"]').val(parts[2] +
                                                '-' + parts[1] + '-' + parts[0]);
                                        } else {
                                            modal.find('input[name="birthdate"]').val(birthDt);
                                        }
                                    }

                                    if (data.SEX === 'ذكر' || data.gender === 'male') {
                                        modal.find('select[name="gender"]').val('male');
                                    } else if (data.SEX === 'أنثى' || data.gender === 'female') {
                                        modal.find('select[name="gender"]').val('female');
                                    }
                                }
                            },
                            error: function(xhr) {
                                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr
                                    .responseJSON.error : (xhr.statusText ||
                                        'حدث خطأ أثناء جلب البيانات');
                                alert(msg);
                            }
                        });
                    }
                });
            });

            // Import Employee functionality
            $('#import-btn').on('click', function(e) {
                e.preventDefault();
                var fileInput = $('#import-form input[name="file"]');
                var file = fileInput[0].files[0];

                if (!file) {
                    alert('الرجاء اختيار ملف CSV أو Excel');
                    return;
                }

                var allowedExtensions = ['csv', 'xlsx', 'xls'];
                var extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    alert('الرجاء اختيار ملف CSV أو Excel (xlsx/xls)');
                    return;
                }

                var formData = new FormData();
                formData.append('file', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $('#import-progress').removeClass('d-none');
                $('#import-form-container').addClass('d-none');
                $('#import-result').addClass('d-none');
                $('#import-btn').prop('disabled', true);

                $.ajax({
                    url: '{{ route('employees.import') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#import-progress').addClass('d-none');
                        $('#import-result').removeClass('d-none');

                        var resultHtml = '<div class="alert ' + (response.imported > 0 ? 'alert-success' : 'alert-warning') + '">';
                        resultHtml += '<h5><i class="fas fa-check-circle"></i> ' + response.message + '</h5>';
                        resultHtml += '<ul>';
                        resultHtml += '<li>تم استيراد: ' + response.imported + ' موظف</li>';
                        if (response.skipped > 0) {
                            resultHtml += '<li>تم تخطي: ' + response.skipped + ' موظف (موجود مسبقاً)</li>';
                        }
                        resultHtml += '</ul>';

                        if (response.errors && response.errors.length > 0) {
                            resultHtml += '<details class="mt-3">';
                            resultHtml += '<summary>عرض الأخطاء (' + response.errors.length + ')</summary>';
                            resultHtml += '<ul class="mt-2" style="max-height: 200px; overflow-y: auto;">';
                            response.errors.forEach(function(error) {
                                resultHtml += '<li>' + error + '</li>';
                            });
                            resultHtml += '</ul>';
                            resultHtml += '</details>';
                        }

                        resultHtml += '</div>';
                        $('#import-result').html(resultHtml);

                        // Reload page after 3 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        $('#import-progress').addClass('d-none');
                        $('#import-result').removeClass('d-none');

                        var errorMsg = 'حدث خطأ أثناء الاستيراد';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors && xhr.responseJSON.errors.file) {
                                errorMsg = xhr.responseJSON.errors.file[0];
                            }
                        } else if (xhr.responseText) {
                            errorMsg = xhr.responseText;
                        }

                        $('#import-result').html('<div class="alert alert-danger"><h5><i class="fas fa-exclamation-circle"></i> خطأ</h5><p>' + errorMsg + '</p></div>');
                        $('#import-form-container').removeClass('d-none');
                        $('#import-btn').prop('disabled', false);
                    }
                });
            });

            // Reset import modal when closed
            $('#importModal').on('hidden.bs.modal', function() {
                $('#import-form')[0].reset();
                $('#import-progress').addClass('d-none');
                $('#import-form-container').removeClass('d-none');
                $('#import-result').addClass('d-none');
                $('#import-btn').prop('disabled', false);
            });
        </script>
    @endsection
