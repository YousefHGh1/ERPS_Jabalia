@extends('layout.master')

@section('title')
    {{ __('vehicles') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('vehicles') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — بنفس هوية لوحة التحكم --}}
        <x-page-header
            :title="__('vehicles')"
            :subtitle="__('Manage municipality vehicles and machinery')"
            icon="fa-truck"
        >
            <x-slot name="actions">
                <a href="#" class="btn btn-light-success font-weight-bolder" data-toggle="collapse"
                    data-target="#collapseSearch">
                    <i class="fas fa-search"></i> {{ __('Search') }}
                </a>
                @can('store vehicle')
                    <a class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> {{ __('Create') }}
                    </a>
                @endcan
            </x-slot>
        </x-page-header>

        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body">
                <div id="collapseSearch" class="collapse mb-7">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ URL::current() }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">{{ __('name') }}</label>
                                    <input name="name" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('name') }}" value="{{ request('name') }}" />
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">{{ __('number') }}</label>
                                    <input name="number" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('number') }}" value="{{ request('number') }}" />
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" data-responsive="true">
                    @can('index vehicle')
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-uppercase text-muted">
                                    <th style="min-width: 50px">{{ __('id') }}</th>
                                    <th style="min-width: 150px">{{ __('number') }}</th>
                                    <th style="min-width: 200px">{{ __('name') }}</th>
                                    <th style="min-width: 100px">{{ __('color') }}</th>
                                    <th style="min-width: 150px">{{ __('images') }}</th>
                                    <th class="text-right" style="min-width: 120px">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td data-label="{{ __('id') }}"><span class="text-muted font-weight-bold">{{ $vehicle->id }}</span></td>
                                        <td data-label="{{ __('number') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-success mr-4">
                                                    <span class="symbol-label">
                                                        <i class="fas fa-truck text-success"></i>
                                                    </span>
                                                </div>
                                                <span
                                                    class="text-dark-75 font-weight-bolder font-size-lg">{{ $vehicle->number }}</span>
                                            </div>
                                        </td>
                                        <td data-label="{{ __('name') }}"><span
                                                class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $vehicle->name }}</span>
                                        </td>
                                        <td data-label="{{ __('color') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="label label-dot mr-2"
                                                    style="background-color: {{ $vehicle->color }}"></span>
                                                <span class="text-muted font-weight-bold">{{ $vehicle->color }}</span>
                                            </div>
                                        </td>
                                        <td data-label="{{ __('images') }}">
                                            <div class="d-flex flex-wrap">
                                                @foreach ($vehicle->images ?? [] as $image)
                                                    <div class="mr-2 mb-2" style="position: relative;">
                                                        <a href="{{ route('storage.image', ['path' => $image]) }}" target="_blank"
                                                           data-toggle="tooltip" title="{{ __('View Image') }}">
                                                            <img src="{{ route('storage.image', ['path' => $image]) }}"
                                                                 alt="vehicle"
                                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #ebedf3; cursor: pointer;"
                                                                 onmouseover="this.style.transform='scale(1.1)'; this.style.transition='transform 0.2s';"
                                                                 onmouseout="this.style.transform='scale(1)';" />
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-right pr-0 table-actions" data-label="{{ __('action') }}">
                                            @can('update vehicle')
                                                <a data-toggle="modal" data-target="#editModal{{ $vehicle->id }}"
                                                    class="btn btn-icon btn-light-info btn-hover-info btn-sm mx-1"
                                                    title="{{ __('Edit') }}">
                                                    <i class="fas fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete vehicle')
                                                <form action="{{ route('vehicle.destroy', $vehicle) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-icon btn-light-danger btn-hover-danger btn-sm mx-1 delete-confirm"
                                                        title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $vehicle->id }}" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-info py-4">
                                                    <h5 class="modal-title text-white font-weight-bolder">
                                                        {{ __('update vehicle') }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <i aria-hidden="true" class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ route('vehicle.update', $vehicle->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-10">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('number') }}</label>
                                                            <input
                                                                class="form-control form-control-solid @error('number') is-invalid @enderror"
                                                                type="text" name="number"
                                                                value="{{ old('number', $vehicle->number) }}" required />
                                                            @error('number')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('name') }}</label>
                                                            <input
                                                                class="form-control form-control-solid @error('name') is-invalid @enderror"
                                                                type="text" name="name"
                                                                value="{{ old('name', $vehicle->name) }}" required />
                                                            @error('name')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('color') }}</label>
                                                            <input
                                                                class="form-control form-control-solid @error('color') is-invalid @enderror"
                                                                type="text" name="color"
                                                                value="{{ old('color', $vehicle->color) }}" required />
                                                            @error('color')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ __('images') }}</label>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    name="images[]" multiple
                                                                    id="edit_images_{{ $vehicle->id }}">
                                                                <label class="custom-file-label"
                                                                    for="edit_images_{{ $vehicle->id }}">{{ __('Choose files') }}</label>
                                                            </div>
                                                            <div class="mt-3 d-flex flex-wrap">
                                                                @foreach ($vehicle->images ?? [] as $image)
                                                                    <div class="mr-2 mb-2" style="position: relative;">
                                                                        <a href="{{ route('storage.image', ['path' => $image]) }}" target="_blank">
                                                                            <img src="{{ route('storage.image', ['path' => $image]) }}"
                                                                                 alt="vehicle"
                                                                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #ebedf3; cursor: pointer;"
                                                                                 onmouseover="this.style.transform='scale(1.1)'; this.style.transition='transform 0.2s';"
                                                                                 onmouseout="this.style.transform='scale(1)';" />
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
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
                                        <td colspan="6" class="text-center p-10">
                                            <x-empty-state
                                                icon="fa-box-open"
                                                :title="__('no_records')"
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                            {!! $vehicles->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    @can('store vehicle')
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary py-4">
                        <h5 class="modal-title text-white font-weight-bolder">{{ __('Create vehicle') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('vehicle.store') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="modal-body p-10">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('number') }}</label>
                                <input class="form-control form-control-solid @error('number') is-invalid @enderror"
                                    type="text" name="number" value="{{ old('number') }}" required />
                                @error('number')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('name') }}</label>
                                <input class="form-control form-control-solid @error('name') is-invalid @enderror"
                                    type="text" name="name" value="{{ old('name') }}" required />
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('color') }}</label>
                                <input class="form-control form-control-solid @error('color') is-invalid @enderror"
                                    type="color" name="color" value="{{ old('color') }}" required />
                                @error('color')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('images') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="images[]" multiple
                                        id="create_images">
                                    <label class="custom-file-label" for="create_images">{{ __('Choose files') }}</label>
                                </div>
                                @error('images')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit"
                                class="btn btn-primary font-weight-bold px-10">{{ __('Save') }}</button>
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
