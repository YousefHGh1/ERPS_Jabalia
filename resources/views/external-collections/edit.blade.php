@extends('layout.master')

@section('title')
    تعديل التحصيل الخارجي
@endsection

@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
    لوحة التحكم
@endsection

@section('sub_title')
    تعديل التحصيل الخارجي
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="تعديل التحصيل الخارجي #{{ $externalCollection->id }}"
                subtitle="تحديث معلومات التحصيل الخارجي"
            >
                <x-slot name="actions">
                    <a href="{{ route('external-collections.show', $externalCollection) }}" class="btn btn-light-info font-weight-bolder mr-2">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <a href="{{ route('external-collections.index') }}" class="btn btn-light-primary font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> العودة
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                    <form action="{{ route('external-collections.update', $externalCollection) }}" method="POST" id="externalCollectionForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="identity_number" class="font-weight-bold text-dark">رقم الهوية <span class="text-danger">*</span></label>
                                    <div class="input-icon">
                                        <input type="text" class="form-control form-control-solid" id="identity_number" name="identity_number"
                                               value="{{ old('identity_number', $externalCollection->identity_number) }}" required>
                                        <span class="input-icon-addon">
                                            <button class="btn btn-icon btn-light-primary" type="button" id="searchTaxpayerBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                    @error('identity_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taxpayer_name" class="font-weight-bold text-dark">اسم المكلف</label>
                                    <input type="text" class="form-control form-control-solid" id="taxpayer_name" name="taxpayer_name"
                                           value="{{ old('taxpayer_name', $externalCollection->taxpayer_name) }}" readonly>
                                    <small class="text-muted mt-1">يتم ملؤه تلقائياً عند البحث عن المكلف</small>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات المكلف الإضافية -->
                        <div id="taxpayerInfo" class="card card-body bg-light border-0 shadow-sm p-8 d-none" style="border-radius: 12px;">
                            <h6 class="alert-heading">معلومات المكلف:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الاسم:</strong> <span id="infoName"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>رقم الهوية:</strong> <span id="infoIdentity"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>رقم المشغل:</strong> <span id="infoLicense"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>العنوان:</strong> <span id="infoAddress"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>الجوال:</strong> <span id="infoMobile"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section_id" class="font-weight-bold text-dark">القسم <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid" id="section_id" name="section_id" required>
                                        <option value="">اختر القسم...</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ (old('section_id', $externalCollection->section_id) == $section->id) ? 'selected' : '' }}>
                                                {{ $section->name }}
                                                @if($section->department)
                                                    - {{ $section->department->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('section_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="font-weight-bold text-dark">المبلغ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid" id="amount" name="amount"
                                               value="{{ old('amount', $externalCollection->amount) }}" step="0.01" min="0" required>
                                        <span class="input-group-text bg-light">شيكل</span>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="font-weight-bold text-dark">ملاحظات</label>
                                    <textarea class="form-control form-control-solid" id="notes" name="notes" rows="3">{{ old('notes', $externalCollection->notes) }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('external-collections.index') }}" class="btn btn-light-warning font-weight-bolder">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary font-weight-bolder">
                                        <i class="fas fa-save"></i> تحديث
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // عرض معلومات المكلف الحالي إذا كان موجوداً
    @if($externalCollection->mokalaf)
        $('#taxpayerInfo').removeClass('d-none');
        $('#infoName').text('{{ $externalCollection->mokalaf->name }}');
        $('#infoIdentity').text('{{ $externalCollection->mokalaf->identity_number }}');
        $('#infoLicense').text('{{ $externalCollection->mokalaf->license_number ?? "-" }}');
        $('#infoAddress').text('{{ $externalCollection->mokalaf->address ?? "-" }}');
        $('#infoMobile').text('{{ $externalCollection->mokalaf->mobile ?? "-" }}');
    @endif

    // البحث عن المكلف
    $('#searchTaxpayerBtn').click(function() {
        const identityNumber = $('#identity_number').val().trim();
        
        if (!identityNumber) {
            alert('يرجى إدخال رقم الهوية');
            return;
        }

        $.ajax({
            url: "{{ route('external-collections.search-taxpayer') }}",
            type: "GET",
            data: { identity_number: identityNumber },
            success: function(response) {
                if (response.success && response.mokalaf) {
                    const mokalaf = response.mokalaf;
                    
                    $('#taxpayer_name').val(mokalaf.name);
                    $('#mokalaf_id').val(mokalaf.id);
                    
                    $('#infoName').text(mokalaf.name);
                    $('#infoIdentity').text(mokalaf.identity_number);
                    $('#infoLicense').text(mokalaf.license_number || '-');
                    $('#infoAddress').text(mokalaf.address || '-');
                    $('#infoMobile').text(mokalaf.mobile || '-');
                    $('#taxpayerInfo').removeClass('d-none');

                } else {
                    $('#taxpayer_name').val('');
                    $('#taxpayerInfo').addClass('d-none');
                    alert(response.message || 'لم يتم العثور على المكلف');
                }
            },
            error: function() {
                alert('حدث خطأ أثناء البحث عن المكلف');
            }
        });
    });

    // البحث عند الضغط على Enter في حقل رقم الهوية
    $('#identity_number').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#searchTaxpayerBtn').click();
        }
    });
});
</script>
@endpush
