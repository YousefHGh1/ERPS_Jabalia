@extends('layout.master')

@section('title')
    إضافة تحصيل خارجي جديد
@endsection

@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
    لوحة التحكم
@endsection

@section('sub_title')
    إضافة تحصيل خارجي جديد
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="إضافة تحصيل خارجي جديد"
                subtitle="إنشاء سجل تحصيل خارجي جديد"
            >
                <x-slot name="actions">
                    <a href="{{ route('external-collections.index') }}" class="btn btn-light-primary font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> العودة
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body">
                    <form action="{{ route('external-collections.store') }}" method="POST" id="externalCollectionForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="identity_number" class="form-label">رقم الهوية <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="identity_number" name="identity_number" 
                                               value="{{ old('identity_number') }}" required>
                                        <button class="btn btn-outline-primary" type="button" id="searchTaxpayerBtn">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                    </div>
                                    @error('identity_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taxpayer_name" class="form-label">اسم المكلف</label>
                                    <input type="text" class="form-control" id="taxpayer_name" name="taxpayer_name" 
                                           value="{{ old('taxpayer_name') }}" readonly>
                                    <small class="text-muted">يتم ملؤه تلقائياً عند البحث عن المكلف</small>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات المكلف الإضافية -->
                        <div id="taxpayerInfo" class="alert alert-info d-none">
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
                                    <label for="section_id" class="form-label">القسم <span class="text-danger">*</span></label>
                                    <select class="form-select" id="section_id" name="section_id" required>
                                        <option value="">اختر القسم...</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
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
                                    <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               value="{{ old('amount') }}" step="0.01" min="0" required>
                                        <span class="input-group-text">شيكل</span>
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
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('external-collections.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ
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
    $('#searchTaxpayerBtn').click(function() {
        const identityNumber = $('#identity_number').val().trim();
        
        if (!identityNumber) {
            alert('يرجى إدخال رقم الهوية');
            return;
        }

        $.ajax({
            url: '{{ route("external-collections.search-taxpayer") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                identity_number: identityNumber
            },
            success: function(response) {
                if (response.found) {
                    const mokalaf = response.mokalaf;
                    $('#taxpayer_name').val(mokalaf.name);
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
@endsection
