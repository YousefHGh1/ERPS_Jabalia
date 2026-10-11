@extends('layout.master')

@section('title')
    {{ __('jobs') }}
@endsection

@section('page_title')
    {{ __('jobs') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('jobs') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('jobs') }}"
                subtitle="{{ __('Manage job titles and their career categories') }}"
            >
                <x-slot name="actions">
                    <button class="btn btn-light-primary font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </button>
                                        @can('store jobs')
                                            <a class="btn btn-create-main font-weight-bolder" data-toggle="modal" data-target="#createModal">
                                                <i class="fas fa-plus"></i> {{ __('Create') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                @if ($errors->any())
                    <div class="alert alert-custom alert-light-danger fade show mb-10" role="alert">
                        <div class="alert-icon">
                            <span class="svg-icon svg-icon-3x svg-icon-danger">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                            </span>
                        </div>
                        <div class="alert-text font-weight-bold">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    <i class="fas fa-times"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                @endif

                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('name') || request()->filled('category_id') ? 'show' : '' }}">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ URL::current() }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="font-weight-bold">{{ __('name') }}</label>
                                    <div class="input-icon">
                                        <input name="name" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="font-weight-bold">{{ __('category') }}</label>
                                    <select class="form-control selectpicker" data-live-search="true"
                                        title="{{ __('All') }}" name="category_id">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8 w-100">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    @forelse($jobs as $job)
                        <div class="col-lg-6 col-xl-4">
                            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-5">
                                        <div>
                                            <h4 class="card-title font-weight-bolder mb-1">
                                                <a href="{{ route('jobs.show', $job) }}"
                                                    class="text-dark text-hover-primary text-decoration-none">
                                                    {{ $job->name }}
                                                </a>
                                            </h4>
                                            <p class="text-muted mb-2">{{ $job->category->name ?? __('N/A') }}</p>
                                        </div>
                                        <span
                                            class="badge badge-secondary badge-pill font-weight-bold">#{{ $job->id }}</span>
                                    </div>

                                    <div class="mb-4">
                                        <div class="font-weight-bold text-dark mb-2">{{ __('created_at') }}</div>
                                        <p class="text-muted mb-0">{{ $job->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center border-0 pt-0">
                                    <div>
                                        @can('update jobs')
                                            <a data-toggle="modal" data-target="#editModal{{ $job->id }}"
                                                class="btn btn-light-info btn-sm font-weight-bold mr-2">
                                                <i class="fas fa-pen"></i> {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete jobs')
                                            <form action="{{ route('jobs.destroy', $job) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-light-danger btn-sm font-weight-bold delete-confirm"
                                                    onclick="return confirm('{{ __('delete_message') }}')">
                                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <x-modal id="editModal{{ $job->id }}" :title="__('update jobs')" module="administrative">
                                <form action="{{ route('jobs.update', $job->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-10">
                                        <div class="form-group">
                                            <label class="font-weight-bold">{{ __('name') }}</label>
                                            <input class="form-control form-control-solid" type="text"
                                                name="name" value="{{ $job->name }}" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">{{ __('category') }}</label>
                                            <select class="form-control selectpicker w-100"
                                                data-live-search="true" name="category_id" required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $job->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <x-slot:footer>
                                        <button type="button" class="btn btn-light-primary font-weight-bold"
                                            data-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit"
                                            class="btn btn-save-module font-weight-bold px-10">{{ __('Save') }}</button>
                                    </x-slot:footer>
                                </form>
                            </x-modal>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="d-flex flex-column align-items-center py-20">
                                <div class="symbol symbol-100 symbol-light-primary mb-5">
                                    <span class="symbol-label">
                                        <i class="fas fa-box-open icon-4x text-primary"></i>
                                    </span>
                                </div>
                                <p class="text-muted font-size-h5 font-weight-bold">{{ __('no_records') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    {!! $jobs->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>

    <x-modal id="createModal" :title="__('Create job')" module="administrative">
        <form action="{{ route('jobs.store') }}" method="post">
            @csrf
            <div class="modal-body p-10">
                <div class="form-group">
                    <label class="font-weight-bold">{{ __('name') }}</label>
                    <input class="form-control form-control-solid" type="text" name="name" required />
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">{{ __('category') }}</label>
                    <select class="form-control selectpicker w-100" data-live-search="true"
                        title="{{ __('Select') }}..." name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit"
                    class="btn btn-save-module font-weight-bold px-10">{{ __('Save') }}</button>
            </x-slot:footer>
        </form>
    </x-modal>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
