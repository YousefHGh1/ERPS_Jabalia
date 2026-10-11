<style>
    .card-body {
        padding: 0;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .modal-title {
        color: white;
    }

    .close {
        color: white;
    }
</style>
<div class="modal fade" id="createModal" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder">{{ __('Create') }} {{ __('employee') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-8">
                <form action="{{ route('employees.store') }}" method="post" class="form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('czn_id') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="number" name="czn_id"
                                        id="czn_id_create" required value="{{ old('czn_id') }}"
                                        placeholder="{{ __('czn_id') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('name') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="text" name="name"
                                        id="name_create" required value="{{ old('name') }}"
                                        placeholder="{{ __('name') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('Email') }}</label>
                                    <input class="form-control form-control-solid" type="email" name="email"
                                        id="email_create" value="{{ old('email') }}"
                                        placeholder="{{ __('Email') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('gender') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" name="gender" id="gender_create"
                                        required>
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('status') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" name="status" required>
                                        <option value="Active">{{ __('Active') }}</option>
                                        <option value="InActive">{{ __('InActive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('birthplace') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="text" name="birthplace"
                                        id="birthplace_create" required value="{{ old('birthplace') }}"
                                        placeholder="{{ __('birthplace') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('address') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="text" name="address"
                                        id="address_create" required value="{{ old('address') }}"
                                        placeholder="{{ __('address') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('place_of_work') }}</label>
                                    <input class="form-control form-control-solid" type="text" name="place_of_work"
                                        id="place_of_work_create" value="{{ old('place_of_work') }}"
                                        placeholder="{{ __('place_of_work') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('mobile') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="text" name="mobile"
                                        required value="{{ old('mobile') }}" placeholder="{{ __('mobile') }}" />
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('birthdate') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="date" name="birthdate"
                                        id="birthdate_create" required value="{{ old('birthdate') }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('salary') }} <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-solid" type="number" step="0.01"
                                        name="salary" required value="{{ old('salary') }}"
                                        placeholder="{{ __('salary') }}" />
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('department') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid selectpicker" name="department_id"
                                        id="department_id_create" data-live-search="true" required>
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('section') }}</label>
                                    <select class="form-control form-control-solid selectpicker"
                                        data-live-search="true" name="section_id" id="section_id_create">
                                        <option value="">{{ __('Select') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('branch') }}</label>
                                    <select class="form-control form-control-solid selectpicker"
                                        data-live-search="true" name="branch_id" id="branch_id_create">
                                        <option value="">{{ __('Select') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('job') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid selectpicker"
                                        data-live-search="true" name="job_id" required>
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light-primary font-weight-bold px-8 py-3"
                            data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit"
                            class="btn btn-primary font-weight-bold px-8 py-3">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach ($employees as $employee)
    <div class="modal fade" id="editModal{{ $employee->id }}" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('update employees') }} -
                        {{ $employee->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('employees.update', $employee->id) }}" method="post" class="form">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('czn_id') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" name="czn_id"
                                            value="{{ $employee->czn_id }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name"
                                            value="{{ $employee->name }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Email') }}</label>
                                        <input class="form-control" type="email" name="email"
                                            value="{{ $employee->email }}" />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="gender" required>
                                            <option value="male"
                                                {{ $employee->gender == 'male' ? 'selected' : '' }}>
                                                {{ __('Male') }}</option>
                                            <option value="female"
                                                {{ $employee->gender == 'female' ? 'selected' : '' }}>
                                                {{ __('Female') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" required>
                                            <option value="Active"
                                                {{ $employee->status == 'Active' ? 'selected' : '' }}>
                                                {{ __('Active') }}</option>
                                            <option value="InActive"
                                                {{ $employee->status == 'InActive' ? 'selected' : '' }}>
                                                {{ __('InActive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('birthplace') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="birthplace"
                                            value="{{ $employee->birthplace }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('address') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="address"
                                            value="{{ $employee->address }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('place_of_work') }}</label>
                                        <input class="form-control" type="text" name="place_of_work"
                                            value="{{ $employee->place_of_work }}" />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="mobile"
                                            value="{{ $employee->mobile }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('birthdate') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" name="birthdate"
                                            value="{{ $employee->birthdate }}" required />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('salary') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" name="salary"
                                            value="{{ $employee->salary }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('department') }} <span class="text-danger">*</span></label>
                                        <select class="form-control department-edit"
                                            data-live-search="true" name="department_id"
                                            data-id="{{ $employee->id }}"
                                            data-initial-department="{{ $employee->department_id }}"
                                            id="department_id_edit_{{ $employee->id }}" required>
                                            <option value="">{{ __('Select') }}</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}"
                                                    {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('section') }}</label>
                                        <select class="form-control section-edit" data-live-search="true"
                                            name="section_id" data-id="{{ $employee->id }}"
                                            data-current="{{ $employee->section_id }}"
                                            data-initial-section="{{ $employee->section_id }}"
                                            id="section_id_edit_{{ $employee->id }}">
                                            <option value="">{{ __('Select') }}</option>
                                            @if ($employee->section && ! $sections->where('department_id', $employee->department_id)->where('id', $employee->section_id)->count())
                                                <option value="{{ $employee->section_id }}" selected>
                                                    {{ $employee->section->name }}</option>
                                            @endif
                                            @foreach ($sections as $section)
                                                @if ($section->department_id == $employee->department_id)
                                                    <option value="{{ $section->id }}"
                                                        {{ $employee->section_id == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('branch') }}</label>
                                        <select class="form-control branch-edit" data-live-search="true"
                                            name="branch_id" data-current="{{ $employee->branch_id }}"
                                            id="branch_id_edit_{{ $employee->id }}">
                                            <option value="">{{ __('Select') }}</option>
                                            @if ($employee->branch && ! $branches->where('section_id', $employee->section_id)->where('id', $employee->branch_id)->count())
                                                <option value="{{ $employee->branch_id }}" selected>
                                                    {{ $employee->branch->name }}</option>
                                            @endif
                                            @foreach ($branches as $branch)
                                                @if ($branch->section_id == $employee->section_id)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('job') }} <span class="text-danger">*</span></label>
                                        <select class="form-control selectpicker" data-live-search="true"
                                            name="job_id" required>
                                            <option value="">{{ __('Select') }}</option>
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}"
                                                    {{ $employee->job_id == $job->id ? 'selected' : '' }}>
                                                    {{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit"
                                class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Import Modal -->
<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder">{{ __('Import Employees') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-8">
                <div id="import-progress" class="d-none">
                    <div class="text-center mb-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-2">{{ __('Importing employees, please wait...') }}</p>
                    </div>
                </div>
                <div id="import-form-container">
                    <form id="import-form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">{{ __('Select File') }} <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-solid" accept=".csv,.xlsx,.xls" required>
                            <small class="form-text text-muted">{{ __('Supported formats: CSV, Excel (xlsx/xls). Maximum file size: 10MB') }}</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            {{ __('The file must contain the following columns in Arabic:') }}
                            <br>
                            <small>اسم الموظف, هوية, الدائرة, القسم, المسمى الوظيفي المعتمد, تاريخ الميلاد, جنس, رقم الهوية, رقم الجوال</small>
                        </div>
                        <div class="form-group">
                            <a href="{{ asset('templates/employee_import_template.csv') }}" class="btn btn-light-primary btn-sm" download>
                                <i class="fas fa-download ml-1"></i> {{ __('Download Template') }}
                            </a>
                        </div>
                    </form>
                </div>
                <div id="import-result" class="d-none"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light-primary font-weight-bold px-8 py-3"
                    data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="import-btn"
                    class="btn btn-success font-weight-bold px-8 py-3">{{ __('Import') }}</button>
            </div>
        </div>
    </div>
</div>
