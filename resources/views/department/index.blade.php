@extends('layout.master')

@section('title')
{{ __('departments') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('departments') }}
@endsection

@section('css')
@include('components.admin.structure-styles')
@endsection

@section('content')
<div class="container-fluid">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="{{ __('Structural') }}"
        subtitle="{{ __('Manage organizational departments') }}"
    >
        <x-slot name="actions">
            <a href="#" class="btn btn-light-success font-weight-bolder btn-sm mr-2" data-toggle="collapse" data-target="#collapseSearch">
                <i class="fas fa-search ml-1"></i> {{ __('Search') }}
            </a>
            @can('store departments')
                <a class="btn btn-primary font-weight-bolder btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus ml-1"></i> {{ __('Add') }} {{ __('departments') }}
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-admin.structure-tabs active="departments" />

    <div id="collapseSearch" class="collapse mb-4">
        <div class="card card-body bg-light border-0 shadow-sm p-4 mb-4">
            <form action="{{ url()->current() }}" method="GET" class="form">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input name="name" type="text" class="form-control" placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary font-weight-bolder px-6">
                            <i class="fas fa-search ml-1"></i> {{ __('Search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($departments as $department)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <x-admin.structure-card
                :item="$department"
                type="department"
                :manager-name="$department->director?->employee?->name"
                :count="$department->sections_count"
                :count-label="__('sections')"
                count-icon="fa-th-list"
                edit-permission="update departments"
                delete-permission="delete departments"
            />
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-10">
                    <i class="fas fa-building icon-4x text-muted mb-4"></i>
                    <p class="text-muted font-weight-bold font-size-h6 mb-0">{{ __('no_records') }}</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($departments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {!! $departments->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
    @endif
</div>

@can('store departments')
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-5">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('Create departments') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('departments.store') }}" method="post">
                @csrf
                <div class="modal-body p-10">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">{{ __('name') }}</label>
                        <input class="form-control form-control-solid @error('name') is-invalid @enderror" type="text" name="name" required />
                        @error('name') <span class="text-danger mt-2 d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-10">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@foreach($departments as $department)
@can('update departments')
<div class="modal fade" id="editModal{{ $department->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info py-5">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('Edit') }}: {{ $department->name }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('departments.update', $department->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body p-10">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">{{ __('name') }}</label>
                        <input class="form-control form-control-solid @error('name') is-invalid @enderror" type="text" name="name" value="{{ $department->name }}" required />
                        @error('name') <span class="text-danger mt-2 d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light-info font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info font-weight-bold px-10">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endforeach
@endsection

@section('scripts')
<script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
