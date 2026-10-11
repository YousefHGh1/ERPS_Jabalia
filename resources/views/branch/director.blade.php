@extends('layout.master')

@section('title')
{{ __('branch director') }}
@endsection

@section('page_title')
{{ __('branch director') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('branch director') }}
@endsection

@section('css')
@include('components.admin.structure-styles')
@endsection

@section('content')
<div class="container-fluid">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="{{ __('branch director') }}"
        subtitle="{{ __('Assign directors to organizational branches') }}"
    >
        <x-slot name="actions">
            @can('store branchdirector')
                <a class="btn btn-primary font-weight-bolder btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus ml-1"></i> {{ __('Create') }}
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-admin.director-tabs active="branch" />

    <div class="row">
        @forelse($directors as $director)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card admin-structure-card h-100">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title font-weight-bolder text-dark mb-0">
                            <i class="fas fa-code-branch text-primary ml-1"></i>
                            {{ $director->branch->name ?? __('N/A') }}
                        </h5>
                        <div class="admin-card-actions">
                            @can('update branchdirector')
                            <button type="button" class="btn btn-icon btn-sm btn-light-primary" data-toggle="modal" data-target="#editModal{{ $director->id }}" title="{{ __('Edit') }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete branchdirector')
                            <form action="{{ route('branchdirector.destroy', $director->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-light-danger" onclick="return confirm('{{ __('confirm_message') }}')" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    <x-admin.manager-info :name="$director->employee->name ?? null" :label="__('employee')" />
                    <span class="badge badge-light-{{ $director->director_type === 'acting' ? 'warning' : 'success' }} mt-2">
                        {{ __($director->director_type) }}
                    </span>
                    <div class="d-flex align-items-center text-muted font-size-sm mt-2">
                        <i class="fas fa-briefcase ml-2" style="width: 1rem;"></i>
                        <span>{{ $director->employee->job->name ?? '---' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @can('update branchdirector')
        <div class="modal fade" id="editModal{{ $director->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-info py-4">
                        <h5 class="modal-title text-white font-weight-bolder">{{ __('update branch director') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('branchdirector.update', $director->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-10">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('branch_id') }}</label>
                                <select class="form-control selectpicker w-100" data-live-search="true" name="branch_id" required>
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $director->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('employee_id') }}</label>
                                <select class="form-control selectpicker w-100" data-live-search="true" name="employee_id" required>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $director->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('director_type') }}</label>
                                <select class="form-control" name="director_type" required>
                                    <option value="permanent" {{ $director->director_type === 'permanent' ? 'selected' : '' }}>{{ __('permanent') }}</option>
                                    <option value="acting" {{ $director->director_type === 'acting' ? 'selected' : '' }}>{{ __('acting') }}</option>
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
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-10">
                    <i class="fas fa-user-tie icon-4x text-muted mb-4"></i>
                    <p class="text-muted font-weight-bold font-size-h6 mb-0">{{ __('no_records') }}</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if(isset($directors) && $directors->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {!! $directors->links('pagination::bootstrap-5') !!}
    </div>
    @endif
</div>

@can('store branchdirector')
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-4">
                <h5 class="modal-title text-white font-weight-bolder">{{ __('Create branch director') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('branchdirector.store') }}" method="post">
                @csrf
                <div class="modal-body p-10">
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('branch_id') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" title="{{ __('Select') }}..." name="branch_id" required>
                            @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('employee_id') }}</label>
                        <select class="form-control selectpicker w-100" data-live-search="true" title="{{ __('Select') }}..." name="employee_id" required>
                            @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('director_type') }}</label>
                        <select class="form-control" name="director_type" required>
                            <option value="permanent">{{ __('permanent') }}</option>
                            <option value="acting">{{ __('acting') }}</option>
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
@endsection

@section('scripts')
<script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
