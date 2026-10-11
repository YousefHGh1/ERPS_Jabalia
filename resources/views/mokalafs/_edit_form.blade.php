@if(request('modal'))
<input type="hidden" name="from_modal" value="1">
@endif

<form method="POST" action="{{ route('mokalafs.update', $mokalaf) }}" id="mokalaf-edit-form" style="padding: 0;">
    @csrf
    @method('PUT')
    @if(request('modal'))
    <input type="hidden" name="from_modal" value="1">
    @endif
    
    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div style="background: #f3f6f9; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
                <h5 class="text-dark font-weight-bold" style="font-size: 13px; margin-bottom: 10px !important;">{{ __('البيانات الأساسية') }}</h5>
                
                <div class="row" style="margin: 0;">
                    <div class="col-md-6 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('هوية | معرف | مشتغل مرخص') }}</label>
                            <div class="input-icon" style="position: relative;">
                                <input type="text" class="form-control form-control-sm form-control-solid" 
                                    value="{{ $mokalaf->identifier }}" readonly style="font-size: 13px; padding: 6px 12px; background-color: #f3f6f9;" />
                                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>
                            </div>
                            <small class="form-text text-muted" style="font-size: 11px; margin-top: 2px;">{{ $mokalaf->identifier_type }}</small>
                        </div>
                    </div>
                    <div class="col-md-6 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label for="mokalaf-edit-name" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('الاسم الكامل') }} <span class="text-danger">*</span></label>
                            <input name="name" type="text" id="mokalaf-edit-name" class="form-control form-control-sm form-control-solid" 
                                value="{{ $mokalaf->name }}" placeholder="{{ __('أدخل الاسم الكامل') }}" required style="font-size: 13px; padding: 6px 12px;" />
                        </div>
                    </div>
                </div>

                <div class="row" style="margin: 0;">
                    <div class="col-md-6 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label for="mokalaf-edit-mobile" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('رقم الجوال') }}</label>
                            <div class="input-icon" style="position: relative;">
                                <input name="mobile" type="text" id="mokalaf-edit-mobile" class="form-control form-control-sm form-control-solid" 
                                    value="{{ $mokalaf->mobile ?? '' }}" placeholder="{{ __('رقم الجوال') }}" style="font-size: 13px; padding: 6px 35px 6px 12px;" />
                                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-phone text-muted" style="font-size: 14px;"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div style="background: #f3f6f9; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
                <h5 class="text-dark font-weight-bold" style="font-size: 13px; margin-bottom: 10px !important;">{{ __('بيانات العنوان') }}</h5>
                
                <div class="row" style="margin: 0;">
                    <div class="col-12 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label for="mokalaf-edit-address" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('العنوان') }}</label>
                            <textarea name="address" id="mokalaf-edit-address" class="form-control form-control-sm form-control-solid" rows="2" 
                                placeholder="{{ __('أدخل العنوان') }}" style="font-size: 13px; padding: 6px 12px; resize: vertical;">{{ $mokalaf->address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin: 0;">
        <div class="col-12" style="padding: 0;">
            <div class="d-flex justify-content-end" style="padding-top: 5px;">
                <button type="button" class="btn btn-sm btn-warning font-weight-bolder mr-2" data-dismiss="modal" style="font-size: 12px; padding: 6px 16px;">
                    <i class="fas fa-times-circle"></i> {{ __('إلغاء') }}
                </button>
                <button type="submit" class="btn btn-sm btn-primary font-weight-bolder" style="font-size: 12px; padding: 6px 16px;">
                    <i class="fas fa-check"></i> {{ __('تحديث') }}
                </button>
            </div>
        </div>
    </div>
</form>
