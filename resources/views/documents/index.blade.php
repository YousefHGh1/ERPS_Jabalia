@extends('layout.master')

@section('title')
    {{ __('المستندات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('المستندات') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('المستندات') }}"
                subtitle="{{ __('إدارة وتصفح سجلات المستندات') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </a>
                                        @can('store documents')
                                            <a href="{{ route('documents.create') }}" class="btn btn-primary font-weight-bolder">
                                                <i class="fas fa-plus"></i> {{ __('إضافة جديد') }}
                                            </a>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('document_type') || request()->filled('status') || request()->filled('from_date') || request()->filled('to_date') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('البحث') }}</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="{{ __('رقم المستند، العنوان، الوصف') }}" value="{{ request('search') }}" />
                                        <span><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('نوع المستند') }}</label>
                                    <select name="document_type" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="contract" {{ request('document_type') == 'contract' ? 'selected' : '' }}>{{ __('عقد') }}</option>
                                        <option value="agreement" {{ request('document_type') == 'agreement' ? 'selected' : '' }}>{{ __('اتفاقية') }}</option>
                                        <option value="report" {{ request('document_type') == 'report' ? 'selected' : '' }}>{{ __('تقرير') }}</option>
                                        <option value="letter" {{ request('document_type') == 'letter' ? 'selected' : '' }}>{{ __('خطاب') }}</option>
                                        <option value="other" {{ request('document_type') == 'other' ? 'selected' : '' }}>{{ __('أخرى') }}</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }}</label>
                                    <select name="status" class="form-control form-control-solid">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('مسودة') }}</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('نشط') }}</option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>{{ __('مؤرشف') }}</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('من تاريخ') }}</label>
                                    <input name="from_date" type="date" class="form-control form-control-solid"
                                        value="{{ request('from_date') }}" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">{{ __('إلى تاريخ') }}</label>
                                    <input name="to_date" type="date" class="form-control form-control-solid"
                                        value="{{ request('to_date') }}" />
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark opacity-0">{{ __('إجراءات') }}</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                                            <i class="fas fa-search"></i> {{ __('بحث') }}
                                        </button>
                                        <a href="{{ route('documents.index') }}" class="btn btn-light-danger font-weight-bolder">
                                            <i class="fas fa-sync-alt"></i> {{ __('إعادة تعيين') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Success/Error messages are displayed via SweetAlert2 in layout/error.blade.php --}}

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr>
                                <th>{{ __('رقم المستند') }}</th>
                                <th>{{ __('العنوان') }}</th>
                                <th>{{ __('نوع المستند') }}</th>
                                <th>{{ __('تاريخ المستند') }}</th>
                                <th>{{ __('حجم الملف') }}</th>
                                <th>{{ __('الحالة') }}</th>
                                <th>{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <span class="badge badge-primary">{{ $document->document_number }}</span>
                                    </td>
                                    <td>{{ $document->title }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $document->document_type }}</span>
                                    </td>
                                    <td>{{ $document->document_date->format('d-m-Y') }}</td>
                                    <td>{{ $document->file_size_formatted }}</td>
                                    <td>{!! $document->status_badge !!}</td>
                                    <td>
                                        <div class="dropdown dropdown-inline">
                                            <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <a href="{{ route('documents.download', $document) }}" class="dropdown-item" target="_blank">
                                                    <i class="fas fa-download text-success"></i> {{ __('تحميل') }}
                                                </a>
                                                @can('update documents')
                                                    <a href="{{ route('documents.edit', $document) }}" class="dropdown-item">
                                                        <i class="fas fa-pen text-primary"></i> {{ __('تعديل') }}
                                                    </a>
                                                @endcan
                                                @can('delete documents')
                                                    <a href="javascript:;" class="dropdown-item delete-document" data-id="{{ $document->id }}">
                                                        <i class="fas fa-trash text-danger"></i> {{ __('حذف') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('لا توجد بيانات') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">{{ __('عرض') }} {{ $documents->firstItem() }} {{ __('إلى') }} {{ $documents->lastItem() }} {{ __('من') }} {{ $documents->total() }} {{ __('سجل') }}</span>
                    </div>
                    <div class="d-flex align-items-center py-3">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.delete-document').click(function() {
            var id = $(this).data('id');
            if(confirm('{{ __('هل أنت متأكد من حذف هذا المستند؟') }}')) {
                $.ajax({
                    url: '{{ route("documents.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('{{ __('حدث خطأ أثناء الحذف') }}');
                    }
                });
            }
        });
    });
</script>
@endpush
