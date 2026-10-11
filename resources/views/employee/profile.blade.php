@extends('layout.master')

@section('title')
{{ __('profile_page') }}
@endsection

@section('page_title')
{{ __('Administrative Affairs') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('profile_page') }}
@endsection

@section('content')
<div class=" container ">
    <!--begin::Advance Table Widget 3-->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card card-custom card-stretch">
                <!--begin::Body-->
                <div class="card-body">
                    <!--begin::User-->
                    <div class="d-flex align-items-center position-relative">
                        <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                            <div class="symbol-label"
                                style="background-image:url('{{ asset('assets/bg/user.png') }}');border-radius:50%">
                            </div>
                            <i class="symbol-badge bg-success"></i>
                        </div>


                        <div>
                            <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">
                                {{ $employee->name }}
                            </a>
                            <div class="text-muted">
                                {{ $employee->job->name }}
                                <br>
                                {{ $employee->czn_id }}
                            </div>
                        </div>


                        @if (Auth::user()->can('update employees') || Auth::user()->employee->id == $employee->id)
                        <a class="btn btn-info font-weight-bolder font-size-sm position-absolute" style="top:0; right: 560px;"
                            data-toggle="modal" data-target="#editBasicDataModal">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                            fill="#000000" fill-rule="nonzero"
                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)">
                                        </path>
                                        <path
                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3">
                                        </path>
                                    </g>
                                </svg>
                            </span>
                            {{ __('تعديل البيانات الأساسية') }}
                        </a>
                        <a class="btn btn-danger font-weight-bolder font-size-sm position-absolute" style="top:0; right: 735px;"
                            href="{{ route('employees.index') }}">
                    
                            {{ __('Back to list') }}
                        </a>
             
                        <a class="btn btn-success font-weight-bolder font-size-sm position-absolute top-0 right-0"
                            data-toggle="modal" data-target="#createModalChangePassword">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Change Password') }}
                        </a>
                        <div class="modal fade " id="createModalChangePassword" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Change Password') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">



                                        <form method="POST" action="{{ route('user.password.update') }}">
                                            @csrf

                                            <div class="row mb-3">
                                                <label for="old_password"
                                                    class="col-md-4 col-form-label text-md-end">{{ __('old_password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="old_password" type="password"
                                                        class="form-control @error('old_password') is-invalid @enderror"
                                                        name="old_password" required>

                                                    @error('old_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="new_password"
                                                    class="col-md-4 col-form-label text-md-end">{{ __('new_password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="new_password" type="password"
                                                        class="form-control @error('new_password') is-invalid @enderror"
                                                        name="new_password" required>

                                                    @error('new_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="new_password_confirmation"
                                                    class="col-md-4 col-form-label text-md-end">{{ __('Confirm_Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="new_password_confirmation" type="password"
                                                        class="form-control" name="new_password_confirmation"
                                                        required autocomplete="new-password">
                                                </div>
                                            </div>

                                            <div class="row mb-0">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Change Password') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif

                        <!--begin::Edit Basic Data Modal-->
                        <div class="modal fade" id="editBasicDataModal" role="dialog" aria-labelledby="editBasicDataLabel"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 900px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBasicDataLabel">{{ __('update employees') }} -
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
                                                                <option value="male" {{ $employee->gender == 'male' ? 'selected' : '' }}>
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
                                                            <select class="form-control" name="department_id" required>
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
                                                            <select class="form-control" name="section_id">
                                                                <option value="">{{ __('Select') }}</option>
                                                                @foreach ($sections as $section)
                                                                    <option value="{{ $section->id }}"
                                                                        {{ $employee->section_id == $section->id ? 'selected' : '' }}>
                                                                        {{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ __('branch') }}</label>
                                                            <select class="form-control" name="branch_id">
                                                                <option value="">{{ __('Select') }}</option>
                                                                @foreach ($branches as $branch)
                                                                    <option value="{{ $branch->id }}"
                                                                        {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                                                        {{ $branch->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ __('job') }} <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="job_id" required>
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
                        <!--end::Edit Basic Data Modal-->

                    </div>

                    <!--end::User-->

                    <!--begin::Contact-->
                    <div class="py-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="font-weight-bold mr-2 d-inline block"
                                        style="width: 20%">{{ __('Email') }}</span>
                                    <a href="#" class="text-muted text-hover-primary">{{ $employee->email }}</a>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="font-weight-bold mr-2" style="width: 20%">{{ __('mobile') }}</span>
                                    <span class="text-muted">{{ $employee->mobile }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold mr-2" style="width: 20%">{{ __('address') }}</span>
                                    <span class="text-muted">{{ $employee->address }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="font-weight-bold mr-2 d-inline block"
                                        style="width: 20%">{{ __('birthdate') }}</span>
                                    <a href="#"
                                        class="text-muted text-hover-primary">{{ $employee->birthdate }}</a>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="font-weight-bold mr-2"
                                        style="width: 20%">{{ __('birthplace') }}</span>
                                    <span class="text-muted">{{ $employee->birthplace }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold mr-2" style="width: 20%">{{ __('salary') }}</span>
                                    <span class="text-muted">{{ $employee->salary }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Contact-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>
    <!--end::Header-->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('jobinfo') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        @if ($have_info == 0)
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModalJobInfo">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}
                        </a>
                        <div class="modal fade " id="createModalJobInfo" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create Job Info') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form" action="{{ route('jobinfo.store') }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row d-flex align-items-center">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="emp_number">{{ __('emp_number') }}</label>
                                                        <input
                                                            class="@error('emp_number') is-invalid @enderror form-control"
                                                            type="number" name="emp_number" id="emp_number"
                                                            value="{{ old('emp_number') }}"
                                                            placeholder="{{ __('emp_number') }}" />
                                                        @error('emp_number')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="type">{{ __('type') }}</label>
                                                        <x-installation-type />
                                                        @error('type')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shift_ids">{{ __('shift_id') }}</label>
                                                        <select
                                                            class="form-control selectpicker @error('shift_ids') is-invalid @enderror"
                                                            data-live-search="true" title="{{ __('Shifts') }}"
                                                            name="shift_ids[]" id="shift_id" multiple>
                                                            @foreach ($shifts as $Shift)
                                                            <option
                                                                {{ in_array($Shift->id, old('shift_ids', [])) ? 'selected' : '' }}
                                                                value="{{ $Shift->id }}">
                                                                {{ $Shift->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('shift_ids')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="current_job">{{ __('current_job') }}</label>
                                                        <input
                                                            class="@error('current_job') is-invalid @enderror form-control"
                                                            type="text" name="current_job" id="current_job"
                                                            value="{{ old('current_job') }}"
                                                            placeholder="{{ __('current_job') }}" />
                                                        @error('current_job')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('work_start_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('work_start_date') is-invalid @enderror form-control"
                                                                type="date" name="work_start_date"
                                                                id="work_start_date"
                                                                value="{{ old('work_start_date') }}"
                                                                placeholder="{{ __('work_start_date') }}" />
                                                        </div>
                                                        @error('work_start_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('ministry_date_appointment') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('ministry_date_appointment') is-invalid @enderror form-control"
                                                                type="date" name="ministry_date_appointment"
                                                                id="ministry_date_appointment"
                                                                value="{{ old('ministry_date_appointment') }}"
                                                                placeholder="{{ __('ministry_date_appointment') }}" />
                                                        </div>
                                                        @error('ministry_date_appointment')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('ministry_installation_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('ministry_installation_date') is-invalid @enderror form-control"
                                                                type="date" name="ministry_installation_date"
                                                                id="ministry_installation_date"
                                                                value="{{ old('ministry_installation_date') }}"
                                                                placeholder="{{ __('ministry_installation_date') }}" />
                                                        </div>
                                                        @error('ministry_installation_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note">{{ __('note') }}</label>
                                                        <input
                                                            class="@error('note') is-invalid @enderror form-control"
                                                            type="text" name="note" id="note"
                                                            value="{{ old('note') }}"
                                                            placeholder="{{ __('note') }}" />
                                                        @error('note')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer w-100">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>
                                            <input type="hidden" name="employee_id"
                                                value="{{ $employee->id }}">
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif


                        @if ($have_info != 0)
                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3" data-toggle="modal"
                            data-target="#editModal{{ $employee->EmployeeJobInfo->id }}">
                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24">
                                        </rect>
                                        <path
                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                            fill="#000000" fill-rule="nonzero"
                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                        </path>
                                        <path
                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    </g>
                                </svg>
                            </span>
                        </a>
                        <form class="d-inline"
                            action="{{ route('jobinfo.sync-related', $employee->id) }}"
                            method="POST"
                            onsubmit="return confirm('{{ __('Sync dependents and job history for this employee?') }}')">
                            @csrf
                            <button type="submit" class="btn btn-icon btn-light btn-hover-success btn-sm"
                                title="{{ __('Sync dependents & job history') }}">
                                <span class="svg-icon svg-icon-md svg-icon-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                        viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"></rect>
                                            <path
                                                d="M8.4329649,13.7209696 L11.2059577,16.4939624 L15.2923106,11.2336001 C15.6842034,10.7446244 16.3896718,10.6637789 16.8786474,11.0556717 C17.3676231,11.4475645 17.4484686,12.1530329 17.0565758,12.6420086 L11.8731063,19.0373441 C11.4932054,19.5114581 10.8022193,19.6064447 10.3106383,19.2537119 L6.31063834,16.4203786 C5.77634257,16.0369781 5.66757101,15.2865323 6.05097154,14.7522365 C6.43437206,14.2179407 7.18481791,14.1091692 7.71911368,14.4925697 L8.4329649,13.7209696 Z"
                                                fill="#000000" fill-rule="nonzero"
                                                transform="translate(11.594106, 15.002076) rotate(-11.000000) translate(-11.594106, -15.002076)">
                                            </path>
                                            <path
                                                d="M18.4,7 C19.2836556,7 20,7.7163444 20,8.6 L20,9.6 C20,10.4836556 19.2836556,11.2 18.4,11.2 C17.5163444,11.2 16.8,10.4836556 16.8,9.6 L16.8,8.6 C16.8,7.7163444 17.5163444,7 18.4,7 Z M5.6,7 C6.4836556,7 7.2,7.7163444 7.2,8.6 L7.2,9.6 C7.2,10.4836556 6.4836556,11.2 5.6,11.2 C4.7163444,11.2 4,10.4836556 4,9.6 L4,8.6 C4,7.7163444 4.7163444,7 5.6,7 Z"
                                                fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </form>
                        <div class="modal fade " id="editModal{{ $employee->EmployeeJobInfo->id }}"
                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('update jobinfo') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!--begin::Form-->
                                        <form
                                            action="{{ route('jobinfo.update', $employee->EmployeeJobInfo->id) }}"
                                            method="post" class="form">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="emp_number">{{ __('emp_number') }}</label>
                                                        <input
                                                            class="@error('emp_number') is-invalid @enderror form-control"
                                                            type="number" name="emp_number" id="emp_number"
                                                            value="{{ old('emp_number', $employee->EmployeeJobInfo->emp_number) }}"
                                                            placeholder="{{ __('emp_number') }}" />
                                                        @error('emp_number')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="type">{{ __('type') }}</label>
                                                        <select class="form-control form-control-lg"
                                                            name="type" id="type">
                                                            <option value="مصنف"
                                                                @if ($employee->EmployeeJobInfo->type == 'مصنف') @selected(true) @endif>
                                                                مصنف (مثبت) </option>
                                                            <option value="غير مصنف"
                                                                @if ($employee->EmployeeJobInfo->type == 'غير مصنف') @selected(true) @endif>
                                                                غير مصنف </option>
                                                            <option value="عقد موازي"
                                                                @if ($employee->EmployeeJobInfo->type == 'عقد موازي') @selected(true) @endif>
                                                                عقد موازي
                                                            </option>
                                                            <option value="عقد مقطوع"
                                                                @if ($employee->EmployeeJobInfo->type == 'عقد مقطوع') @selected(true) @endif>
                                                                عقد مقطوع </option>
                                                            <option value="عقد خاص"
                                                                @if ($employee->EmployeeJobInfo->type == 'عقد خاص') @selected(true) @endif>
                                                                عقد خاص </option>
                                                            <option value="كرت عمل يومي"
                                                                @if ($employee->EmployeeJobInfo->type == 'كرت عمل يومي') @selected(true) @endif>
                                                                كرت عمل يومي
                                                            </option>
                                                            <option value="متقاعد"
                                                                @if ($employee->EmployeeJobInfo->type == 'متقاعد') @selected(true) @endif>
                                                                متقاعد</option>
                                                        </select> @error('type')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shift_id">{{ __('shift_id') }}</label>
                                                        <select
                                                            class="form-control selectpicker @error('shift_ids') is-invalid @enderror"
                                                            data-live-search="true" title="{{ __('Shifts') }}"
                                                            name="shift_ids[]" id="shift_ids" multiple>
                                                            @foreach ($shifts as $shift)
                                                            <option
                                                                {{ in_array($shift->id, $employee->EmployeeJobInfo->shifts->pluck('id')->toArray()) ? 'selected' : '' }}
                                                                value="{{ $shift->id }}">
                                                                {{ $shift->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('shift_ids')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <input type="hidden" value="{{ $employee->id }}"
                                                        name="employee_id">
                                                    <div class="form-group">
                                                        <label for="current_job">{{ __('current_job') }}</label>
                                                        <input
                                                            class="@error('current_job') is-invalid @enderror form-control"
                                                            type="text" name="current_job" id="current_job"
                                                            value="{{ old('current_job', $employee->EmployeeJobInfo->current_job) }}"
                                                            placeholder="{{ __('current_job') }}" />
                                                        @error('current_job')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">

                                                    <div class="form-group">
                                                        <label>{{ __('work_start_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('work_start_date') is-invalid @enderror form-control"
                                                                type="date" name="work_start_date"
                                                                id="work_start_date"
                                                                value="{{ old('work_start_date', $employee->EmployeeJobInfo->work_start_date) }}" />
                                                        </div>
                                                        @error('work_start_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('ministry_date_appointment') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('ministry_date_appointment') is-invalid @enderror form-control"
                                                                type="date" name="ministry_date_appointment"
                                                                id="ministry_date_appointment"
                                                                value="{{ old('ministry_date_appointment', $employee->EmployeeJobInfo->ministry_date_appointment) }}" />
                                                        </div>
                                                        @error('ministry_date_appointment')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('ministry_installation_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('ministry_installation_date') is-invalid @enderror form-control"
                                                                type="date" name="ministry_installation_date"
                                                                id="ministry_installation_date"
                                                                value="{{ old('ministry_installation_date', $employee->EmployeeJobInfo->ministry_installation_date) }}" />
                                                        </div>
                                                        @error('ministry_installation_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note">{{ __('note') }}</label>
                                                        <input
                                                            class="@error('note') is-invalid @enderror form-control"
                                                            type="text" name="note" id="note"
                                                            value="{{ old('note', $employee->EmployeeJobInfo->note) }}" />
                                                        @error('note')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer w-100">
                                                    <button type="button"
                                                        class="btn btn-light-primary font-weight-bold"
                                                        data-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit"
                                                        class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row p-9">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center mb-2">
                            <span
                                class="font-weight-bold mr-2 d-inline block w-50 mb-2">{{ __('emp_number') }}</span>
                            <a href="#"
                                class="text-muted text-hover-primary">{{ $employee->EmployeeJobInfo?->emp_number }}</a>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="font-weight-bold mr-2 w-50  mb-2">{{ __('work_start_date') }}</span>
                            <span class="text-muted">{{ $employee->EmployeeJobInfo?->work_start_date }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span
                                class="font-weight-bold mr-2 w-50  mb-2">{{ __('ministry_date_appointment') }}</span>
                            <span
                                class="text-muted">{{ $employee->EmployeeJobInfo?->ministry_date_appointment }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="font-weight-bold mr-2 w-50  mb-2">{{ __('type') }}</span>
                            <span class="text-muted">{{ $employee->EmployeeJobInfo?->type }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span
                                class="font-weight-bold mr-2 w-50  mb-2">{{ __('ministry_installation_date') }}</span>
                            <span
                                class="text-muted">{{ $employee->EmployeeJobInfo?->ministry_installation_date }}</span>
                        </div>
                        @if ($have_info != 0)
                        <div class="d-flex align-items-center">
                            <span class="font-weight-bold mr-2 w-50  mb-2">{{ __('shifts') }}</span>
                            @foreach ($employee->EmployeeJobInfo?->shifts as $shift)
                            <span class="text-muted">{{ $shift->name }} , </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('employee_attachments') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModalAttachment">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade " id="createModalAttachment" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Create Attachment') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form" id="createAttachmentForm"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <label for="file_name">{{ __('file_name') }}</label>
                                                <input class="@error('file_name') is-invalid @enderror form-control"
                                                    type="text" name="file_name" id="file_name"
                                                    value="{{ old('file_name') }}"
                                                    placeholder="{{ __('file_name') }}" />
                                                @error('file_name')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="file">{{ __('file') }}</label>
                                                <input class="@error('file') is-invalid @enderror form-control"
                                                    type="file" name="file" id="file"
                                                    accept="application/pdf" value="{{ old('file') }}"
                                                    placeholder="{{ __('file') }}" />
                                                <small class="text-muted">{{ __('Maximum file size: 20 MB') }}</small>
                                                @error('file')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="modal-footer w-100">
                                                <button type="button" class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold" id="submitAttachment">{{ __('Save') }}</button>
                                            </div>
                                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-0 pb-3 card-stretch ">
                    <!--begin::Table-->
                    <div class="table-responsive p-3">
                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 250px">{{ __('file_name') }}</th>
                                    <th style="min-width: 100px">{{ __('file') }}</th>
                                    <th style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->attachments as $attachment)
                                <tr>
                                    <td class="pl-0 py-8">{{ $attachment->file_name }}</td>
                                    <td class="pl-0 py-8">
                                        <a href="{{ route('attachments.download', $attachment->id) }}" target="_blank">
                                            <svg width="50" height="50" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                    fill="currentColor"></path>
                                                <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor">
                                                </path>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="pr-0 ">
                                        {{-- edit-update --}}
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModalAttachment{{ $attachment->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade " id="editModalAttachment{{ $attachment->id }}"
                                            role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            {{ __('update attachments') }} -
                                                            {{ $attachment->name }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true" class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!--begin::Form-->
                                                        <form
                                                            action="{{ route('attachments.update', $attachment->id) }}"
                                                            enctype="multipart/form-data" method="post"
                                                            class="form updateAttachmentForm" novalidate>
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="card-body">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="file_name">{{ __('file_name') }}</label>
                                                                    <input
                                                                        class="@error('file_name') is-invalid @enderror form-control"
                                                                        type="text" name="file_name"
                                                                        id="file_name"
                                                                        value="{{ $attachment->file_name }}"
                                                                        placeholder="{{ __('file_name') }}" />
                                                                    @error('file_name')
                                                                    <span
                                                                        class="text-sm text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <label
                                                                        for="file">{{ __('file') }}</label>
                                                                    <input
                                                                        class="@error('file') is-invalid @enderror form-control"
                                                                        type="file" name="file"
                                                                        id="file" accept="application/pdf"
                                                                        value="{{ old('file') }}"
                                                                        placeholder="{{ __('file') }}" />
                                                                    @error('file')
                                                                    <span
                                                                        class="text-sm text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <a href=" {{ asset('storage/' . $attachment->file) }}"
                                                                    target="_blank">
                                                                    <svg width="50" height="50"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        {{-- DELETE --}}

                                        <button class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                            type="button"
                                            onclick="deleteAttachment({{ $attachment->id }})">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24">
                                                        </rect>
                                                        <path
                                                            d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                            fill="#000000" fill-rule="nonzero"></path>
                                                        <path
                                                            d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                            fill="#000000" opacity="0.3"></path>
                                                </g>
                                            </svg> </span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
            </div>
        </div>
    </div>
    @if ($have_info)
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('jobHistory') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModaljobHistory">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade" id="createModaljobHistory" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create jobHistory') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('job_history.store') }}" method="POST"
                                            class="form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="employee_id"
                                                value="{{ $employee->id }}">
                                            <div class="form-group ">
                                                <label>{{ __('type') }}</label>
                                                <div class="input-group">
                                                    <select class="form-control selectpicker"
                                                        data-live-search="true" title="اختر نوع التغيير ..."
                                                        name="type" id="type">
                                                        <option value="promotion">{{ __('promotion') }}</option>
                                                        <option value="assigned">{{ __('assigned') }}</option>
                                                        <option value="transfer">{{ __('transfer') }}</option>
                                                        <option value="turning_off">{{ __('turning_off') }}
                                                        </option>
                                                        <option value="back">{{ __('back') }}</option>
                                                    </select>
                                                </div>
                                                @error('type')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('date') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('date') is-invalid @enderror form-control"
                                                        type="date" name="date" id="date"
                                                        value="{{ old('date') }}" />
                                                </div>
                                                @error('date')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('attachment') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('attachment') is-invalid @enderror form-control"
                                                        type="file" name="attachment" id="attachment"
                                                        value="{{ old('attachment') }}" />
                                                </div>
                                                @error('attachment')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-0 pb-3 card-stretch ">
                    <!--begin::Table-->
                    <div class="table-responsive p-3">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: ">{{ __('type') }}</th>
                                    <th style="min-width: ">{{ __('job') }}</th>
                                    <th style="min-width: ">{{ __('category') }}</th>
                                    <th style="min-width: ">{{ __('degree') }}</th>
                                    <th style="min-width: ">{{ __('section') }}</th>
                                    <th style="min-width: ">{{ __('department') }}</th>
                                    <th style="min-width: ">{{ __('branch') }}</th>
                                    <th style="min-width: ">{{ __('status') }}</th>
                                    <th style="min-width: ">{{ __('bonus_percent') }}</th>
                                    <th style="min-width: ">{{ __('date') }}</th>
                                    <th style="min-width: ">{{ __('attachment') }}</th>
                                    <th style="min-width: ">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->employeeJobHistories as $emp_history)
                                <tr>
                                    <td class="pl-0 py-8">{{ __($emp_history->type) }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->job }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->category }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->degree }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->section }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->department }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->branch }}</td>
                                    <td class="pl-0 py-8">{{ __($emp_history->status) }}</td>
                                    <td class="pl-0 py-8">{{ $emp_history->bonus_percent }}</td>
                                    <td class="pl-0 py-8">{{ date('d-m-Y', strtotime($emp_history->date)) }}
                                    <td class="pl-0 py-8">
                                        @if ($emp_history->attachment != null)
                                        <a href=" {{ asset('storage/' . $emp_history->attachment) }}"
                                            target="_blank">
                                            <svg width="50" height="50" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                    fill="currentColor"></path>
                                                <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                    fill="currentColor">
                                                </path>
                                            </svg>
                                        </a>
                                        @endif
                                    </td>
                                    </td>
                                    <td class="pr-0 ">
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModaljobHistory{{ $emp_history->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            opacity="0.3">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade "
                                            id="editModaljobHistory{{ $emp_history->id }}" role="dialog"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            {{ __('update jobHistory') }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true" class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('job_history.update', $emp_history->id) }}"
                                                            method="POST" class="form"
                                                            enctype="multipart/form-data">
                                                            @method('put')
                                                            @csrf
                                                            <input type="hidden" name="employee_id"
                                                                value="{{ $employee->id }}">
                                                            <div class="form-group ">
                                                                <label>{{ __('type') }}</label>
                                                                <div class="input-group">
                                                                    <select class="form-control selectpicker"
                                                                        data-live-search="true"
                                                                        title="اختر نوع التغيير ..."
                                                                        name="type" id="type">
                                                                        <option value="promotion"
                                                                            @if ($emp_history->type == 'promotion') @selected(true) @endif>
                                                                            {{ __('promotion') }}
                                                                        </option>
                                                                        <option value="assigned"
                                                                            @if ($emp_history->type == 'assigned') @selected(true) @endif>
                                                                            {{ __('assigned') }}
                                                                        </option>
                                                                        <option value="transfer"
                                                                            @if ($emp_history->type == 'transfer') @selected(true) @endif>
                                                                            {{ __('transfer') }}
                                                                        </option>
                                                                        <option value="turning_off"
                                                                            @if ($emp_history->type == 'turning_off') @selected(true) @endif>
                                                                            {{ __('turning_off') }}
                                                                        </option>
                                                                        <option value="back"
                                                                            @if ($emp_history->type == 'back') @selected(true) @endif>
                                                                            {{ __('back') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                @error('type')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('date') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('date') is-invalid @enderror form-control"
                                                                        type="date" name="date"
                                                                        id="date"
                                                                        value="{{ old('date', $emp_history->date) }}" />
                                                                </div>
                                                                @error('date')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('attachment') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('attachment') is-invalid @enderror form-control"
                                                                        type="file" name="attachment"
                                                                        id="attachment"
                                                                        value="{{ old('attachment', $emp_history->attachment) }}" />
                                                                </div>
                                                                @error('attachment')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <form class="d-inline-block"
                                            action="{{ route('job_history.destroy', $emp_history) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                type="submit"
                                                onclick=" return confirm('{{ __('delete_message') }}')">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                        version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24">
                                                            </rect>
                                                            <path
                                                                d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                fill="#000000" fill-rule="nonzero"></path>
                                                            <path
                                                                d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                fill="#000000" opacity="0.3"></path>
                                                        </g>
                                                    </svg> </span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('bonus') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModalBonus">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade" id="createModalBonus" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create bonus_employee') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('bonus_employee.store') }}" method="POST"
                                            class="form">
                                            @csrf
                                            <input type="hidden" name="employee_id"
                                                value="{{ $employee->id }}">
                                            <div class="form-group ">
                                                <label>{{ __('bonus') }}</label>
                                                <div class="input-group">
                                                    <select class="form-control selectpicker"
                                                        data-live-search="true" title="ادخل العلاوة ..."
                                                        name="bonus_id" id="bonus_id">
                                                        @foreach ($bonuses as $bon)
                                                        <option value="{{ $bon->id }}">
                                                            {{ $bon->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('branch_id')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('total_amount') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('total_amount') is-invalid @enderror form-control"
                                                        type="number" name="total_amount" id="total_amount"
                                                        value="{{ old('total_amount') }}" />
                                                </div>
                                                @error('total_amount')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-0 pb-3 card-stretch ">
                    <!--begin::Table-->
                    <div class="table-responsive p-3">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 250px">{{ __('name') }}</th>
                                    <th style="min-width: 100px">{{ __('total_amount') }}</th>
                                    <th style="min-width: 100px">{{ __('created_at') }}</th>
                                    <th style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->employeeBonus as $emp_bonus)
                                <tr>
                                    <td class="pl-0 py-8">{{ $emp_bonus->bonus->name }}</td>
                                    <td class="pl-0 py-8">{{ $emp_bonus->total_amount }}</td>
                                    <td class="pl-0 py-8">
                                        {{ date('d-m-Y', strtotime($emp_bonus->created_at)) }}
                                    </td>
                                    <td class="pr-0 ">
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModalBonus{{ $emp_bonus->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            opacity="0.3">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade " id="editModalBonus{{ $emp_bonus->id }}"
                                            role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            {{ __('Create bonus') }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true" class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('bonus_employee.update', $emp_bonus->id) }}"
                                                            method="POST" class="form">
                                                            @method('put')
                                                            @csrf
                                                            <input type="hidden" name="employee_id"
                                                                value="{{ $employee->id }}">

                                                            <div class="form-group ">
                                                                <label>{{ __('bonus') }}</label>
                                                                <div class="input-group">
                                                                    <select class="form-control selectpicker"
                                                                        data-live-search="true"
                                                                        title="ادخل العلاوة ..."
                                                                        name="bonus_id" id="bonus_id">
                                                                        @foreach ($bonuses as $bon)
                                                                        <option
                                                                            value="{{ $bon->id }}"
                                                                            {{ $emp_bonus->bonus_id == $bon->id ? 'selected' : '' }}>
                                                                            {{ $bon->name }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @error('bonus_id')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>

                                                            <div class="form-group ">
                                                                <label>{{ __('total_amount') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('total_amount') is-invalid @enderror form-control"
                                                                        type="number" name="total_amount"
                                                                        id="total_amount"
                                                                        value="{{ $emp_bonus->total_amount }}" />
                                                                </div>
                                                                @error('total_amount')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <form class="d-inline-block"
                                            action="{{ route('bonus_employee.destroy', $emp_bonus) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                type="submit"
                                                onclick=" return confirm('{{ __('delete_message') }}')">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                        version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24">
                                                            </rect>
                                                            <path
                                                                d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                fill="#000000" fill-rule="nonzero"></path>
                                                            <path
                                                                d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                fill="#000000" opacity="0.3"></path>
                                                        </g>
                                                    </svg> </span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('dependents') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModaldependents">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employee.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade" id="createModaldependents" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create dependents') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('dependents.store') }}" method="POST"
                                            class="form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="employee_id"
                                                value="{{ $employee->id }}">
                                            <div class="form-group ">
                                                <label>{{ __('name') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('name') is-invalid @enderror form-control"
                                                        type="text" name="name" id="name"
                                                        value="{{ old('name') }}" />
                                                </div>
                                                @error('name')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('czn_id') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('czn_id') is-invalid @enderror form-control"
                                                        type="text" name="czn_id" id="czn_id"
                                                        value="{{ old('czn_id') }}" />
                                                </div>
                                                @error('czn_id')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('gender') }}</label>
                                                <div class="input-group">
                                                    <select
                                                        class="@error('gender') is-invalid @enderror form-control"
                                                        type="text" name="gender" id="gender"
                                                        value="{{ old('gender') }}">
                                                        <option value="male">{{ __('Male') }}</option>
                                                        <option value="female">{{ __('Female') }}</option>
                                                    </select>
                                                </div>
                                                @error('gender')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('birth') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('birth') is-invalid @enderror form-control"
                                                        type="date" name="birth" id="birth"
                                                        value="{{ old('birth') }}" />
                                                </div>
                                                @error('birth')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('relation') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('relation') is-invalid @enderror form-control"
                                                        type="text" name="relation" id="relation"
                                                        value="{{ old('relation') }}" />
                                                </div>
                                                @error('relation')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('status') }}</label>
                                                <div class="input-group">
                                                    <select
                                                        class="@error('status') is-invalid @enderror form-control"
                                                        type="text" name="status" id="status"
                                                        value="{{ old('status') }}">
                                                        <option value="Active">{{ __('Active') }}</option>
                                                        <option value="InActive">{{ __('InActive') }}</option>
                                                    </select>
                                                </div>
                                                @error('status')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group ">
                                                <label>{{ __('Attchment') }}</label>
                                                <div class="input-group">
                                                    <input
                                                        class="@error('attachment') is-invalid @enderror form-control"
                                                        type="file" name="attachment" id="attachment"
                                                        value="{{ old('attachment') }}" />
                                                </div>
                                                @error('attachment')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-0 pb-3 card-stretch ">
                    <!--begin::Table-->
                    <div class="table-responsive p-3">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 250px">{{ __('name') }}</th>
                                    <th style="min-width: 100px">{{ __('czn_id') }}</th>
                                    <th style="min-width: 100px">{{ __('gender') }}</th>
                                    <th style="min-width: 120px">{{ __('birth') }}</th>
                                    <th style="min-width: 120px">{{ __('relation') }}</th>
                                    <th style="min-width: 120px">{{ __('status') }}</th>
                                    <th style="min-width: 120px">{{ __('attachment') }}</th>
                                    <th style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->employeeDependents as $dependents)
                                <tr>
                                    <td class="pl-0 py-8">{{ $dependents->name }}</td>
                                    <td class="pl-0 py-8">{{ $dependents->czn_id }}</td>
                                    <td class="pl-0 py-8">{{ __($dependents->gender) }}</td>
                                    <td class="pl-0 py-8">{{ $dependents->birth }}</td>
                                    <td class="pl-0 py-8">{{ $dependents->relation }}</td>
                                    <td class="pl-0 py-8">{{ __($dependents->status) }}</td>
                                    <td class="pl-0 py-8">
                                        @if ($dependents->attachment != null)
                                        <a href=" {{ asset('storage/' . $dependents->attachment) }}"
                                            target="_blank">
                                            <svg width="50" height="50" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                    fill="currentColor"></path>
                                                <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                    fill="currentColor">
                                                </path>
                                            </svg>
                                        </a>
                                        @else
                                        {{ __('no_file') }}
                                        @endif

                                    </td>
                                    <td class="pr-0 ">
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModaldependents{{ $dependents->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                    version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            opacity="0.3">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade "
                                            id="editModaldependents{{ $dependents->id }}" role="dialog"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            {{ __('Create bonus') }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true" class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('dependents.update', $dependents) }}"
                                                            method="POST" class="form"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="employee_id"
                                                                value="{{ $employee->id }}">
                                                            <div class="form-group ">
                                                                <label>{{ __('name') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('name') is-invalid @enderror form-control"
                                                                        type="text" name="name"
                                                                        id="name"
                                                                        value="{{ old('name', $dependents->name) }}" />
                                                                </div>
                                                                @error('name')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('czn_id') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('czn_id') is-invalid @enderror form-control"
                                                                        type="number" name="czn_id"
                                                                        id="czn_id"
                                                                        value="{{ old('czn_id', $dependents->czn_id) }}" />
                                                                </div>
                                                                @error('czn_id')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('gender') }}</label>
                                                                <div class="input-group">
                                                                    <select
                                                                        class="@error('gender') is-invalid @enderror form-control"
                                                                        type="text" name="gender"
                                                                        id="gender"
                                                                        value="{{ old('gender') }}">
                                                                        <option value="male"
                                                                            @if ($dependents->gender == 'male') @selected(true) @endif>
                                                                            {{ __('Male') }}
                                                                        </option>
                                                                        <option value="female"
                                                                            @if ($dependents->gender == 'female') @selected(true) @endif>
                                                                            {{ __('Female') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                @error('gender')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('birth') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('birth') is-invalid @enderror form-control"
                                                                        type="date" name="birth"
                                                                        id="birth"
                                                                        value="{{ old('birth', $dependents->birth) }}" />
                                                                </div>
                                                                @error('birth')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('relation') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('relation') is-invalid @enderror form-control"
                                                                        type="text" name="relation"
                                                                        id="relation"
                                                                        value="{{ old('relation', $dependents->relation) }}" />
                                                                </div>
                                                                @error('relation')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('status') }}</label>
                                                                <div class="input-group">
                                                                    <select
                                                                        class="@error('status') is-invalid @enderror form-control"
                                                                        type="text" name="status"
                                                                        id="status"
                                                                        value="{{ old('status') }}">
                                                                        <option value="Active"
                                                                            @if ($dependents->status == 'Active') @selected(true) @endif>
                                                                            {{ __('Active') }}
                                                                        </option>
                                                                        <option value="InActive"
                                                                            @if ($dependents->status == 'InActive') @selected(true) @endif>
                                                                            {{ __('InActive') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                @error('status')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group ">
                                                                <label>{{ __('Attchment') }}</label>
                                                                <div class="input-group">
                                                                    <input
                                                                        class="@error('attachment') is-invalid @enderror form-control"
                                                                        type="file" name="attachment"
                                                                        id="attachment"
                                                                        value="{{ old('attachment') }}" />
                                                                </div>
                                                                @error('attachment')
                                                                <span
                                                                    class="text-sm text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form class="d-inline-block"
                                            action="{{ route('dependents.destroy', $dependents) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                type="submit"
                                                onclick=" return confirm('{{ __('delete_message') }}')">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                        version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24">
                                                            </rect>
                                                            <path
                                                                d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                fill="#000000" fill-rule="nonzero"></path>
                                                            <path
                                                                d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                fill="#000000" opacity="0.3"></path>
                                                        </g>
                                                    </svg> </span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>

            </div>
        </div>
    </div>
    @if (($employee->department?->director?->id ?? null) != null || ($employee->section?->director?->id ?? null) != null)
    @if (Auth::user()->employee->id == $employee->id)
    <div class="row">
        <div class="col-md-8">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('EmployeeHoliday') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModal">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employeeholiday.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none"
                                        fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade " id="createModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create Employee Holiday') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form class="form"
                                            action="{{ route('EmployeeHolidays.store') }}"
                                            method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="hidden" name="employee_id"
                                                        value="{{ $employee->id }}">

                                                    <div class="form-group ">
                                                        <label>{{ __('holiday_id') }}</label>
                                                        <div class="input-group">
                                                            <select class="form-control selectpicker"
                                                                data-live-search="true"
                                                                title="ادخل نوع الإجازة ..."
                                                                name="holiday_id" id="holiday_id">
                                                                @foreach ($holidays as $holiday)
                                                                <option value="{{ $holiday->id }}">
                                                                    {{ $holiday->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        @error('employee_id')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('start_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('start_date') is-invalid @enderror form-control"
                                                                type="date" name="start_date"
                                                                id="start_date"
                                                                value="{{ old('start_date') }}" />
                                                        </div>
                                                        @error('start_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('end_date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('end_date') is-invalid @enderror form-control"
                                                                type="date" name="end_date"
                                                                id="end_date"
                                                                value="{{ old('end_date') }}" />
                                                        </div>
                                                        @error('end_date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <div class="form-group">
                                                        <label>{{ __('vacation_days') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('vacation_days') is-invalid @enderror form-control"
                                                                type="number" name="vacation_days"
                                                                id="vacation_days"
                                                                value="{{ old('vacation_days', $employee->EmployeeJobInfo->vacation_days) }}"
                                                                placeholder="{{ __('vacation_days') }}"
                                                                readonly />
                                                        </div>
                                                        @error('vacation_days')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('spent_balance') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('spent_balance') is-invalid @enderror form-control"
                                                                type="number" name="spent_balance"
                                                                id="spent_balance"
                                                                value="{{ old('spent_balance') }}"
                                                                placeholder="{{ __('spent_balance') }}"
                                                                readonly />
                                                        </div>
                                                        @error('spent_balance')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('remaining_balance') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('remaining_balance') is-invalid @enderror form-control"
                                                                type="number" name="remaining_balance"
                                                                id="remaining_balance"
                                                                value="{{ old('remaining_balance') }}"
                                                                placeholder="{{ __('remaining_balance') }}"
                                                                readonly />
                                                        </div>
                                                        @error('remaining_balance')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('note') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('note') is-invalid @enderror form-control"
                                                                type="text" name="note"
                                                                id="note"
                                                                value="{{ old('note') }}"
                                                                placeholder="{{ __('note') }}" />
                                                        </div>
                                                        @error('note')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 100px">{{ __('id') }}</th>
                                    <th style="min-width: 50px">{{ __('holiday_id') }}</th>
                                    <th style="min-width: 10px">{{ __('vacation_days') }}</th>
                                    <th style="min-width: 10px">{{ __('spent_balance') }}</th>
                                    <th style="min-width: 10px">{{ __('remaining_balance') }}</th>
                                    <th style="min-width: 10px">{{ __('status') }}</th>
                                    <th style="min-width: 50px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->employeeHoliday as $employeeholiday)
                                <tr>
                                    <td class="pl-0 py-8">{{ $employeeholiday->id }}</td>
                                    <td class="pl-0 py-8">{{ $employeeholiday->holiday->name }}</td>
                                    <td class="pl-0 py-8">{{ $employeeholiday->vacation_days }}</td>
                                    <td class="pl-0 py-8">{{ $employeeholiday->spent_balance }}</td>
                                    <td class="pl-0 py-8">{{ $employeeholiday->remaining_balance }}
                                    </td>
                                    <td class="pl-0 py-8"><span
                                            class="label label-lg
                                                    @if ($employeeholiday->status == 'pending') label-light-warning @endif
                                                    @if ($employeeholiday->status == 'accepted') label-light-success @endif
                                                    @if ($employeeholiday->status == 'rejected') label-light-danger @endif
                                                    label-inline">{{ __($employeeholiday->status) }}</span>
                                    </td>
                                    <td class="pr-0 ">
                                        {{-- edit-update --}}
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModal{{ $employeeholiday->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1"
                                                        fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade "
                                            id="editModal{{ $employeeholiday->id }}"
                                            role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document"
                                                style="max-width: 800px">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="exampleModalLabel">
                                                            {{ __('update employeeholidays') }} -
                                                            {{ $employeeholiday->employee_id }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true"
                                                                class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!--begin::Form Update-->
                                                        <form method="post" class="form"
                                                            action="{{ route('EmployeeHolidays.update', $employeeholiday->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <!-- Employee Dropdown -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('employee_id') }}</label>
                                                                        <div class="input-group">
                                                                            <input type="hidden"
                                                                                name="employee_id"
                                                                                value="{{ $employee->id }}">
                                                                        </div>
                                                                        @error('employee_id')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- Holiday Type Dropdown -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('holiday_id') }}</label>
                                                                        <div class="input-group">
                                                                            <select
                                                                                class="form-control selectpicker"
                                                                                data-live-search="true"
                                                                                title="ادخل نوع الإجازة ..."
                                                                                name="holiday_id"
                                                                                id="holiday_id">
                                                                                @foreach ($holidays as $holiday)
                                                                                <option
                                                                                    value="{{ $holiday->id }}"
                                                                                    {{ $employeeholiday->holiday_id == $holiday->id ? 'selected' : '' }}>
                                                                                    {{ $holiday->name }}
                                                                                </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        @error('holiday_id')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- Note -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('note') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('note') is-invalid @enderror form-control"
                                                                                type="text"
                                                                                name="note"
                                                                                id="note"
                                                                                value="{{ old('note', $employeeholiday->note) }}" />
                                                                        </div>
                                                                        @error('note')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- Start Date -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('start_date') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('start_date') is-invalid @enderror form-control"
                                                                                type="date"
                                                                                name="start_date"
                                                                                id="update_start_date"
                                                                                value="{{ old('start_date', $employeeholiday->start_date) }}" />
                                                                        </div>
                                                                        @error('start_date')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Vacation Days -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('vacation_days') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('vacation_days') is-invalid @enderror form-control"
                                                                                type="number"
                                                                                name="vacation_days"
                                                                                id="update_vacation_days"
                                                                                data-initial-balance="{{ $employeeholiday->remaining_balance }}"
                                                                                value="{{ old('vacation_days', $employeeholiday->vacation_days) }}" />
                                                                        </div>
                                                                        @error('vacation_days')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- Spent Balance -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('spent_balance') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('spent_balance') is-invalid @enderror form-control"
                                                                                type="number"
                                                                                name="spent_balance"
                                                                                id="update_spent_balance"
                                                                                value="{{ old('spent_balance', $employeeholiday->spent_balance) }}" />
                                                                        </div>
                                                                        @error('spent_balance')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- Remaining Balance -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('remaining_balance') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('remaining_balance') is-invalid @enderror form-control"
                                                                                type="number"
                                                                                name="remaining_balance"
                                                                                id="update_remaining_balance"
                                                                                value="{{ old('remaining_balance', $employeeholiday->remaining_balance) }}" />
                                                                        </div>
                                                                        @error('remaining_balance')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <!-- End Date -->
                                                                    <div class="form-group">
                                                                        <label>{{ __('end_date') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('end_date') is-invalid @enderror form-control"
                                                                                type="date"
                                                                                name="end_date"
                                                                                id="update_end_date"
                                                                                value="{{ old('end_date', $employeeholiday->end_date) }}" />
                                                                        </div>
                                                                        @error('end_date')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- DELETE --}}

                                        <form class="d-inline-block"
                                            action="{{ route('EmployeeHolidays.destroy', $employeeholiday) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                type="submit"
                                                onclick="return confirm('{{ __('delete_message') }}')">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="24px" height="24px"
                                                        viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1"
                                                            fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24"
                                                                height="24">
                                                            </rect>
                                                            <path
                                                                d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                fill="#000000" fill-rule="nonzero">
                                                            </path>
                                                            <path
                                                                d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                fill="#000000" opacity="0.3">
                                                            </path>
                                                        </g>
                                                    </svg> </span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Body-->
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('MainEmployeeHoliday') }}</span>
                    </h3>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 50px">{{ __('holiday_name') }}</th>
                                    <th style="min-width: 50px">{{ __('start_date') }}</th>
                                    <th style="min-width: 50px">{{ __('end_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->employeeMainHoliday as $employeeMainHoliday)
                                <tr>
                                    <td class="pl-0 py-8">
                                        {{ $employeeMainHoliday->mainHoliday->holiday_name }}
                                    </td>
                                    <td class="pl-0 py-8">
                                        {{ $employeeMainHoliday->mainHoliday->start_date }}
                                    </td>
                                    <td class="pl-0 py-8">
                                        {{ $employeeMainHoliday->mainHoliday->end_date }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span
                            class="card-label font-weight-bolder text-dark">{{ __('EmployeePermission') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-success font-weight-bolder font-size-sm" data-toggle="modal"
                            data-target="#createModalEmployeePermission">
                            <span class="svg-icon svg-icon-md svg-icon-white">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Add-employeeholiday.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none"
                                        fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            {{ __('Create') }}</a>

                        <div class="modal fade " id="createModalEmployeePermission" tabindex="-1"
                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 800px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('Create EmployeePermissions') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form class="form"
                                            action="{{ route('EmployeePermissions.store') }}"
                                            method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __('name') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('name') is-invalid @enderror form-control"
                                                                type="text" name="name"
                                                                id="name"
                                                                value="{{ old('name') }}"
                                                                placeholder="{{ __('name') }}" />
                                                        </div>
                                                        @error('name')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ __('date') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('date') is-invalid @enderror form-control"
                                                                type="date" name="date"
                                                                id="date"
                                                                value="{{ old('date') }}" />
                                                        </div>
                                                        @error('date')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('premissiontype') }}</label>
                                                        <div class="input-group">
                                                            <select class="form-control form-control-lg"
                                                                name="type" id="type">
                                                                <option value="">اختر</option>
                                                                <option value="إذن عمل"
                                                                    {{ old('type') === 'إذن عمل' ? 'selected' : '' }}>
                                                                    إذن عمل</option>
                                                                <option value="إذن خاص"
                                                                    {{ old('type') === 'إذن خاص' ? 'selected' : '' }}>
                                                                    إذن خاص</option>
                                                            </select>
                                                        </div>
                                                        @error('type')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('start_time') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('start_time') is-invalid @enderror form-control"
                                                                type="time" name="start_time"
                                                                id="start_time"
                                                                value="{{ old('start_time') }}" />
                                                        </div>
                                                        @error('start_time')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('end_time') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                class="@error('end_time') is-invalid @enderror form-control"
                                                                type="time" name="end_time"
                                                                id="end_time"
                                                                value="{{ old('end_time') }}" />
                                                        </div>
                                                        @error('end_time')
                                                        <span
                                                            class="text-sm text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table
                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 50px">{{ __('name') }}</th>
                                    <th style="min-width: 10px">{{ __('date') }}</th>
                                    <th style="min-width: 10px">{{ __('type') }}</th>
                                    <th style="min-width: 10px">{{ __('status') }}</th>
                                    <th style="min-width: 10px">{{ __('start_time') }}</th>
                                    <th style="min-width: 10px">{{ __('end_time') }}</th>
                                    <th style="min-width: 50px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->EmployeePermissions as $EmployeePermission)
                                <tr>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->name }}</td>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->date }}</td>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->type }}</td>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->status }}</td>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->start_time }}</td>
                                    <td class="pl-0 py-8">{{ $EmployeePermission->end_time }}</td>

                                    <td class="pr-0 ">
                                        {{-- edit-update --}}
                                        <a class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3"
                                            data-toggle="modal"
                                            data-target="#editModalEmployeePermission{{ $EmployeePermission->id }}">
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1"
                                                        fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24">
                                                        </rect>
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                        </path>
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="modal fade "
                                            id="editModalEmployeePermission{{ $EmployeePermission->id }}"
                                            role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document"
                                                style="max-width: 800px">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="exampleModalLabel">
                                                            {{ __('update EmployeePermission') }} -
                                                            {{ $EmployeePermission->id }}
                                                        </h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <i aria-hidden="true"
                                                                class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!--begin::Form Update-->
                                                        <form method="post" class="form"
                                                            action="{{ route('EmployeePermissions.update', $employeeholiday->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden"
                                                                name="employeeholiday"
                                                                value="{{ $employeeholiday->id }}">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>{{ __('name') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('name') is-invalid @enderror form-control"
                                                                                type="text"
                                                                                name="name"
                                                                                id="name"
                                                                                value="{{ old('name', $EmployeePermission->name) }}"
                                                                                placeholder="{{ __('name') }}" />
                                                                        </div>
                                                                        @error('name')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>{{ __('date') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('date') is-invalid @enderror form-control"
                                                                                type="date"
                                                                                name="date"
                                                                                id="date"
                                                                                value="{{ old('date', $EmployeePermission->date) }}" />
                                                                        </div>
                                                                        @error('date')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('premissiontype') }}</label>
                                                                        <div class="input-group">

                                                                            <select
                                                                                class="form-control form-control-lg"
                                                                                name="type"
                                                                                id="type">
                                                                                <option
                                                                                    value="">
                                                                                    اختر</option>
                                                                                <option
                                                                                    value="إذن عمل"
                                                                                    {{ $EmployeePermission->type ===
                                                                                                        'إذن
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            عمل'
                                                                                                            ? 'selected'
                                                                                                            : '' }}>
                                                                                    إذن عمل
                                                                                </option>
                                                                                <option
                                                                                    value="إذن خاص"
                                                                                    {{ $EmployeePermission->type ===
                                                                                                        'إذن
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            خاص'
                                                                                                            ? 'selected'
                                                                                                            : '' }}>
                                                                                    إذن خاص
                                                                                </option>
                                                                            </select>
                                                                            <input
                                                                                class="@error('type') is-invalid @enderror form-control"
                                                                                type="date"
                                                                                name="type"
                                                                                id="type"
                                                                                value="{{ old('type', $EmployeePermission->type) }}" />
                                                                        </div>
                                                                        @error('type')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('start_time') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('start_time') is-invalid @enderror form-control"
                                                                                type="time"
                                                                                name="start_time"
                                                                                id="start_time"
                                                                                value="{{ old('start_time', $EmployeePermission->start_time) }}" />
                                                                        </div>
                                                                        @error('start_time')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('end_time') }}</label>
                                                                        <div class="input-group">
                                                                            <input
                                                                                class="@error('end_time') is-invalid @enderror form-control"
                                                                                type="time"
                                                                                name="end_time"
                                                                                id="end_time"
                                                                                value="{{ old('end_time', $EmployeePermission->end_time) }}" />
                                                                        </div>
                                                                        @error('end_time')
                                                                        <span
                                                                            class="text-sm text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-light-primary font-weight-bold"
                                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- DELETE --}}

                                        <form class="d-inline-block"
                                            action="{{ route('EmployeePermissions.destroy', $EmployeePermission) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                type="submit"
                                                onclick="return confirm('{{ __('delete_message') }}')">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="24px" height="24px"
                                                        viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1"
                                                            fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24"
                                                                height="24">
                                                            </rect>
                                                            <path
                                                                d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                fill="#000000" fill-rule="nonzero">
                                                            </path>
                                                            <path
                                                                d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                fill="#000000" opacity="0.3">
                                                            </path>
                                                        </g>
                                                    </svg> </span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-4" colspan="4">
                                        {{ __('no_records') }}
                                    </td>
                                </tr>
                                @endforelse{{-- --}}
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- سجل الحضور والانصراف -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('سجل الحضور والانصراف') }}</span>
                    </h3>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('التاريخ') }}</th>
                                    <th>{{ __('وقت الدخول') }}</th>
                                    <th>{{ __('وقت الخروج') }}</th>
                                    <th>{{ __('عدد ساعات العمل') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($attendanceRecords && $attendanceRecords->count() > 0)
                                @foreach($attendanceRecords as $record)
                                <tr>
                                    <td>{{ $record->date ?? '-' }}</td>
                                    <td>{{ $record->entry_time ?? '-' }}</td>
                                    <td>{{ $record->exit_time ?? '-' }}</td>
                                    <td>{{ $record->work_hours ?? '-' }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="4">{{ __('لا توجد سجلات حضور') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>

    <!-- سجل العمليات (Audit Log) -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">{{ __('سجل العمليات') }}</span>
                        <span class="text-muted mt-1">{{ __('جميع العمليات التي قام بها الموظف أو تمت على بياناته') }}</span>
                    </h3>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 15%">{{ __('العملية') }}</th>
                                    <th style="width: 30%">{{ __('الكيان المتأثر') }}</th>
                                    <th style="width: 35%">{{ __('التفاصيل') }}</th>
                                    <th style="width: 20%">{{ __('تاريخ ووقت العملية') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($auditLogs && $auditLogs->count() > 0)
                                @foreach($auditLogs as $log)
                                <tr>
                                    <td>
                                        @if(in_array($log->action, ['create', 'created']))
                                        <span class="badge badge-success">{{ __('إضافة') }}</span>
                                        @elseif(in_array($log->action, ['update', 'updated']))
                                        <span class="badge badge-warning">{{ __('تعديل') }}</span>
                                        @elseif(in_array($log->action, ['delete', 'deleted']))
                                        <span class="badge badge-danger">{{ __('حذف') }}</span>
                                        @elseif(in_array($log->action, ['login']))
                                        <span class="badge badge-info">{{ __('تسجيل دخول') }}</span>
                                        @else
                                        <span class="badge badge-secondary">{{ $log->action }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->table_name ?? '-' }}</td>
                                    <td class="text-muted" style="font-size: 0.9em">
                                        @if(isset($log->properties) && is_object($log->properties))
                                        @php
                                        // Handle both Auditable trait (with attributes) and manual logging (without attributes)
                                        if(isset($log->properties->attributes)) {
                                            $changes = $log->properties->attributes ?? [];
                                        } else {
                                            $changes = [];
                                        }
                                        
                                        // For manual logging, show the description instead
                                        if(count($changes) > 0) {
                                            $firstFew = array_slice($changes, 0, 3);
                                            foreach($firstFew as $key => $value) {
                                                if(!is_null($value)) {
                                                    echo htmlspecialchars(mb_strimwidth($key . ': ' . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value), 0, 80, '...'));
                                                    echo '<br>';
                                                }
                                            }
                                            if(count($changes) > 3) {
                                                echo '<span class="text-muted">...' . (count($changes) - 3) . ' ' . __('حقول أخرى') . '</span>';
                                            }
                                        } else {
                                            // Show description for manual logging
                                            echo htmlspecialchars($log->description ?? '-');
                                        }
                                        @endphp
                                        @else
                                        {{ $log->description ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at instanceof \Carbon\Carbon ? $log->created_at->format('Y-m-d H:i:s') : ($log->created_at ?? '-') }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="4">{{ __('لا توجد سجلات عمليات') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>

    @endif
    <!--begin::Body-->

    <!--end::Body-->
</div>
<!--end::Advance Table Widget 3-->
@endsection

@section('scripts')
<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
<!--end::Page Scripts-->


{{-- اضهار رصيد الاجازات --}}
<script>
    function handleEmployeeSelection() {
        var selectedOption = $("#employee_id option:selected");

        var vacationDays = selectedOption.data("vacation-days");

        $("#vacation_days").val(vacationDays);
    }

    $("#employee_id").on("change", handleEmployeeSelection);
</script>

<!-- حساب عملية رصيد الإجازة و المستنفذ و المتبقي -->
<script>
    function dateDifferenceInDays(startDate, endDate) {
        const oneDay = 24 * 60 * 60 * 1000;
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffInDays = Math.round((end - start) / oneDay);
        return diffInDays;
    }

    function updateBalances() {
        const startDateEl = document.getElementById('start_date');
        const endDateEl = document.getElementById('end_date');
        const vacationDaysEl = document.getElementById('vacation_days');
        
        // Only run if these elements exist on the page
        if (!startDateEl || !endDateEl || !vacationDaysEl) {
            return;
        }
        
        const startDate = startDateEl.value;
        const endDate = endDateEl.value;
        const holidayBalance = parseFloat(vacationDaysEl.value);
        const spentBalanceInput = document.getElementById('spent_balance');
        const remainingBalanceInput = document.getElementById('remaining_balance');

        const daysSpent = dateDifferenceInDays(startDate, endDate);
        spentBalanceInput.value = daysSpent;

        const remainingBalance = holidayBalance - daysSpent;
        remainingBalanceInput.value = remainingBalance;
    }


    // Add event listeners to the date inputs only if they exist
    const startDateEl = document.getElementById('start_date');
    const endDateEl = document.getElementById('end_date');
    
    if (startDateEl && endDateEl) {
        startDateEl.addEventListener('change', updateBalances);
        endDateEl.addEventListener('change', updateBalances);

        // Initial update on page load
        updateBalances();
    }
</script>

{{-- For update --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateBalances() {
            const startDateEl = document.getElementById('update_start_date');
            const endDateEl = document.getElementById('update_end_date');
            const vacationDaysEl = document.getElementById('update_vacation_days');
                
            // Only run if these elements exist
            if (!startDateEl || !endDateEl || !vacationDaysEl) {
                return;
            }
                
            var startDate = new Date(startDateEl.value);
            var endDate = new Date(endDateEl.value);
            var vacationDays = parseFloat(vacationDaysEl.value);
            var spentBalance = (endDate - startDate) / (1000 * 60 * 60 * 24); // Convert milliseconds to days
    
            document.getElementById('update_spent_balance').value = spentBalance;
            document.getElementById('update_remaining_balance').value = vacationDays - spentBalance;
        }
    
        // Initial calculation
        updateBalances();
    
        // Event handlers for date fields
        const updateStartDateEl = document.getElementById('update_start_date');
        const updateEndDateEl = document.getElementById('update_end_date');
            
        if (updateStartDateEl) {
            updateStartDateEl.addEventListener('change', function() {
                updateBalances();
            });
        }
            
        if (updateEndDateEl) {
            updateEndDateEl.addEventListener('change', function() {
                updateBalances();
            });
        }
    });

    // $(document).ready(function() {
    //     // Function to calculate and update spent_balance and remaining_balance
    //     function updateBalances() {
    //         var startDate = new Date($('#update_start_date').val());
    //         var endDate = new Date($('#update_end_date').val());
    //         var vacationDays = parseFloat($('#update_vacation_days').val());
    //         var spentBalance = (endDate - startDate) / (1000 * 60 * 60 * 24); // Convert milliseconds to days

    //         if (spentBalance > vacationDays) {
    //             spentBalance = vacationDays;
    //         }

    //         $('#update_spent_balance').val(spentBalance);
    //         $('#update_remaining_balance').val(vacationDays - spentBalance);
    //     }

    //     // Initial calculation
    //     updateBalances();

    //     // Event handlers for date fields
    //     $('#update_start_date, #update_end_date').on('change', function() {
    //         updateBalances();
    //     });
    // });

    // Setup global AJAX headers for all requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });

    // AJAX for adding attachment
    $('#createAttachmentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $('#submitAttachment');
        var originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('جاري الحفظ...');
        
        $.ajax({
            url: '{{ route('attachments.store') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Success response:', response);
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $('#createModalAttachment').modal('hide');
                    $('#createAttachmentForm')[0].reset();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload page after success message closes
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message || 'حدث خطأ!',
                        confirmButtonText: 'حسناً'
                    });
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.status, xhr.responseText);
                submitBtn.prop('disabled', false).text(originalText);
                
                var message = 'حدث خطأ غير متوقع!';
                
                // Handle validation errors (422)
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    // Get first error message
                    var firstErrorKey = Object.keys(errors)[0];
                    message = errors[firstErrorKey][0];
                    
                    // Show validation errors in SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'خطأ في التحقق',
                        html: '<div class="text-right"><strong>الأخطاء:</strong><ul class="text-right mt-2">' + 
                              Object.values(errors).flat().map(err => '<li>' + err + '</li>').join('') + 
                              '</ul></div>',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                } else if (xhr.status === 500) {
                    message = 'خطأ في الخادم! يرجى المحاولة لاحقاً.';
                    console.error('Server error details:', xhr.responseText);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: message,
                    confirmButtonText: 'حسناً'
                });
            }
        });
    });

    // AJAX for updating attachment
    $('.updateAttachmentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        var modal = $(this).closest('.modal');
        
        submitBtn.prop('disabled', true).text('جاري الحفظ...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Success response:', response);
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    modal.modal('hide');
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload page after success message closes
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message || 'حدث خطأ!',
                        confirmButtonText: 'حسناً'
                    });
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.status, xhr.responseText);
                submitBtn.prop('disabled', false).text(originalText);
                
                var message = 'حدث خطأ غير متوقع!';
                
                // Handle validation errors (422)
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    // Get first error message
                    var firstErrorKey = Object.keys(errors)[0];
                    message = errors[firstErrorKey][0];
                    
                    // Show validation errors in SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'خطأ في التحقق',
                        html: '<div class="text-right"><strong>الأخطاء:</strong><ul class="text-right mt-2">' + 
                              Object.values(errors).flat().map(err => '<li>' + err + '</li>').join('') + 
                              '</ul></div>',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                } else if (xhr.status === 500) {
                    message = 'خطأ في الخادم! يرجى المحاولة لاحقاً.';
                    console.error('Server error details:', xhr.responseText);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: message,
                    confirmButtonText: 'حسناً'
                });
            }
        });
    });

    // AJAX for deleting attachment
    window.deleteAttachment = function(attachmentId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من استعادة هذا المرفق!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('attachments.destroy', '') }}/' + attachmentId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Delete success:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page after success message closes
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: response.message || 'حدث خطأ!',
                                confirmButtonText: 'حسناً'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('Delete error:', xhr.status, xhr.responseText);
                        var message = 'حدث خطأ غير متوقع!';
                        
                        // Handle validation errors (422)
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var firstErrorKey = Object.keys(errors)[0];
                            message = errors[firstErrorKey][0];
                        } else if (xhr.status === 500) {
                            message = 'خطأ في الخادم! يرجى المحاولة لاحقاً.';
                            console.error('Server error details:', xhr.responseText);
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: message,
                            confirmButtonText: 'حسناً'
                        });
                    }
                });
            }
        });
    };
    // AJAX for updating attachment
$(document).on('submit', 'form[action*="attachments"][method="post"]', function(e) {
    // تأكد أن الفورم يحتوي على method PUT (تعديل وليس إضافة)
    if ($(this).find('input[name="_method"][value="PUT"]').length === 0) {
        return; // اتركه يكمل بشكل عادي إذا لم يكن تعديلاً
    }
    
    e.preventDefault();
    
    var form = $(this);
    var formData = new FormData(this);
    var actionUrl = form.attr('action');
    var modal = form.closest('.modal');
    
    $.ajax({
        url: actionUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                modal.modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: response.message || 'حدث خطأ!',
                    confirmButtonText: 'حسناً'
                });
            }
        },
        error: function(xhr) {
            var message = 'حدث خطأ غير متوقع!';
            
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                Swal.fire({
                    icon: 'warning',
                    title: 'خطأ في التحقق',
                    html: '<ul class="text-right mt-2">' + 
                          Object.values(errors).flat().map(err => '<li>' + err + '</li>').join('') + 
                          '</ul>',
                    confirmButtonText: 'حسناً'
                });
                return;
            } else if (xhr.status === 500) {
                message = 'خطأ في الخادم! يرجى المحاولة لاحقاً.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: message,
                confirmButtonText: 'حسناً'
            });
        }
    });
});
</script>
<!--end::Page Scripts-->
@endsection