@extends('layout.master')

@section('title')
    التحصيل الخارجي
@endsection

@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
    لوحة التحكم
@endsection

@section('sub_title')
    التحصيل الخارجي
@endsection

@section('content')
    @php
        $sections = App\Models\Section::with('department')->get();
    @endphp
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="التحصيل الخارجي"
                subtitle="إدارة وتصفح سجلات التحصيل الخارجي"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> بحث
                                        </a>
                                        @can('create external_collections')
                                            <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                                                data-target="#addExternalCollectionModal">
                                                <i class="fas fa-plus"></i> إضافة تحصيل جديد
                                            </button>
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                <div id="collapseSearch"
                    class="collapse mb-7 {{ request()->filled('search') || request()->filled('taxpayer_name') || request()->filled('section_id') ? 'show' : '' }}">
                    <div class="card card-body bg-light border-0 shadow-sm p-8" style="border-radius: 12px;">
                        <form id="filter-form" class="form" method="GET"
                            action="{{ route('external-collections.index') }}">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">اسم المكلف</label>
                                    <div class="input-icon">
                                        <input name="search" type="text" class="form-control form-control-solid"
                                            placeholder="ابحث عن طريق اسم المكلف" value="{{ request('search') }}">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">القسم</label>
                                    <div class="input-icon">
                                        <select name="section_id" class="form-control form-control-solid">
                                            <option value="">جميع الأقسام</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="input-icon-addon">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">رقم الهوية</label>
                                    <div class="input-icon">
                                        <input name="identity_number" type="text" class="form-control form-control-solid"
                                            placeholder="رقم الهوية" value="{{ request('identity_number') }}">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary font-weight-bolder mr-2 flex-grow-1">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                        <a href="{{ route('external-collections.index') }}"
                                            class="btn btn-light-secondary font-weight-bolder flex-grow-1">
                                            <i class="fas fa-times"></i> مسح
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" data-responsive="true">
                    <table class="table table-head-custom table-vertical-center table-hover"
                        id="external_collections_table">
                        <thead class="bg-light">
                            <tr>
                                <th class="font-weight-bolder text-dark">#</th>
                                <th class="font-weight-bolder text-dark">اسم المكلف</th>
                                <th class="font-weight-bolder text-dark">رقم الهوية</th>
                                <th class="font-weight-bolder text-dark">القسم</th>
                                <th class="font-weight-bolder text-dark">المبلغ</th>
                                <th class="font-weight-bolder text-dark">حالة الدفع</th>
                                <th class="font-weight-bolder text-dark">تاريخ الإنشاء</th>
                                <th class="font-weight-bolder text-dark text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $collection)
                                <tr class="border-bottom">
                                    <td class="font-weight-bold">{{ $collection->id }}</td>
                                    <td>
                                        @if ($collection->mokalaf)
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-success mr-3">
                                                    <span
                                                        class="symbol-label font-weight-bolder">{{ substr($collection->mokalaf->name ?? 'N/A', 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('mokalafs.show', $collection->mokalaf->id) }}"
                                                        class="text-dark-75 font-weight-bolder text-hover-primary">
                                                        {{ $collection->mokalaf->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ $collection->taxpayer_name ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info badge-pill px-3 py-2">
                                            <i class="fas fa-id-card ml-1"></i> {{ $collection->identity_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary badge-pill px-3 py-2">
                                            <i class="fas fa-building ml-1"></i> {{ $collection->section->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="font-weight-bolder text-dark">{{ number_format($collection->amount, 2) }}</span>
                                        <small class="text-muted d-block">شيكل</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{!! $collection->payment_status_badge !!}</span>
                                            @if ($collection->paid_amount > 0)
                                                <small class="text-muted mt-1">
                                                    <span class="text-success">مدفوع:
                                                        {{ number_format($collection->paid_amount, 2) }}</span>
                                                    @if ($collection->remaining_amount > 0)
                                                        <span class="text-danger mr-1">| متبقي:
                                                            {{ number_format($collection->remaining_amount, 2) }}</span>
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="font-weight-bold">{{ $collection->created_at->format('d-m-Y') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown dropdown-inline">
                                            <a href="#" class="btn btn-sm btn-light-primary font-weight-bolder px-4"
                                                data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog ml-1"></i> إجراءات
                                                <i class="fas fa-chevron-down mr-1"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right p-0 m-0 shadow-sm"
                                                style="border-radius: 8px; min-width: 180px;">
                                                <div
                                                    class="dropdown-header font-weight-bolder text-dark border-bottom py-3">
                                                    الإجراءات المتاحة</div>
                                                <a href="{{ route('external-collections.show', $collection) }}"
                                                    class="dropdown-item py-3">
                                                    <i class="fas fa-eye text-primary ml-2"></i> عرض التفاصيل
                                                </a>
                                                @can('edit external_collections')
                                                    <a href="#" class="dropdown-item py-3 open-edit-modal"
                                                        data-toggle="modal" data-target="#editExternalCollectionModal"
                                                        data-id="{{ $collection->id }}"
                                                        data-identity-number="{{ $collection->identity_number }}"
                                                        data-taxpayer-name="{{ $collection->taxpayer_name }}"
                                                        data-section-id="{{ $collection->section_id }}"
                                                        data-amount="{{ $collection->amount }}"
                                                        data-notes="{{ $collection->notes }}">
                                                        <i class="fas fa-edit text-warning ml-2"></i> تعديل
                                                    </a>
                                                @endcan
                                                @can('payment external_collections')
                                                    <a href="#" class="dropdown-item py-3 open-payment-modal"
                                                        data-toggle="modal" data-target="#paymentExternalCollectionModal"
                                                        data-id="{{ $collection->id }}"
                                                        data-taxpayer-name="{{ $collection->taxpayer_full_name }}"
                                                        data-amount="{{ $collection->amount }}"
                                                        data-remaining-amount="{{ $collection->remaining_amount }}">
                                                        <i class="fas fa-money-bill-wave text-success ml-2"></i> دفع
                                                    </a>
                                                @endcan
                                                @can('delete external_collections')
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('external-collections.destroy', $collection) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التحصيل؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-3">
                                                            <i class="fas fa-trash text-danger ml-2"></i> حذف
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8">
                                        <div class="alert alert-info border-0 d-inline-block px-8 py-5"
                                            style="border-radius: 12px;">
                                            <i class="fas fa-info-circle fa-2x d-block mb-2"></i>
                                            <span class="font-weight-bold">لا توجد سجلات تحصيل خارجي حالياً</span>
                                        </div>
                                    </td>
                                </tr>
                            @endempty
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($collections->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4 px-4">
                    <div class="text-muted font-weight-bold">
                        عرض {{ $collections->firstItem() }} إلى {{ $collections->lastItem() }} من
                        {{ $collections->total() }} سجل
                    </div>
                    <div>
                        {{ $collections->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal إضافة تحصيل خارجي جديد -->
    <div class="modal fade" id="addExternalCollectionModal" tabindex="-1"
        aria-labelledby="addExternalCollectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExternalCollectionModalLabel">
                        <i class="fas fa-plus-circle text-success"></i> إضافة تحصيل خارجي جديد
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('external-collections.store') }}" method="POST" id="modalAddForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_identity_number" class="form-label">رقم الهوية <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_identity_number"
                                            name="identity_number" required>
                                        <button class="btn btn-outline-primary" type="button"
                                            id="modalSearchTaxpayerBtn">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="modal_identity_number_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_taxpayer_name" class="form-label">اسم المكلف</label>
                                    <input type="text" class="form-control" id="modal_taxpayer_name"
                                        name="taxpayer_name" readonly>
                                    <small class="text-muted">يتم ملؤه تلقائياً عند البحث عن المكلف</small>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات المكلف الإضافية -->
                        <div id="modalTaxpayerInfo" class="alert alert-info d-none">
                            <h6 class="alert-heading">معلومات المكلف:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الاسم:</strong> <span id="modalInfoName"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>رقم الهوية:</strong> <span id="modalInfoIdentity"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>رقم المشغل:</strong> <span id="modalInfoLicense"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>العنوان:</strong> <span id="modalInfoAddress"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>الجوال:</strong> <span id="modalInfoMobile"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_section_id" class="font-weight-bold text-dark">القسم <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon">
                                        <select class="form-control form-control-solid"
                                            id="modal_section_id" name="section_id" required>
                                            <option value="">اختر القسم...</option>
                                            @php
                                                $groupedSections = $sections->groupBy(function ($section) {
                                                    return $section->department
                                                        ? $section->department->name
                                                        : 'بدون قسم';
                                                });
                                            @endphp
                                            @foreach ($groupedSections as $departmentName => $deptSections)
                                                <optgroup label="{{ $departmentName }}">
                                                    @foreach ($deptSections as $section)
                                                        <option value="{{ $section->id }}">
                                                            {{ $section->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <span class="input-icon-addon">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback" id="modal_section_id_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_amount" class="form-label">المبلغ <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="modal_amount" name="amount"
                                            step="0.01" min="0.01" required>
                                        <span class="input-group-text">شيكل</span>
                                    </div>
                                    <div class="invalid-feedback" id="modal_amount_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="modal_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="modal_notes" name="notes" rows="3"
                                        placeholder="ملاحظات إضافية (اختياري)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-success" id="modalSubmitBtn">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal تعديل تحصيل خارجي -->
    <div class="modal fade" id="editExternalCollectionModal" tabindex="-1"
        aria-labelledby="editExternalCollectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExternalCollectionModalLabel">
                        <i class="fas fa-edit text-warning"></i> تعديل التحصيل الخارجي
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="editExternalCollectionForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_identity_number" class="font-weight-bold text-dark">رقم الهوية
                                        <span class="text-danger">*</span></label>
                                    <div class="input-icon">
                                        <input type="text" class="form-control form-control-solid"
                                            id="edit_identity_number" name="identity_number" required>
                                        <span class="input-icon-addon">
                                            <button class="btn btn-icon btn-light-primary" type="button"
                                                id="editSearchTaxpayerBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_taxpayer_name" class="font-weight-bold text-dark">اسم
                                        المكلف</label>
                                    <input type="text" class="form-control form-control-solid"
                                        id="edit_taxpayer_name" name="taxpayer_name" readonly>
                                    <small class="text-muted mt-1">يتم ملؤه تلقائياً عند البحث عن المكلف</small>
                                </div>
                            </div>
                        </div>

                        <div id="editTaxpayerInfo" class="card card-body bg-light border-0 shadow-sm p-8 d-none"
                            style="border-radius: 12px;">
                            <h6 class="font-weight-bolder text-dark mb-3">معلومات المكلف:</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong class="text-dark">الاسم:</strong> <span
                                        id="editInfoName"></span></div>
                                <div class="col-md-6 mb-2"><strong class="text-dark">رقم الهوية:</strong> <span
                                        id="editInfoIdentity"></span></div>
                                <div class="col-md-6 mb-2"><strong class="text-dark">رقم المشغل:</strong> <span
                                        id="editInfoLicense"></span></div>
                                <div class="col-md-6 mb-2"><strong class="text-dark">العنوان:</strong> <span
                                        id="editInfoAddress"></span></div>
                                <div class="col-md-6 mb-2"><strong class="text-dark">الجوال:</strong> <span
                                        id="editInfoMobile"></span></div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_section_id" class="font-weight-bold text-dark">القسم <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" id="edit_section_id"
                                        name="section_id" required>
                                        <option value="">اختر القسم...</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">
                                                {{ $section->name }}
                                                @if ($section->department)
                                                    - {{ $section->department->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_amount" class="font-weight-bold text-dark">المبلغ <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid"
                                            id="edit_amount" name="amount" step="0.01" min="0" required>
                                        <span class="input-group-text bg-light">شيكل</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="edit_notes" class="font-weight-bold text-dark">ملاحظات</label>
                                    <textarea class="form-control form-control-solid" id="edit_notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary font-weight-bolder"
                            data-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-save"></i> تحديث
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal دفع تحصيل خارجي -->
    <div class="modal fade" id="paymentExternalCollectionModal" tabindex="-1"
        aria-labelledby="paymentExternalCollectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentExternalCollectionModalLabel">
                        <i class="fas fa-money-bill-wave text-success"></i> دفع التحصيل الخارجي
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="paymentExternalCollectionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="card card-body bg-light border-0 shadow-sm p-8 mb-5" style="border-radius: 12px;">
                            <h6 class="font-weight-bolder text-dark mb-3"><i
                                    class="fas fa-file-invoice text-info mr-2"></i> معلومات التحصيل:</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <strong class="text-dark">المكلف:</strong> <span id="paymentTaxpayerName"
                                        class="font-weight-bold"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong class="text-dark">المبلغ الإجمالي:</strong> <span id="paymentTotalAmount"
                                        class="text-danger font-weight-bold"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong class="text-dark">المبلغ المتبقي:</strong> <span
                                        id="paymentRemainingAmount" class="text-success font-weight-bold"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_amount" class="font-weight-bold text-dark">المبلغ <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid"
                                            id="payment_amount" name="amount" step="0.01" min="0.01"
                                            required>
                                        <span class="input-group-text bg-light">شيكل</span>
                                    </div>
                                    <small class="text-muted mt-1">الحد الأقصى: <span id="paymentMaxAmount"></span>
                                        شيكل</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="font-weight-bold text-dark">طريقة الدفع <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" id="payment_method"
                                        name="payment_method" required>
                                        <option value="">اختر طريقة الدفع...</option>
                                        @foreach ($paymentMethods as $key => $method)
                                            <option value="{{ $key }}">{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="font-weight-bold text-dark">تاريخ الدفع <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-solid" id="payment_date"
                                        name="payment_date" value="{{ now()->format('Y-m-d') }}"
                                        max="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_receipt_number" class="font-weight-bold text-dark">رقم
                                        الإيصال</label>
                                    <input type="text" class="form-control form-control-solid"
                                        id="payment_receipt_number" name="receipt_number" placeholder="اختياري">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="payment_notes" class="font-weight-bold text-dark">ملاحظات</label>
                                    <textarea class="form-control form-control-solid" id="payment_notes" name="notes" rows="3"
                                        placeholder="ملاحظات إضافية (اختياري)"></textarea>
                                </div>
                            </div>
                        </div>

                        @if ($openBatch)
                            <div class="alert alert-success border-0 mb-0" style="border-radius: 12px;">
                                <i class="fas fa-check-circle"></i> سيتم إضافة هذه الدفعة إلى الرزمة اليومية
                                #{{ $openBatch->id }}
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mb-0" style="border-radius: 12px;">
                                <i class="fas fa-exclamation-triangle"></i> لا توجد رزمة يومية مفتوحة حالياً. سيتم
                                تسجيل الدفعة بدون رزمة.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary font-weight-bolder"
                            data-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-success font-weight-bolder">
                            <i class="fas fa-money-bill-wave"></i> تسجيل الدفعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // البحث عن المكلف في النافذة المنبثقة
            $('#modalSearchTaxpayerBtn').click(function() {
                const identityNumber = $('#modal_identity_number').val().trim();

                if (!identityNumber) {
                    $('#modal_identity_number_error').text('يرجى إدخال رقم الهوية').show();
                    return;
                }

                $('#modal_identity_number_error').hide();
                $('#modalSubmitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> جاري البحث...');

                $.ajax({
                    url: '{{ route('external-collections.search-taxpayer') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        identity_number: identityNumber
                    },
                    success: function(response) {
                        if (response.found) {
                            const mokalaf = response.mokalaf;
                            $('#modal_taxpayer_name').val(mokalaf.name);
                            $('#modalInfoName').text(mokalaf.name);
                            $('#modalInfoIdentity').text(mokalaf.identity_number);
                            $('#modalInfoLicense').text(mokalaf.license_number || '-');
                            $('#modalInfoAddress').text(mokalaf.address || '-');
                            $('#modalInfoMobile').text(mokalaf.mobile || '-');
                            $('#modalTaxpayerInfo').removeClass('d-none');
                        } else {
                            $('#modal_taxpayer_name').val('');
                            $('#modalTaxpayerInfo').addClass('d-none');
                            alert(response.message || 'لم يتم العثور على المكلف');
                        }
                        $('#modalSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> حفظ');
                    },
                    error: function() {
                        alert('حدث خطأ أثناء البحث عن المكلف');
                        $('#modalSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> حفظ');
                    }
                });
            });

            // البحث عند الضغط على Enter في حقل رقم الهوية
            $('#modal_identity_number').keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    $('#modalSearchTaxpayerBtn').click();
                }
            });

            // التحقق من صحة النموذج قبل الإرسال
            $('#modalAddForm').on('submit', function(e) {
                let isValid = true;

                // التحقق من رقم الهوية
                if (!$('#modal_identity_number').val().trim()) {
                    $('#modal_identity_number_error').text('رقم الهوية مطلوب').show();
                    isValid = false;
                } else {
                    $('#modal_identity_number_error').hide();
                }

                // التحقق من القسم
                if (!$('#modal_section_id').val()) {
                    $('#modal_section_id_error').text('اختيار القسم مطلوب').show();
                    isValid = false;
                } else {
                    $('#modal_section_id_error').hide();
                }

                // التحقق من المبلغ
                if (!$('#modal_amount').val() || parseFloat($('#modal_amount').val()) <= 0) {
                    $('#modal_amount_error').text('المبلغ يجب أن يكون أكبر من صفر').show();
                    isValid = false;
                } else {
                    $('#modal_amount_error').hide();
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // إعادة تعيين النافذة عند الإغلاق
            $('#addExternalCollectionModal').on('hidden.bs.modal', function() {
                $('#modalAddForm')[0].reset();
                $('#modalTaxpayerInfo').addClass('d-none');
                $('.invalid-feedback').hide();
                $('#modalSubmitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> حفظ');
            });

            // ==================== Edit Modal ====================
            $('.open-edit-modal').click(function(e) {
                e.preventDefault();
                const $btn = $(this);
                const id = $btn.data('id');
                const identityNumber = $btn.data('identity-number');
                const taxpayerName = $btn.data('taxpayer-name');
                const sectionId = $btn.data('section-id');
                const amount = $btn.data('amount');
                const notes = $btn.data('notes');

                // Build form action URL
                const actionUrl = '{{ route('external-collections.update', ':id') }}'.replace(':id', id);
                $('#editExternalCollectionForm').attr('action', actionUrl);

                // Populate fields
                $('#edit_identity_number').val(identityNumber);
                $('#edit_taxpayer_name').val(taxpayerName);
                $('#edit_section_id').val(sectionId);
                $('#edit_amount').val(amount);
                $('#edit_notes').val(notes);

                // Reset taxpayer info card
                $('#editTaxpayerInfo').addClass('d-none');
            });

            // البحث عن المكلف في نموذج التعديل
            $('#editSearchTaxpayerBtn').click(function() {
                const identityNumber = $('#edit_identity_number').val().trim();
                if (!identityNumber) {
                    alert('يرجى إدخال رقم الهوية');
                    return;
                }

                $.ajax({
                    url: '{{ route('external-collections.search-taxpayer') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        identity_number: identityNumber
                    },
                    success: function(response) {
                        if (response.found) {
                            const mokalaf = response.mokalaf;
                            $('#edit_taxpayer_name').val(mokalaf.name);
                            $('#editInfoName').text(mokalaf.name);
                            $('#editInfoIdentity').text(mokalaf.identity_number);
                            $('#editInfoLicense').text(mokalaf.license_number || '-');
                            $('#editInfoAddress').text(mokalaf.address || '-');
                            $('#editInfoMobile').text(mokalaf.mobile || '-');
                            $('#editTaxpayerInfo').removeClass('d-none');
                        } else {
                            $('#edit_taxpayer_name').val('');
                            $('#editTaxpayerInfo').addClass('d-none');
                            alert(response.message || 'لم يتم العثور على المكلف');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء البحث عن المكلف');
                    }
                });
            });

            // البحث عند Enter في حقل رقم الهوية (تعديل)
            $('#edit_identity_number').keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    $('#editSearchTaxpayerBtn').click();
                }
            });

            // إعادة تعيين نموذج التعديل عند الإغلاق
            $('#editExternalCollectionModal').on('hidden.bs.modal', function() {
                $('#editExternalCollectionForm')[0].reset();
                $('#editTaxpayerInfo').addClass('d-none');
            });

            // ==================== Payment Modal ====================
            $('.open-payment-modal').click(function(e) {
                e.preventDefault();
                const $btn = $(this);
                const id = $btn.data('id');
                const taxpayerName = $btn.data('taxpayer-name');
                const amount = parseFloat($btn.data('amount')) || 0;
                const remainingAmount = parseFloat($btn.data('remaining-amount')) || 0;

                // Build form action URL
                const actionUrl = '{{ route('external-collections.process-payment', ':id') }}'.replace(
                    ':id', id);
                $('#paymentExternalCollectionForm').attr('action', actionUrl);

                // Populate info card
                $('#paymentTaxpayerName').text(taxpayerName);
                $('#paymentTotalAmount').text(amount.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }) + ' شيكل');
                $('#paymentRemainingAmount').text(remainingAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }) + ' شيكل');

                // Set payment amount defaults
                $('#payment_amount').val(remainingAmount > 0 ? remainingAmount : '');
                $('#payment_amount').attr('max', remainingAmount);
                $('#paymentMaxAmount').text(remainingAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));
            });

            // التحقق من مبلغ الدفع
            $('#payment_amount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                const max = parseFloat($(this).attr('max')) || 0;
                if (amount > max) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // إعادة تعيين نموذج الدفع عند الإغلاق
            $('#paymentExternalCollectionModal').on('hidden.bs.modal', function() {
                $('#paymentExternalCollectionForm')[0].reset();
                $('#payment_amount').removeClass('is-invalid');
            });
        });
    </script>
@endsection
