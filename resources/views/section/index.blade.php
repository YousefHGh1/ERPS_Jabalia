@extends('layout.master')

@section('title')
{{ __('sections') }}
@endsection

@section('page_title')
{{ __('sections') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('sections') }}
@endsection

@section('css')
@include('components.admin.structure-styles')
@endsection

@section('content')
<div class="container-fluid">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="{{ __('Structural') }}"
        subtitle="{{ __('Manage organizational sections and their parent departments') }}"
    >
        <x-slot name="actions">
            <a href="#" class="btn btn-light-success font-weight-bolder btn-sm mr-2" data-toggle="collapse" data-target="#collapseSearch">
                <i class="fas fa-search ml-1"></i> {{ __('Search') }}
            </a>
            @can('store sections')
                <a class="btn btn-primary font-weight-bolder btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus ml-1"></i> {{ __('Add') }} {{ __('sections') }}
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-admin.structure-tabs active="sections" />

    <div id="collapseSearch" class="collapse mb-4 {{ request()->anyFilled(['name', 'department_id']) ? 'show' : '' }}">
        <div class="card card-body bg-light border-0 shadow-sm p-4 mb-4">
            <form action="{{ url()->current() }}" method="GET" class="form">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input name="name" type="text" class="form-control form-control-solid" placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">{{ __('department') }}</label>
                        <select class="form-control selectpicker" data-live-search="true" title="{{ __('All') }}" name="department_id">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
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
        @forelse($sections as $section)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <x-admin.structure-card
                :item="$section"
                type="section"
                :manager-name="$section->director?->employee?->name"
                :count="$section->branches_count"
                :count-label="__('branches')"
                count-icon="fa-code-branch"
                edit-permission="update sections"
                delete-permission="delete sections"
            />
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-10">
                    <i class="fas fa-th-list icon-4x text-muted mb-4"></i>
                    <p class="text-muted font-weight-bold font-size-h6 mb-0">{{ __('no_records') }}</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($sections->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {!! $sections->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
    @endif
</div>

@can('store sections')
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-4">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('Create sections') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('sections.store') }}" method="post">
                @csrf
                <div class="modal-body p-10">
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input class="form-control form-control-solid" type="text" name="name" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('departments') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" title="{{ __('Select') }}..." name="department_id" required>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-10">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@foreach($sections as $section)
@can('update sections')
<div class="modal fade" id="editModal{{ $section->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info py-4">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('update sections') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('sections.update', $section->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body p-10">
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input class="form-control form-control-solid" type="text" name="name" value="{{ $section->name }}" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('departments') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" name="department_id" required>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ $section->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
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
