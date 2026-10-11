@if(request('modal'))
    <input type="hidden" name="from_modal" value="1">
@endif

<form method="POST" action="{{ route('notices.update', $notice) }}" id="edit-notice-form" class="form" style="padding: 0;">
    @csrf
    @method('PUT')
    @if(request('modal'))
        <input type="hidden" name="from_modal" value="1">
    @endif

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div style="background: #f3f6f9; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                <h5 class="text-dark font-weight-bold mb-4" style="font-size: 14px;">
                    {{ __('بيانات الإخطار الأساسية') }}
                </h5>

                {{-- الصف الأول: رقم الإخطار + تاريخ الإخطار --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('رقم الإخطار') }}
                    </label>
                    <div class="col-lg-3">
                        <input type="text"
                               name="notice_number"
                               class="form-control"
                               value="{{ $notice->notice_number }}"
                               readonly
                               style="font-size: 13px; padding: 6px 12px; background-color:#f3f6f9; cursor:not-allowed;">
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('تاريخ الإخطار') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <input type="date"
                               name="notice_date"
                               class="form-control"
                               value="{{ optional($notice->notice_date)->format('Y-m-d') }}"
                               style="font-size: 13px; padding: 6px 12px;">
                    </div>
                </div>

                {{-- الصف الثاني: سبب الإخطار + مكان الإيجار --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('سبب الإخطار') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-4">
                        <select name="notice_reason_id" class="form-control" style="font-size: 13px;">
                            @foreach($noticeReasons as $reason)
                                <option value="{{ $reason->id }}" {{ $notice->notice_reason_id == $reason->id ? 'selected' : '' }}>
                                    {{ $reason->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('مكان الإيجار') }}
                    </label>
                    <div class="col-lg-3">
                        <select name="rent_location_id" class="form-control" style="font-size: 13px;">
                            <option value="">{{ __('اختر...') }}</option>
                            @foreach($rentLocations as $location)
                                <option value="{{ $location->id }}" {{ $notice->rent_location_id == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- الصف الثالث: الحالة --}}
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('الحالة') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-3">
                        <select name="status" class="form-control" style="font-size: 13px;">
                            <option value="draft" {{ $notice->status === 'draft' ? 'selected' : '' }}>{{ __('مسودة') }}</option>
                            <option value="sent" {{ $notice->status === 'sent' ? 'selected' : '' }}>{{ __('مرسل') }}</option>
                            <option value="delivered" {{ $notice->status === 'delivered' ? 'selected' : '' }}>{{ __('مسلّم') }}</option>
                            <option value="cancelled" {{ $notice->status === 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                        </select>
                    </div>
                </div>

                {{-- الصف الرابع: الوصف --}}
                <div class="form-group row mb-0">
                    <label class="col-lg-2 col-form-label text-right font-weight-bold text-dark" style="font-size: 12px;">
                        {{ __('الوصف') }}
                    </label>
                    <div class="col-lg-8">
                        <textarea name="description"
                                  class="form-control"
                                  rows="4"
                                  style="font-size: 13px; padding: 6px 12px; resize: vertical;">{{ $notice->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div class="d-flex justify-content-end" style="padding-top: 5px;">
                <button type="button"
                        class="btn btn-sm btn-warning font-weight-bolder mr-2"
                        data-dismiss="modal"
                        style="font-size: 12px; padding: 6px 16px;">
                    <i class="fas fa-times-circle"></i> {{ __('إلغاء') }}
                </button>
                <button type="submit"
                        class="btn btn-sm btn-primary font-weight-bolder"
                        style="font-size: 12px; padding: 6px 16px;">
                    <i class="fas fa-check"></i> {{ __('تحديث') }}
                </button>
            </div>
        </div>
    </div>
</form>

