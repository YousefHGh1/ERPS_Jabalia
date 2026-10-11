@extends('layout.master')

@section('title')
    {{ __('holidays') }}
@endsection

@section('page_title')
    {{ __('holidays') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('holidays') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('holidays') }}"
                subtitle="{{ __('Manage available holiday types and their durations') }}"
            >
                <x-slot name="actions">
                    @can('store holidays')
                                            <a class="btn btn-success font-weight-bolder" data-toggle="modal" data-target="#createModal">
                                                <i class="fas fa-plus"></i> {{ __('Create') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom shadow-sm border-0">

            <div class="card-body pt-7">
                <div class="table-responsive">
                    @can('index holidays')
                        <table class="table table-head-custom table-vertical-center table-hover border-0">
                            <thead>
                                <tr class="text-uppercase text-muted">
                                    <th class="pl-0" style="width: 80px">{{ __('id') }}</th>
                                    <th style="min-width: 250px">{{ __('name') }}</th>
                                    <th style="min-width: 150px">{{ __('num_of_days') }}</th>
                                    <th style="min-width: 150px">{{ __('created_at') }}</th>
                                    <th class="text-right" style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $holiday)
                                    <tr>
                                        <td class="pl-0"><span class="text-muted font-weight-bold">{{ $holiday->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-primary mr-3">
                                                    <span class="symbol-label font-size-h5 font-weight-bold">
                                                        <i class="fas fa-calendar text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="label label-lg label-light-info label-inline font-weight-bold">
                                                {{ $holiday->num_of_days }} {{ __('days') }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted font-weight-bold">{{ $holiday->created_at->format('Y-m-d') }}</span></td>
                                        <td class="text-right pr-0">
                                            @can('update holidays')
                                                <a class="btn btn-icon btn-light-info btn-hover-info btn-sm mx-1" data-toggle="modal"
                                                    data-target="#editModal{{ $holiday->id }}" title="{{ __('Edit') }}">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('delete holidays')
                                                <form class="d-inline-block" action="{{ route('holidays.destroy', $holiday) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-icon btn-light-danger btn-hover-danger btn-sm mx-1" type="submit"
                                                        onclick="return confirm('{{ __('delete_message') }}')" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $holiday->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-info py-4">
                                                    <h5 class="modal-title text-white font-weight-bolder">{{ __('update holiday') }} - {{ $holiday->name }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <i aria-hidden="true" class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ route('holidays.update', $holiday->id) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-10">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('name') }}</label>
                                                            <input class="form-control form-control-solid @error('name') is-invalid @enderror" 
                                                                type="text" name="name" value="{{ $holiday->name }}" required />
                                                            @error('name')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('num_of_days') }}</label>
                                                            <input class="form-control form-control-solid @error('num_of_days') is-invalid @enderror" 
                                                                type="number" name="num_of_days" value="{{ $holiday->num_of_days }}" required />
                                                            @error('num_of_days')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
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
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-10">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="symbol symbol-100 symbol-light-primary mb-5">
                                                    <span class="symbol-label">
                                                        <i class="fas fa-calendar icon-4x text-primary"></i>
                                                    </span>
                                                </div>
                                                <p class="text-muted font-size-h5 font-weight-bold">{{ __('no_records') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success py-4">
                    <h5 class="modal-title text-white font-weight-bolder">{{ __('Create holiday') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('holidays.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-10">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('name') }}</label>
                            <input class="form-control form-control-solid @error('name') is-invalid @enderror" 
                                type="text" name="name" value="{{ old('name') }}" required />
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('num_of_days') }}</label>
                            <input class="form-control form-control-solid @error('num_of_days') is-invalid @enderror" 
                                type="number" name="num_of_days" value="{{ old('num_of_days') }}" required />
                            @error('num_of_days')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold px-10">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
