@extends('layout.master')

@section('title')
    {{ $pageTitle }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ $pageTitle }}"
                subtitle="{{ $pageSubtitle }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        <a href="{{ route('mokalafs.index') }}" class="btn btn-light-primary font-weight-bolder">
                                            <i class="fas fa-user"></i> {{ __('كل المكلفين') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('identity_number') || request()->filled('address') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form method="GET" action="{{ url()->current() }}" class="form" id="filter-form">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الاسم') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('الاسم') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('رقم الهوية') }}</label>
                                    <div class="input-icon">
                                        <input name="identity_number" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('رقم الهوية') }}" value="{{ request('identity_number') }}" />
                                        <span><i class="fas fa-id-card text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('العنوان') }}</label>
                                    <div class="input-icon">
                                        <input name="address" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('العنوان') }}" value="{{ request('address') }}" />
                                        <span><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark opacity-0">{{ __('إجراءات') }}</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </button>
                                        <a href="{{ url()->current() }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-sync-alt"></i> {{ __('إعادة تعيين') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" data-responsive="true">
                    <table class="table table-head-custom table-vertical-center" id="mokalaf_table">
                        <thead>
                            <tr>
                                <th>{{ __('رقم المكلف') }}</th>
                                <th>{{ __('اسم المكلف') }}</th>
                                <th>{{ __('هوية | معرف | مشتغل مرخص') }}</th>
                                <th>{{ __('العنوان') }}</th>
                                <th>{{ __('رقم الجوال') }}</th>
                                <th>{{ __('تاريخ الإضافة') }}</th>
                                <th>{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mokalafs as $mokalaf)
                                <tr>
                                    <td data-label="{{ __('رقم المكلف') }}">{{ $mokalaf->id }}</td>
                                    <td data-label="{{ __('اسم المكلف') }}">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40 symbol-light-success mr-3">
                                                <span class="symbol-label font-weight-bolder">{{ substr($mokalaf->name ?? 'N/A', 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <a href="{{ route('mokalafs.show', $mokalaf) }}" class="text-dark-75 font-weight-bolder text-hover-primary">
                                                    {{ $mokalaf->name ?? 'N/A' }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('هوية | معرف | مشتغل مرخص') }}">
                                        <span class="badge badge-{{ $mokalaf->identity_number ? 'info' : 'warning' }}">
                                            {{ $mokalaf->identifier }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $mokalaf->identifier_type }}</small>
                                    </td>
                                    <td data-label="{{ __('العنوان') }}">{{ $mokalaf->address ?? '-' }}</td>
                                    <td data-label="{{ __('رقم الجوال') }}">{{ $mokalaf->mobile ?? '-' }}</td>
                                    <td data-label="{{ __('تاريخ الإضافة') }}">{{ $mokalaf->created_at->format('d-m-Y') }}</td>
                                    <td data-label="{{ __('إجراءات') }}" class="table-actions">
                                        <a href="{{ route('mokalafs.show', $mokalaf) }}" class="btn btn-sm btn-clean btn-icon" title="{{ __('عرض') }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open icon-4x text-muted mb-4"></i>
                                            <p class="text-muted font-weight-bold font-size-h5">{{ $emptyMessage }}</p>
                                            <a href="{{ route('mokalafs.index') }}" class="btn btn-light-primary font-weight-bolder mt-3">
                                                <i class="fas fa-user"></i> {{ __('تصفح كل المكلفين') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap table-pagination-wrapper">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">{{ __('عرض') }} {{ $mokalafs->firstItem() ?? 0 }} {{ __('إلى') }} {{ $mokalafs->lastItem() ?? 0 }} {{ __('من') }} {{ $mokalafs->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {!! $mokalafs->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
