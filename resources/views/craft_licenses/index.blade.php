@extends('layout.master')

@section('title')
    {{ __('Craft Licenses') }}
@endsection

@section('page_title')
    {{ __('Craft Licenses') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('List Licenses') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Craft Licenses') }}"
                subtitle="{{ __('Manage craft licenses and fees') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </a>
                                        <form action="{{ route('craft_licenses.accrue_annual_fees') }}" method="POST" class="d-inline mr-2"
                                              onsubmit="return confirm('هل أنت متأكد من ترصيد رسوم السنة الحالية لكل رخص الحرف النشطة؟');">
                                            @csrf
                                            <button type="submit" class="btn btn-light-warning font-weight-bolder">
                                                <i class="fas fa-sync-alt"></i> ترصيد رسوم السنة الحالية
                                            </button>
                                        </form>
                                        <a href="{{ route('craft_licenses.create') }}" class="btn btn-primary font-weight-bolder">
                                            <i class="fas fa-plus"></i> {{ __('Create') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                <div id="collapseSearch" class="collapse mb-7">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ route('craft_licenses.index') }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('Search by name or craft') }}</label>
                                    <input name="search" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('Search here...') }}" value="{{ request('search') }}" />
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('File Number') }}</label>
                                    <input name="file_number" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('File Number') }}" value="{{ request('file_number') }}" />
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('Status') }}</label>
                                    <select name="status" class="form-control form-control-solid">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>
                                            {{ __('New License') }}</option>
                                        <option value="renewal" {{ request('status') == 'renewal' ? 'selected' : '' }}>
                                            {{ __('Annual Renewal') }}</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
                                            {{ __('Closed') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">تصنيف الحرفة</label>
                                    <select name="classification" class="form-control form-control-solid selectpicker" data-live-search="true">
                                        <option value="">{{ __('الكل') }}</option>
                                        <option value="مهنية" {{ request('classification') == 'مهنية' ? 'selected' : '' }}>1 - مهنية</option>
                                        <option value="مهنية مواصلات" {{ request('classification') == 'مهنية مواصلات' ? 'selected' : '' }}>2 - مهنية مواصلات</option>
                                        <option value="صناعية" {{ request('classification') == 'صناعية' ? 'selected' : '' }}>3 - صناعية</option>
                                        <option value="تجارية" {{ request('classification') == 'تجارية' ? 'selected' : '' }}>4 - تجارية</option>
                                        <option value="مطاعم/مطابخ" {{ request('classification') == 'مطاعم/مطابخ' ? 'selected' : '' }}>5 - مطاعم/مطابخ</option>
                                        <option value="مخابز" {{ request('classification') == 'مخابز' ? 'selected' : '' }}>6 - مخابز</option>
                                        <option value="محروقات" {{ request('classification') == 'محروقات' ? 'selected' : '' }}>7 - محروقات</option>
                                        <option value="صالات ومناسبات" {{ request('classification') == 'صالات ومناسبات' ? 'selected' : '' }}>8 - صالات ومناسبات</option>
                                        <option value="خدماتية" {{ request('classification') == 'خدماتية' ? 'selected' : '' }}>9 - خدماتية</option>
                                        <option value="غذائية A" {{ request('classification') == 'غذائية A' ? 'selected' : '' }}>10 - غذائية A</option>
                                        <option value="غذائية B" {{ request('classification') == 'غذائية B' ? 'selected' : '' }}>11 - غذائية B</option>
                                        <option value="تحلية مياه" {{ request('classification') == 'تحلية مياه' ? 'selected' : '' }}>12 - تحلية مياه</option>
                                        <option value="طبية" {{ request('classification') == 'طبية' ? 'selected' : '' }}>13 - طبية</option>
                                        <option value="زراعية" {{ request('classification') == 'زراعية' ? 'selected' : '' }}>14 - زراعية</option>
                                        <option value="مصانع غذائية" {{ request('classification') == 'مصانع غذائية' ? 'selected' : '' }}>15 - مصانع غذائية</option>
                                        <option value="يافطات و اعلانات" {{ request('classification') == 'يافطات و اعلانات' ? 'selected' : '' }}>16 - يافطات و اعلانات</option>
                                        <option value="مولدات" {{ request('classification') == 'مولدات' ? 'selected' : '' }}>17 - مولدات</option>
                                        <option value="مطاحن" {{ request('classification') == 'مطاحن' ? 'selected' : '' }}>0 - مطاحن</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">نوع الحرفة التفصيلي</label>
                                    <select name="detailed_craft_type" id="search_detailed_craft_type" class="form-control form-control-solid selectpicker" data-live-search="true">
                                        <option value="">اختر التصنيف أولاً...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                    <a href="{{ route('craft_licenses.index') }}"
                                        class="btn btn-light-primary font-weight-bolder px-8 ml-2">
                                        <i class="fas fa-sync-alt"></i> {{ __('Reset') }}
                                    </a>
                                    <a href="{{ route('craft_licenses.export', request()->all()) }}"
                                        class="btn btn-success font-weight-bolder px-8 ml-2">
                                        <i class="fa fa-file-csv"></i> {{ __('Export Data') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" data-responsive="true">
                    <table class="table table-head-custom table-vertical-center table-borderless">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th>{{ __('File Number') }}</th>
                                <th>{{ __('Owner / ID') }}</th>
                                <th>{{ __('Craft Type / Classification') }}</th>
                                <th>DEBT_2017</th>
                                <th>{{ __('Financial (Fees/Debts/Signboard)') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th class="text-danger">{{ __('Remaining') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($licenses as $license)
                                <tr>
                                    <td class="font-weight-bolder" data-label="{{ __('File Number') }}">{{ $license->file_number }}</td>
                                    <td data-label="{{ __('Owner / ID') }}">
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-dark-75 font-weight-bolder font-size-lg">{{ $license->owner_name }}</span>
                                            <span class="text-muted font-weight-bold">{{ $license->id_number }}</span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('Craft Type / Classification') }}">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bolder">{{ $license->detailed_craft_type }}</span>
                                            <span class="text-muted font-size-sm">{{ $license->classification }}</span>
                                        </div>
                                    </td>
                                    <td data-label="DEBT_2017">
                                        @php
                                            $colors = [
                                                'closed' => 'label-light-danger',
                                                'new' => 'label-light-primary',
                                                'renewal' => 'label-light-warning',
                                                'undefined' => 'label-light-success',
                                                'stopped' => 'label-light-danger',
                                                'active_no_file' => 'label-light-info',
                                            ];
                                            $statusLabels = [
                                                'closed' => 'حرفة مغلق',
                                                'new' => 'ترخيص جديد',
                                                'renewal' => 'تجديد سنوي',
                                                'undefined' => 'غير محدد',
                                                'stopped' => 'حزمة متوقفة',
                                                'active_no_file' => 'بدون ملف',
                                            ];
                                        @endphp
                                        <span class="font-weight-bolder text-danger">
                                            {{ number_format($license->total_debts ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('Financial (Fees/Debts/Signboard)') }}">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bold">رسوم:
                                                {{ number_format($license->craft_fees, 2) }}</span>
                                            <span class="text-muted font-size-sm">ديون:
                                                {{ number_format($license->total_debts, 2) }} | يافطة:
                                                {{ number_format($license->signboard_fees, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-success font-weight-bolder" data-label="{{ __('Paid') }}">
                                        {{ number_format($license->payment_amount, 2) }} ₪</td>
                                    <td class="text-danger font-weight-bolder" data-label="{{ __('Remaining') }}">{{ number_format($license->remaining, 2) }}
                                        ₪</td>
                                    <td class="text-right pr-0 table-actions" data-label="{{ __('Actions') }}">
                                        <a href="{{ route('craft_licenses.edit', $license->id) }}"
                                            class="btn btn-icon btn-light-primary btn-sm mx-1" title="{{ __('Edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('craft_licenses.print', $license->id) }}" target="_blank"
                                            class="btn btn-icon btn-light-success btn-sm mx-1" title="{{ __('Print') }}">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="{{ route('craft_licenses.destroy', $license->id) }}" method="POST"
                                            class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                                onclick="return confirm('{{ __('confirm_message') }}')"
                                                title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                            <span
                                                class="text-muted font-weight-bold font-size-h5">{{ __('no_records') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {!! $licenses->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('craft_licenses.partials.scripts', [
        'license' => new \App\Models\CraftLicense(),
        'next_file_number' => $next_file_number,
    ])

    <script>
        $(document).ready(function() {
            // تجهيز أنواع الحرف التفصيلية للبحث
            $('[name="classification"]').on('change', function() {
                var $detailed = $('[name="detailed_craft_type"]');
                var classif = $(this).val();

                $detailed.empty().append('<option value="">اختر نوع الحرفة التفصيلي...</option>');

                if (classif && CRAFT_DETAILED_TYPES[classif]) {
                    CRAFT_DETAILED_TYPES[classif].forEach(function(item) {
                        var opt = $('<option>').val(item).text(item);
                        if ("{{ request('detailed_craft_type') }}" === item) {
                            opt.prop('selected', true);
                        }
                        $detailed.append(opt);
                    });
                    $detailed.prop('disabled', false);
                } else {
                    $detailed.prop('disabled', true);
                }

                if ($.fn.selectpicker) {
                    $detailed.selectpicker('refresh');
                }
            });

            // تحميل الأنواع إذا كان التصنيف محدداً مسبقاً
            var initialClassif = $('[name="classification"]').val();
            if (initialClassif) {
                $('[name="classification"]').trigger('change');
            }
        });
    </script>
@endsection
