@extends('layout.master')

@section('title')
    @if($mail == 4) {{ __('الطلبات قيد الانتظار') }}
    @elseif($mail == 1) {{ __('الطلبات المقبولة') }}
    @elseif($mail == 2) {{ __('الطلبات المعلقة') }}
    @elseif($mail == 3) {{ __('الطلبات المرفوضة') }}
    @else {{ __('كل الطلبات') }}
    @endif
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('بريد الطلبات') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-header border-0 py-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">
                        @if($mail == 4) {{ __('الطلبات قيد الانتظار') }}
                        @elseif($mail == 1) {{ __('الطلبات المقبولة') }}
                        @elseif($mail == 2) {{ __('الطلبات المعلقة') }}
                        @elseif($mail == 3) {{ __('الطلبات المرفوضة') }}
                        @else {{ __('كل الطلبات') }}
                        @endif
                    </span>
                    <span class="text-muted mt-3 font-weight-bold font-size-sm">{{ __('إدارة وتصفح طلبات الأقسام') }}</span>
                </h3>
                <div class="card-toolbar">
                    <div class="btn-group" role="group">
                        <a href="{{ route('requests.mail.waiting') }}"
                           class="btn btn-sm {{ $mail == 4 ? 'btn-primary' : 'btn-light-primary' }}">
                            {{ __('قيد الانتظار') }}
                        </a>
                        <a href="{{ route('requests.mail.accepted') }}"
                           class="btn btn-sm {{ $mail == 1 ? 'btn-success' : 'btn-light-success' }}">
                            {{ __('مقبول') }}
                        </a>
                        <a href="{{ route('requests.mail.pending') }}"
                           class="btn btn-sm {{ $mail == 2 ? 'btn-warning' : 'btn-light-warning' }}">
                            {{ __('معلق') }}
                        </a>
                        <a href="{{ route('requests.mail.unaccepted') }}"
                           class="btn btn-sm {{ $mail == 3 ? 'btn-danger' : 'btn-light-danger' }}">
                            {{ __('مرفوض') }}
                        </a>
                        <a href="{{ route('requests.mail.all') }}"
                           class="btn btn-sm {{ $mail == 0 ? 'btn-info' : 'btn-light-info' }}">
                            {{ __('الكل') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                {{-- نموذج البحث البسيط --}}
                <div class="mb-5">
                    <form method="GET" class="form">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold text-dark">{{ __('البحث') }}</label>
                                <div class="input-icon">
                                    <input type="text"
                                           name="search"
                                           value="{{ request('search') }}"
                                           class="form-control form-control-solid"
                                           placeholder="{{ __('البحث برقم الطلب، اسم المكلف، أو اسم القسم') }}">
                                    <span><i class="fas fa-search text-muted"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 align-self-end">
                                <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                                    <i class="fas fa-search"></i> {{ __('بحث') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-light-danger font-weight-bolder">
                                    <i class="fas fa-sync-alt"></i> {{ __('إعادة تعيين') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr>
                                <th width="10%">{{ __('رقم الطلب') }}</th>
                                <th width="18%">{{ __('نوع الطلب') }}</th>
                                <th width="20%">{{ __('مقدم الطلب') }}</th>
                                <th width="15%">{{ __('الجهة (قسم/دائرة)') }}</th>
                                <th width="15%">{{ __('تاريخ الإنشاء') }}</th>
                                <th width="12%">{{ __('حالة المسار') }}</th>
                                <th width="10%">{{ __('الإجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($responses as $response)
                                @php
                                    $req = $response->request;
                                @endphp
                                @if($req)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $req->id }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $req->requestType->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($req->mokalaf)
                                                <span class="text-dark-75 font-weight-bolder">
                                                    {{ $req->join && $req->common_name ? $req->common_name : $req->mokalaf->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark-75 font-weight-bold">
                                                {{ $response->unit_name }}
                                            </span>
                                            @if ($response->unit_type === 'department')
                                                <span class="badge badge-light-info font-size-xs px-2 py-1">دائرة</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional($req->created_at)->format('d-m-Y H:i') }}
                                        </td>
                                        <td>
                                            @php
                                                $type = $response->response_type;
                                                $badgeClass = $type == 1 ? 'success' : ($type == 2 ? 'warning' : ($type == 3 ? 'danger' : 'info'));
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ $response->responseType->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('requests.mail.show', ['requestId' => $req->id, 'responseId' => $response->id]) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> {{ __('عرض') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('لا توجد بيانات') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($responses) && $responses instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-4">
                        <div class="d-flex align-items-center py-2">
                            @if($responses->total() > 0)
                                <span class="text-muted">
                                    {{ __('عرض') }} {{ $responses->firstItem() }} {{ __('إلى') }} {{ $responses->lastItem() }}
                                    {{ __('من') }} {{ $responses->total() }} {{ __('سجل') }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('لا توجد سجلات') }}</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center py-2">
                            {{-- {{ $responses->appends(request()->except('page'))->links() }} --}}
                            {!! $responses->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
