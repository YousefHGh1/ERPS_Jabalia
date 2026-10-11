@extends('layout.master')

@section('title')
    {{ __('تفاصيل الطلب') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('بريد الطلبات') }} / {{ __('تفاصيل الطلب') }}
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
            <div class="card-header border-0 py-7">
                <div class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark font-size-h2">
                        <i class="fas fa-file text-primary mr-2"></i>
                        {{ __('تفاصيل الطلب') }} #{{ $request->id }}
                    </span>
                    <span class="text-muted mt-2 font-weight-bold font-size-sm">
                        {{ __('حالة الطلب') }}: 
                        <span class="badge badge-lg badge-{{ $status == 'مرفوض' ? 'danger' : ($status == 'معلق' ? 'warning' : ($status == 'مكتمل' ? 'success' : 'info')) }}">
                            {{ $status }}
                        </span>
                        @if(isset($request) && $request->status_reason_sections_text)
                            <span class="d-inline-block mr-2 text-dark-75">({{ $request->status_reason_sections_text }})</span>
                        @endif
                    </span>
                </div>
                <div class="card-toolbar">
                    <a href="{{ route('requests.mail.waiting') }}" class="btn btn-light-danger font-weight-bolder mr-2">
                        <i class="fas fa-arrow-right"></i> {{ __('عودة') }}
                    </a>
                    @php
                        $canUpdateStatus = Auth::user()->employee && Auth::user()->employee->section_id !== null;
                        if (!$canUpdateStatus) {
                            try {
                                $canUpdateStatus = Auth::user()->hasPermissionTo('mail.create')
                                    || Auth::user()->hasPermissionTo('mail.update')
                                    || Auth::user()->hasRole('admin');
                            } catch (\Exception $e) {
                                $canUpdateStatus = false;
                            }
                        }
                    @endphp
                    @if($response->response_type == 4 && $canUpdateStatus)
                        <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#responseModal">
                            <i class="fas fa-check"></i> {{ __('تعديل الحالة') }} / {{ __('الرد على الطلب') }}
                        </button>
                    @elseif($response->response_type == 2 && $canUpdateStatus)
                        <button type="button" class="btn btn-warning font-weight-bolder" data-toggle="modal" data-target="#responseModal">
                            <i class="fas fa-check"></i> {{ __('تعديل الحالة') }} / {{ __('فك التعليق') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Request Details -->
            <div class="col-xl-8">
                <!-- Request Information -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            {{ __('معلومات الطلب') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="row mb-6">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark mb-0">{{ __('رقم الطلب') }}</label>
                                <p class="form-control-plaintext font-weight-bolder font-size-lg text-dark">
                                    #{{ $request->id }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark mb-0">{{ __('نوع الطلب') }}</label>
                                <p class="form-control-plaintext">
                                    <span class="badge badge-lg badge-info font-weight-bold p-3">
                                        {{ $request->requestType->name ?? '-' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="mb-6">
                            <label class="font-weight-bold text-dark mb-0 font-size-lg">{{ __('نص الطلب') }}</label>
                            <div class="bg-light-primary p-6 rounded border-left-primary border-left-3 mt-3">
                                <p class="font-size-lg text-dark-75 mb-0" style="line-height: 1.8;">
                                    {!! nl2br(e($request->text)) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responses History -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-check text-primary mr-2"></i>
                            {{ __('مسارات وردود الطلب') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="20%">{{ __('الجهة (قسم/دائرة)') }}</th>
                                        <th width="15%">{{ __('الموظف') }}</th>
                                        <th width="15%">{{ __('تاريخ الوصول') }}</th>
                                        <th width="12%">{{ __('حالة المسار') }}</th>
                                        <th width="15%">{{ __('تاريخ الرد') }}</th>
                                        <th width="18%">{{ __('الرد / سبب الرفض') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->responses->sortBy('created_at') as $resp)
                                        @php
                                            $responseTypeId = $resp->response_type;
                                            $responseTypeName = $resp->responseType->name ?? '-';
                                            $isRejected = $responseTypeId == 3;
                                            $isApproved = $responseTypeId == 1;
                                            $isPending = $responseTypeId == 2;
                                            $isWaiting = $responseTypeId == 4;
                                            
                                            $badgeClass = 'secondary';
                                            if ($isApproved) $badgeClass = 'success';
                                            elseif ($isRejected) $badgeClass = 'danger';
                                            elseif ($isPending) $badgeClass = 'warning';
                                            elseif ($isWaiting) $badgeClass = 'info';
                                        @endphp
                                        <tr class="{{ $resp->id == $response->id ? 'table-active' : '' }}">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="font-weight-bolder text-dark">
                                                    {{ $resp->unit_name }}
                                                </span>
                                                @if ($resp->unit_type === 'department')
                                                    <span class="badge badge-light-info font-size-xs px-2 py-1">دائرة</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($resp->responsedBy)
                                                    <span class="font-weight-bold text-primary">
                                                        {{ $resp->responsedBy->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="direction: ltr;" class="text-right">
                                                {{ $request->created_at->format('d-m-Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-lg badge-{{ $badgeClass }} font-weight-bold">
                                                    {{ $responseTypeName }}
                                                </span>
                                            </td>
                                            <td style="direction: ltr;" class="text-right">
                                                @if($resp->responsed_at)
                                                    {{ \Carbon\Carbon::parse($resp->responsed_at)->format('d-m-Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isRejected && $resp->notes && $resp->notes != '-')
                                                    <div class="bg-light-danger p-3 rounded border-left-danger border-left-3">
                                                        <div class="font-weight-bolder text-danger mb-1">
                                                            <i class="fas fa-times-circle text-danger"></i> {{ __('سبب الرفض') }}:
                                                        </div>
                                                        <div class="text-dark-75 font-size-sm">
                                                            {{ $resp->notes }}
                                                        </div>
                                                    </div>
                                                @elseif($resp->notes && $resp->notes != '-')
                                                    <div class="text-dark-75 font-size-sm">
                                                        {{ $resp->notes }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-xl-4">
                <!-- Current Response Status -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-check text-primary mr-2"></i>
                            {{ __('حالة المسار الحالي') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">{{ __('الجهة (قسم/دائرة)') }}</label>
                            <p class="font-weight-bolder text-dark font-size-lg">
                                {{ $response->unit_name }}
                                @if ($response->unit_type === 'department')
                                    <span class="badge badge-light-info font-size-sm px-2 py-1">دائرة</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">{{ __('حالة المسار') }}</label>
                            <p>
                                <span class="badge badge-lg badge-{{ $response->response_type == 4 ? 'info' : ($response->response_type == 1 ? 'success' : ($response->response_type == 2 ? 'warning' : 'danger')) }} font-weight-bold p-3">
                                    {{ $response->responseType->name ?? '-' }}
                                </span>
                            </p>
                        </div>
                        @if($response->responsedBy)
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">{{ __('الموظف') }}</label>
                            <p class="font-weight-bolder text-dark font-size-lg">
                                {{ $response->responsedBy->name }}
                            </p>
                        </div>
                        @endif
                        @if($response->responsed_at)
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">{{ __('تاريخ الرد') }}</label>
                            <p class="font-weight-bolder text-dark font-size-lg" style="direction: ltr;">
                                {{ \Carbon\Carbon::parse($response->responsed_at)->format('d-m-Y H:i') }}
                            </p>
                        </div>
                        @endif
                        @if($response->notes && $response->notes != '-')
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">{{ __('الرد') }}</label>
                            <div class="bg-light p-4 rounded">
                                <p class="mb-0">{{ $response->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                @if($request->attachments->count() > 0)
                <div class="card card-custom gutter-b shadow-sm border-0">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-paperclip text-primary mr-2"></i>
                            {{ __('المرفقات') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        @foreach($request->attachments as $attachment)
                            <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file text-primary mr-3 font-size-h3"></i>
                                    <div>
                                        <span class="text-dark font-weight-bolder">{{ $attachment->file_name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $attachment->file_size ? formatBytes($attachment->file_size) : '' }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('requests.download-attachment', ['request' => $request->id, 'attachment' => $attachment->id]) }}" 
                                   class="btn btn-sm btn-light-primary" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Response Modal -->
    @if(($response->response_type == 4 || $response->response_type == 2) && $canUpdateStatus)
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bolder">
                        <i class="fas fa-check text-primary mr-2"></i>
                        {{ __('الرد على الطلب') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="responseForm">
                    <div class="modal-body">
                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                        <input type="hidden" name="response_id" value="{{ $response->id }}">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('اختر نوع الرد') }} <span class="text-danger">*</span></label>
                            <select class="form-control form-control-solid" id="response_type" name="response_type" required>
                                <option value="">{{ __('اختر نوع الرد') }}</option>
                                @foreach($responseTypes ?? [] as $type)
                                    <option value="{{ $type->id }}" {{ $response->response_type == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('نص الرد / سبب الرفض') }}</label>
                            <textarea class="form-control form-control-solid" id="notes" name="notes" rows="4" 
                                      placeholder="{{ __('أدخل نص الرد أو سبب الرفض إن وجد') }}">{{ $response->notes != '-' ? $response->notes : '' }}</textarea>
                            <span class="form-text text-muted">{{ __('في حالة الرفض، يرجى توضيح سبب الرفض') }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-check"></i> {{ __('حفظ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#responseForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                request_id: $('input[name="request_id"]').val(),
                response_id: $('input[name="response_id"]').val(),
                response_type: $('#response_type').val(),
                notes: $('#notes').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!formData.response_type) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'يرجى اختيار نوع الرد'
                });
                return;
            }

            Swal.fire({
                title: 'جاري الحفظ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("requests.mail.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح',
                        text: response.message || 'تم حفظ الرد بنجاح',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMsg = 'حدث خطأ أثناء الحفظ';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: errorMsg
                    });
                }
            });
        });
    });
</script>
@endsection
