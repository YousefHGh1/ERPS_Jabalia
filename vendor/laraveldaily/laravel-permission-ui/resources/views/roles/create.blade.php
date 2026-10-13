@extends('layout.master')

@section('title')
    {{ __('Administrative Affairs') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('Administrative Affairs') }}
@endsection

@section('content')
    <div class=" container ">
        <!--begin::Advance Table Widget 3-->
        <div class="card card-custom gutter-b">
            <!--begin::Header-->
            <div class="card-header border-0 py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">{{ __('roles') }}</span>
                </h3>
                <div class="card-toolbar">
                    @can('index roles')
                        <a class="btn btn-primary font-weight-bolder font-size-sm"
                            href="{{ route(config('permission_ui.route_name_prefix') . 'roles.index') }}">

                            {{ __('Index') }}</a>
                    @endcan

                </div>
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <form class="form" action="{{ route(config('permission_ui.route_name_prefix') . 'roles.store') }}"
                method="post">
                @csrf

                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>{{ __('roles') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" ype="text" name="name" id="name"
                                    value="{{ old('name') }}" required />
                                <div class="input-group-append"><span class="input-group-text"><i
                                            class="la la-bookmark-o"></i></span></div>
                            </div>
                            @error('name')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if (collect($permissionCategories)->count())
                        <div class="space-y-2">
                            <label class="checkbox checkbox-outline pb-5">

                                <input class="all_ch" for="permissions" type="checkbox" id="all_type_requests" />
                                <span></span>
                                {{ __('permissions') }}

                            </label>

                            <div class="row">
                                @foreach ($permissionCategories as $category => $permissions)
                                    <div class="col-md-3">
                                        <div class="space-y-2">

                                            <div class="pb-5">
                                                <label class="checkbox checkbox-outline">
                                                    <input type="checkbox" id="all_type_requests" class="all_ch"/>
                                                    <span></span>
                                                    {{ __($category) }}
                                                </label>
                                            </div>
                                            <div id="Requests_types_menu">
                                                @foreach ($permissions as $id => $name)
                                                    <div class="inline-flex space-x-1">
                                                        <input
                                                            class="ch rounded-md border-gray-300 shadow-sm permission-checkbox"
                                                            type="checkbox" name="permissions[]"
                                                            id="permission-{{ $id }}"
                                                            value="{{ $id }}" @checked(old('permission-' . $id))>
                                                        <label class="text-sm font-medium text-gray-700"
                                                            for="permission-{{ $id }}">{{ $name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-success mr-2">{{ __('Save') }}</button>
                            <button type="reset" class="btn btn-secondary">{{ __('Cancel') }}</button>
                        </div>

                    </div>
                </div>
            </form>
            <!--end::Body-->
        </div>
        <!--end::Advance Table Widget 3-->

    </div>
@endsection

@section('scripts')
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
    <div class="form-group Section_ row">


    </div>
    <script>
        var checkAlltypeRequests = document.querySelector("#all_type_requests")
        var checktypeRequests = document.querySelectorAll("#Requests_types_menu .ch")

        checkAlltypeRequests.addEventListener('click', function() {
            checktypeRequests.forEach(function(e) {
                e.checked = checkAlltypeRequests.checked
            })
        });
    </script>
    <!--end::Page Scripts-->
@endsection
