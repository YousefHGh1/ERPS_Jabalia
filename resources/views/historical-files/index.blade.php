@extends('layout.master')

@section('title', 'الاستعلام التاريخي للحرف ')
@section('page_title')
    لوحة التحكم
@endsection

@section('sub_main')
 الحرف والصناعات
@endsection

@section('sub_title')
     الاستعلام التاريخي
@endsection
@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="الاستعلام التاريخي للحرف"
                subtitle="ابحث عن الملفات التاريخية حسب الرقم أو الاسم
                        أو الحالة أو المصدر"
            ></x-page-header>
        <div class="card card-custom shadow-sm border-0">
            <div class="card-body">
                <form method="get" class="form">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label>المصدر</label>
                            <input type="text" readonly class="form-control form-control-solid"
                                value="الحرف والصناعات" />
                        </div>
                        <div class="col-md-2">
                            <label>العمود</label>
                            <select name="column" class="form-control form-control-solid">
                                <option value="">-- لا تستخدم عمود --</option>
                                <option value="file_number"
                                    {{ ($filters['column'] ?? '') === 'file_number' ? 'selected' : '' }}>رقم الملف</option>
                                <option value="owner" {{ ($filters['column'] ?? '') === 'owner' ? 'selected' : '' }}>المالك
                                </option>
                                <option value="status" {{ ($filters['column'] ?? '') === 'status' ? 'selected' : '' }}>
                                    الحالة</option>
                                <option value="address" {{ ($filters['column'] ?? '') === 'address' ? 'selected' : '' }}>
                                    العنوان</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>بحث عام</label>
                            <input name="search" value="{{ $filters['search'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-2">
                            <label>رقم الملف</label>
                            <input name="file_number" value="{{ $filters['file_number'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-2">
                            <label>اسم المالك</label>
                            <input name="owner_name" value="{{ $filters['owner_name'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-2">
                            <label>الحالة</label>
                            <input name="status" value="{{ $filters['status'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                    </div>
                    <div class="row mt-4 align-items-end">
                        <div class="col-md-2">
                            <label>من تاريخ</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-2">
                            <label>إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-8 text-right">
                            <button class="btn btn-primary">بحث</button>
                            <a href="{{ route('historical-files.index') }}" class="btn btn-light-primary ml-2">مسح</a>
                            <a href="{{ route('historical-files.export', request()->query()) }}"
                                class="btn btn-success ml-2">تصدير Excel</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-8">
                    <table class="table table-head-custom table-borderless">
                        <thead>
                            <tr>
                                <th>نوع الحرفة</th>
                                <th>رقم الملف</th>
                                <th>المالك</th>
                                <th>الحالة</th>
                                <th>حالة الملف</th>
                                <th>العنوان</th>
                                <th>ملخص</th>
                                {{-- <th>التاريخ</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $row)
                                <tr>
                                    <td><span class="label label-inline label-light-info">{{ $row['craft_type'] }}</span>
                                    </td>
                                    <td>{{ $row['file_number'] ?? '—' }}</td>
                                    <td>{{ $row['owner'] ?? '—' }}</td>
                                    <td>{{ $row['status'] ?? '—' }}</td>
                                    <td>{{ $row['file_status'] ?? '—' }}</td>
                                    <td>{{ $row['address'] ?? '—' }}</td>
                                    <td>{{ Str::limit($row['summary'] ?? '—', 80) }}</td>
                                    {{-- <td>{{ $row['date'] ?? '—' }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">لا توجد نتائج</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                            {!! $results->withQueryString()->links('pagination::bootstrap-5') !!}

                </div>
            </div>
        </div>
    </div>
@endsection
