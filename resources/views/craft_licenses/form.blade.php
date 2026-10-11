@extends('layout.master')

@section('title')
    تعديل حرفة
@endsection

@section('page_title')
    {{ __('Edit _License') }}
@endsection
@section('sub_main')
    {{ __('Edit License') }}
@endsection
@section('sub_title')
    {{ __('Edit License') }}
@endsection


@section('content')
    <div class="container-fluid" dir="rtl">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="تعديل ملف حرفة"
            >
                <x-slot name="actions">
                    <a href="{{ route('craft_licenses.print', $license->id) }}"
                                            class="btn btn-primary font-weight-bolder font-size-sm">
                                            <i class="fas fa-print"></i> طباعة الملف
                                        </a>
                                        <a href="{{ route('craft_licenses.index') }}"
                                            class="btn btn-light-primary font-weight-bolder font-size-sm ml-2">
                                            <i class="fas fa-arrow-right"></i> عودة
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <form action="{{ route('craft_licenses.update', $license->id) }}" method="POST" id="licenseForm">
                    @csrf
                    @method('PUT')

                    @include('craft_licenses.partials.form_fields', ['license' => $license])

                    <div class="card-footer text-right">
                        <button type="submit" id="saveBtn" class="btn btn-success font-weight-bolder px-10">تحديث
                            البيانات</button>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($histories))
            <div class="card card-custom gutter-b">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title">
                        <span class="card-label font-weight-bolder text-dark">سجل الرسوم والتعديلات (Read-only)</span>
                    </h3>
                </div>
                <div class="card-body pt-7">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                                <tr class="text-left">
                                    <th style="min-width: 150px">العملية</th>
                                    <th style="min-width: 200px">نوع الحرفة</th>
                                    <th style="min-width: 150px">القيمة</th>
                                    <th style="min-width: 150px">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($histories as $history)
                                    <tr>
                                        <td>{{ $history->operation_type }}</td>
                                        <td>{{ $history->craft_type }}</td>
                                        <td>{{ number_format($history->fee_amount, 2) }} ₪</td>
                                        <td>{{ $history->action_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @include('craft_licenses.partials.scripts', [
        'license' => $license,
        'next_file_number' => $next_file_number ?? '',
    ])
@endsection
