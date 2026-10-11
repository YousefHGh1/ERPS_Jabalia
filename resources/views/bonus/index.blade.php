@extends('layout.master')

@section('title')
    {{ __('bonus') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('bonus') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('bonus') }}"
                subtitle="{{ __('Manage employee bonuses') }}"
            >
                <x-slot name="actions">
                    <button class="btn btn-light-primary font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </button>
                                        @can('store bonus')
                                            <a class="btn btn-success font-weight-bolder" data-toggle="modal" data-target="#createModal">
                                                <i class="fas fa-plus"></i> {{ __('Create') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <!--begin::Body-->
            <div class="card-body pt-0 pb-3">
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
                <div class="table-responsive">
                    @can('index bonus')
                        <table class="table table-head-custom table-vertical-center table-borderless">
                            <thead>
                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                    <th class="pl-0" style="width: 40px">#</th>
                                    <th style="min-width: 250px">{{ __('name') }}</th>
                                    <th style="min-width: 100px">{{ __('amount') }}</th>
                                    <th class="text-right" style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bonuses as $bonus)
                                    <tr>
                                        <td class="pl-0">
                                            <span class="text-muted font-weight-bold">{{ $bonus->id }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $bonus->name }}</span>
                                        </td>
                                        <td>
                                            <span class="label label-lg label-light-success label-inline font-weight-bold">
                                                {{ $bonus->amount }}
                                            </span>
                                        </td>
                                        <td class="text-right pr-0">
                                            @can('update bonus')
                                                <a data-toggle="modal" data-target="#editModal{{ $bonus->id }}"
                                                    class="btn btn-icon btn-light-info btn-hover-info btn-sm mx-1"
                                                    title="{{ __('Edit') }}">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('delete bonus')
                                                <form action="{{ route('bonus.destroy', $bonus) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-icon btn-light-danger btn-hover-danger btn-sm mx-1 delete-confirm"
                                                        title="{{ __('Delete') }}"
                                                        onclick="return confirm('{{ __('delete_message') }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $bonus->id }}" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-info py-4">
                                                    <h5 class="modal-title text-white font-weight-bolder">
                                                        {{ __('Edit') }} - {{ $bonus->name }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <i aria-hidden="true" class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ route('bonus.update', $bonus->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-10 text-left">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('name') }}</label>
                                                            <input class="form-control form-control-solid" type="text"
                                                                name="name" value="{{ $bonus->name }}" required />
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('amount') }}</label>
                                                            <input class="form-control form-control-solid" type="number"
                                                                name="amount" value="{{ $bonus->amount }}" required />
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
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-10">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="symbol symbol-100 symbol-light-primary mb-5">
                                                    <span class="symbol-label">
                                                        <i class="fas fa-box-open icon-4x text-primary"></i>
                                                    </span>
                                                </div>
                                                <p class="text-muted font-size-h5 font-weight-bold">{{ __('no_records') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-5">
                            {!! $bonuses->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary py-4">
                    <h5 class="modal-title text-white font-weight-bolder" id="createModalLabel">
                        {{ __('Create Bonus') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('bonus.store') }}" method="POST" class="form">
                    @csrf
                    <div class="modal-body p-10">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('name') }}</label>
                            <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" value="{{ old('name') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('amount') }}</label>
                            <input type="number" name="amount" class="form-control form-control-solid @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required />
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
@endsection

@section('scripts')
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
    <!--end::Page Scripts-->
@endsection
