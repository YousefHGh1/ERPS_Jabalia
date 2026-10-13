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
                    <span class="card-label font-weight-bolder text-dark">{{ __('permissions') }}</span>
                </h3>
                <div class="card-toolbar">
                    @can('index permissions')
                        <a class="btn btn-primary font-weight-bolder font-size-sm"
                            href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.index') }}">

                            {{ __('Index') }}</a>
                    @endcan

                </div>
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <form class="form" action="{{ route(config('permission_ui.route_name_prefix') . 'permissions.store') }}"
                method="post">
                @csrf

                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>{{ __('permissions') }}</label>
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


                    @if ($roles->count())
                        <div class="space-y-2">
                            <label class="block text-base font-medium text-gray-700"
                                for="permissions">{{ __('roles') }}</label>
                            <div class="space-x-2">
                                @foreach ($roles as $id => $name)
                                    <div class="inline-flex space-x-1">
                                        <input class="rounded-md border-gray-300 shadow-sm" type="checkbox" name="roles[]"
                                            id="role-{{ $id }}" value="{{ $id }}"
                                            @checked(old('role-' . $id))>
                                        <label class="text-sm font-medium text-gray-700"
                                            for="role-{{ $id }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                </div>
                <div class="card-footer">
                    <div class="row">
                        {{-- <div class="col-lg-4"> </div> --}}
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
    <!--end::Page Scripts-->
@endsection
