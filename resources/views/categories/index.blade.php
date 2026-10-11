@extends('layout.master')

@section('title')
    {{ __('Categories') }}
@endsection

@section('page_title')
    {{ __('Categories') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Categories') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Categories') }}"
                subtitle="{{ __('Manage job categories and salary degrees') }}"
            >
                <x-slot name="actions">
                    <button class="btn btn-light-primary font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </button>
                                        @can('store categories')
                                            <a class="btn btn-success font-weight-bolder" data-toggle="modal" data-target="#createModal">
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
                <div id="collapseSearch" class="collapse mb-7 {{ request()->filled('name') ? 'show' : '' }}">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ URL::current() }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">{{ __('name') }}</label>
                                    <div class="input-icon">
                                        <input name="name" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    @forelse($categories as $category)
                        <div class="col-lg-6 col-xl-4">
                            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-5">
                                        <div>
                                            <h4 class="card-title font-weight-bolder mb-1">{{ $category->name }}</h4>
                                            <p class="text-muted mb-2">{{ $category->work_nature ?? '-' }}</p>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="label label-inline label-light-primary font-weight-bold py-2 px-3">
                                                    {{ __('start_degree') }}: {{ $category->start_degree }}
                                                </span>
                                                <span class="label label-inline label-light-info font-weight-bold py-2 px-3">
                                                    {{ __('end_degree') }}: {{ $category->end_degree }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="badge badge-secondary badge-pill font-weight-bold">#{{ $category->id }}</span>
                                    </div>

                                    <div class="mb-5">
                                        <div class="font-weight-bold text-dark mb-2">{{ __('included_degrees') }}</div>
                                        <p class="text-muted mb-0">{{ $category->included_degrees ?: '-' }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <div class="font-weight-bold text-dark mb-2">الدرجات والراتب الأساسي</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @forelse($category->degrees as $degree)
                                                <span class="label label-inline label-light-success font-weight-bold py-2 px-3">
                                                    {{ $degree->degree_name }}
                                                    @if($degree->basic_salary !== null)
                                                        - {{ number_format($degree->basic_salary, 0) }}
                                                    @endif
                                                </span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center border-0 pt-0">
                                    <div>
                                        @can('update categories')
                                            <a data-toggle="modal" data-target="#editModal{{ $category->id }}"
                                                class="btn btn-light-info btn-sm font-weight-bold mr-2">
                                                <i class="fas fa-pen"></i> {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete categories')
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
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

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-info py-4">
                                            <h5 class="modal-title text-white font-weight-bolder">
                                                {{ __('Edit') }} - {{ $category->name }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal"
                                                aria-label="Close">
                                                <i aria-hidden="true" class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <form action="{{ route('categories.update', $category->id) }}"
                                            method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-10">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __('name') }}</label>
                                                    <input class="form-control form-control-solid" type="text"
                                                        name="name" value="{{ $category->name }}" required />
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label class="font-weight-bold">{{ __('start_degree') }}</label>
                                                        <input class="form-control form-control-solid" type="text"
                                                            name="start_degree" value="{{ $category->start_degree }}"
                                                            required />
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label class="font-weight-bold">{{ __('end_degree') }}</label>
                                                        <input class="form-control form-control-solid" type="text"
                                                            name="end_degree" value="{{ $category->end_degree }}"
                                                            required />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __('work_nature') }}</label>
                                                    <input class="form-control form-control-solid" type="text"
                                                        name="work_nature" value="{{ $category->work_nature }}" />
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __('included_degrees') }}</label>
                                                    <input class="form-control form-control-solid" type="text"
                                                        name="included_degrees"
                                                        value="{{ $category->included_degrees }}" />
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light-primary font-weight-bold"
                                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-info font-weight-bold px-10">{{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
                    {!! $categories->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success py-4">
                    <h5 class="modal-title text-white font-weight-bolder">{{ __('Create Category') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('categories.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-10">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('name') }}</label>
                            <input class="form-control form-control-solid" type="text" name="name" required />
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('start_degree') }}</label>
                                <input class="form-control form-control-solid" type="text" name="start_degree"
                                    required />
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('end_degree') }}</label>
                                <input class="form-control form-control-solid" type="text" name="end_degree"
                                    required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('work_nature') }}</label>
                            <input class="form-control form-control-solid" type="text" name="work_nature" />
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('included_degrees') }}</label>
                            <input class="form-control form-control-solid" type="text" name="included_degrees"
                                placeholder="A1,A2,A3,A4,A" />
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light-primary font-weight-bold"
                            data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit"
                            class="btn btn-success font-weight-bold px-10">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
