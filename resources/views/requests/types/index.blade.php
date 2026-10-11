@extends('layout.master')

@section('title')
    {{ __('أنواع الطلبات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('أنواع الطلبات') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('أنواع الطلبات') }}"
                subtitle="{{ __('إدارة أنواع الطلبات والمسارات') }}"
            >
                <x-slot name="actions">
                    <button class="btn btn-light-primary font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </button>
                                        @can('store request_types')
                                            <a class="btn btn-success font-weight-bolder" data-toggle="modal" data-target="#createModal">
                                                <i class="fas fa-plus"></i> {{ __('إضافة نوع طلب') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <div id="collapseSearch" class="collapse mb-7 {{ request()->filled('name') ? 'show' : '' }}">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ URL::current() }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">{{ __('اسم نوع الطلب') }}</label>
                                    <div class="input-icon">
                                        <input name="name" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('اسم نوع الطلب') }}" value="{{ request('name') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('بحث') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-hover border-0">
                        <thead>
                            <tr class="text-uppercase text-muted">
                                <th class="pl-0" style="width: 80px">#</th>
                                <th style="min-width: 250px">{{ __('اسم نوع الطلب') }}</th>
                                <!-- <th style="min-width: 300px">{{ __('الوصف') }}</th> -->
                                <th style="min-width: 100px">{{ __('المراحل') }}</th>
                                <th style="min-width: 150px">{{ __('تاريخ الإنشاء') }}</th>
                                <th class="text-right" style="min-width: 200px">{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requestTypes as $type)
                                <tr>
                                    <td class="pl-0">
                                        <span class="text-muted font-weight-bold">{{ $type->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40 symbol-light-primary mr-3">
                                                <span class="symbol-label font-size-h5 font-weight-bold">
                                                    {{ mb_substr($type->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                    {{ $type->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                            {{ $type->description ?? '-' }}
                                        </span>
                                    </td> -->
                                    <td>
                                        <span class="badge badge-light-primary font-weight-bold">{{ $type->workflows_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted font-weight-bold">
                                            {{ $type->created_at->format('Y-m-d') }}
                                        </span>
                                    </td>
                                    <td class="text-right pr-0">
                                        <a href="{{ route('request-workflows.index', $type->id) }}" 
                                           class="btn btn-icon btn-light-primary btn-hover-primary btn-sm mx-1"
                                           title="{{ __('عرض المسارات') }}">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        @can('update request_types')
                                            <a data-toggle="modal" data-target="#editModal{{ $type->id }}"
                                                class="btn btn-icon btn-light-info btn-hover-info btn-sm mx-1"
                                                title="{{ __('تعديل') }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('delete request_types')
                                            <form action="{{ route('request-types.destroy', $type->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-icon btn-light-danger btn-hover-danger btn-sm mx-1 delete-confirm"
                                                    title="{{ __('حذف') }}"
                                                    onclick="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-10">
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
                </div>

                @if($requestTypes->hasPages())
                    <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                        {!! $requestTypes->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success py-4">
                    <h5 class="modal-title text-white font-weight-bolder">{{ __('إضافة نوع طلب') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('request-types.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-10">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('اسم نوع الطلب') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-solid" required placeholder="{{ __('أدخل الاسم') }}" name="name">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('الوصف') }}</label>
                            <textarea class="form-control form-control-solid" rows="3" placeholder="{{ __('أدخل الوصف') }}" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-warning font-weight-bold" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold px-10">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach($requestTypes as $type)
        <div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-info py-4">
                        <h5 class="modal-title text-white font-weight-bolder">{{ __('تعديل نوع طلب') }} - {{ $type->name }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('request-types.update', $type->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-10">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('اسم نوع الطلب') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-solid" required value="{{ $type->name }}" name="name">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('الوصف') }}</label>
                                <textarea class="form-control form-control-solid" rows="3" name="description">{{ $type->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-warning font-weight-bold" data-dismiss="modal">{{ __('إلغاء') }}</button>
                            <button type="submit" class="btn btn-info font-weight-bold px-10">{{ __('تحديث') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
