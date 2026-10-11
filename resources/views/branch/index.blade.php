@extends('layout.master')

@section('title')
{{ __('branches') }}
@endsection

@section('page_title')
{{ __('branches') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('branches') }}
@endsection

@section('css')
@include('components.admin.structure-styles')
@endsection

@section('content')
<div class="container-fluid">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="{{ __('Structural') }}"
        subtitle="{{ __('Manage organizational branches and their parent sections') }}"
    >
        <x-slot name="actions">
            <a href="#" class="btn btn-light-success font-weight-bolder btn-sm mr-2" data-toggle="collapse" data-target="#collapseSearch">
                <i class="fas fa-search ml-1"></i> {{ __('Search') }}
            </a>
            @can('store branches')
                <a class="btn btn-primary font-weight-bolder btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus ml-1"></i> {{ __('Add') }} {{ __('branches') }}
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-admin.structure-tabs active="branches" />

    <div id="collapseSearch" class="collapse mb-4 {{ request()->anyFilled(['name', 'section_id']) ? 'show' : '' }}">
        <div class="card card-body bg-light border-0 shadow-sm p-4 mb-4">
            <form action="{{ url()->current() }}" method="GET" class="form">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input name="name" type="text" class="form-control form-control-solid" placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">{{ __('section') }}</label>
                        <select class="form-control selectpicker" data-live-search="true" title="{{ __('All') }}" name="section_id">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
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
        @forelse($branches as $branch)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <x-admin.structure-card
                :item="$branch"
                type="branch"
                :manager-name="$branch->director?->employee?->name"
                :count="$branch->employees_count"
                :count-label="__('employees')"
                count-icon="fa-users"
                edit-permission="update branches"
                delete-permission="delete branches"
            />
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-10">
                    <i class="fas fa-code-branch icon-4x text-muted mb-4"></i>
                    <p class="text-muted font-weight-bold font-size-h6 mb-0">{{ __('no_records') }}</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($branches->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {!! $branches->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
    @endif
</div>

@can('store branches')
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-4">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('Create branches') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('branches.store') }}" method="post">
                @csrf
                <div class="modal-body p-10">
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input class="form-control form-control-solid" type="text" name="name" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('sections') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" title="{{ __('Select') }}..." name="section_id" required>
                            @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
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

@foreach($branches as $branch)
@can('update branches')
<div class="modal fade" id="editModal{{ $branch->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info py-4">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('update branches') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('branches.update', $branch->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body p-10">
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('name') }}</label>
                        <input class="form-control form-control-solid" type="text" name="name" value="{{ $branch->name }}" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('sections') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" name="section_id" required>
                            @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ $branch->section_id == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
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
