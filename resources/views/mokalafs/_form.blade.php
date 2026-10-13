<form method="POST" action="{{ route('mokalafs.store') }}" id="mokalaf-form" style="padding: 0;">
    @csrf
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
                            <label for="identity_number" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('رقم الهوية') }} <span class="text-danger">*</span></label>
                            <div class="input-icon" style="position: relative;">
                                <input name="identity_number" type="text" class="form-control form-control-sm form-control-solid" 
                                    id="identity_number" placeholder="{{ __('أدخل رقم الهوية') }}" style="font-size: 13px; padding: 6px 35px 6px 12px;" />
                                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>
                            </div>
                            <small class="form-text text-muted" style="font-size: 11px; margin-top: 2px;">{{ __('أو رقم الترخيص') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-left: 8px; padding-right: 8px;">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('رقم الترخيص') }}</label>
                            <div class="input-icon" style="position: relative;">
                                <input name="license_number" type="text" class="form-control form-control-sm form-control-solid" 
                                    id="license_number" placeholder="{{ __('أدخل رقم الترخيص') }}" style="font-size: 13px; padding: 6px 35px 6px 12px;" />
                                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-file-alt text-muted" style="font-size: 14px;"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="manual-input" style="display: none; margin: 0;">
                    <div class="col-12 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label for="mokalaf-form-name" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('الاسم الكامل') }} <span class="text-danger">*</span></label>
                            <input name="name" type="text" id="mokalaf-form-name" class="form-control form-control-sm form-control-solid" 
                                placeholder="{{ __('أدخل الاسم الكامل') }}" style="font-size: 13px; padding: 6px 12px;" />
                        </div>
                    </div>
                </div>

                <div class="row" style="margin: 0;">
                    <div class="col-md-6 form-col-padding">
                        <div class="form-group mb-2" style="margin-bottom: 10px !important;">
                            <label for="mokalaf-form-mobile" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('رقم الجوال') }}</label>
                            <div class="input-icon" style="position: relative;">
                                <input name="mobile" type="text" id="mokalaf-form-mobile" class="form-control form-control-sm form-control-solid" 
                                    placeholder="{{ __('رقم الجوال') }}" style="font-size: 13px; padding: 6px 35px 6px 12px;" />
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
                            <label for="mokalaf-form-address" class="font-weight-bold text-dark" style="font-size: 12px; margin-bottom: 4px;">{{ __('العنوان') }}</label>
                            <textarea name="address" id="mokalaf-form-address" class="form-control form-control-sm form-control-solid" rows="2" 
                                placeholder="{{ __('أدخل العنوان') }}" style="font-size: 13px; padding: 6px 12px; resize: vertical;"></textarea>
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
                    <i class="fas fa-check"></i> {{ __('حفظ') }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    var isFetchingData = false;
    var lastFetchedId = '';

    // Reset form fields when modal opens or form loads
    function resetFormFields() {
        var nameInput = $('input[name="name"]');
        nameInput.removeAttr('readonly');
        nameInput.css({
            'background-color': '',
            'cursor': '',
            'color': ''
        });
        nameInput.removeAttr('title');
        nameInput.val('');
        lastFetchedId = '';
    }
    
    // Reset fields on page load
    resetFormFields();

    // Focus on identity number field when modal opens
    setTimeout(function() { 
        $('#identity_number').focus(); 
    }, 300);

    // Reset name field when identity number changes
    $('#identity_number').off('input').on('input', function() {
        var identityNumber = $(this).val().trim();
        
        // Reset name field if identity number is changed or cleared
        if (identityNumber.length < 9 || identityNumber !== lastFetchedId) {
            var nameInput = $('input[name="name"]');
            if (nameInput.attr('readonly')) {
                nameInput.removeAttr('readonly');
                nameInput.css({
                    'background-color': '',
                    'cursor': '',
                    'color': ''
                });
                nameInput.removeAttr('title');
                nameInput.val('');
            }
        }
        
        // Only fetch if 9 digits and not already fetching
        if (identityNumber.length === 9 && !isNaN(identityNumber) && !isFetchingData && lastFetchedId !== identityNumber) {
            isFetchingData = true;
            lastFetchedId = identityNumber;
            
            // Show loading indicator
            var inputGroup = $(this).closest('.input-icon');
            var loadingHtml = '<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3;"><i class="spinner spinner-sm spinner-primary"></i></span>';
            inputGroup.find('span i').remove();
            inputGroup.append(loadingHtml);
            
            // First check if mokalaf exists
            $.ajax({
                url: '{{ route("mokalafs.check-exists") }}',
                type: 'GET',
                data: { identity_number: identityNumber },
                success: function(response) {
                    if (response.exists) {
                        isFetchingData = false;
                        inputGroup.find('.spinner').remove();
                        inputGroup.append('<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>');
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '{{ __('تنبيه') }}',
                                text: '{{ __('هذا الشخص مضاف بالفعل كمكلف. هل تريد تعديل بياناته؟') }}',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: '{{ __('نعم') }}',
                                cancelButtonText: '{{ __('إلغاء') }}',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = response.edit_url;
                                } else {
                                    $('#identity_number').val('');
                                    lastFetchedId = '';
                                }
                            });
                        } else {
                            if (confirm('{{ __('هذا الشخص مضاف بالفعل كمكلف. هل تريد تعديل بياناته؟') }}')) {
                                window.location.href = response.edit_url;
                            } else {
                                $('#identity_number').val('');
                                lastFetchedId = '';
                            }
                        }
                        return;
                    }
                    
                    // If not exists, fetch citizen data
                    fetchCitizenData(identityNumber, inputGroup);
                },
                error: function() {
                    isFetchingData = false;
                    inputGroup.find('.spinner').remove();
                    inputGroup.append('<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>');
                    fetchCitizenData(identityNumber, inputGroup);
                }
            });
        } else if (identityNumber.length < 9) {
            // Reset if user is still typing
            lastFetchedId = '';
        }
    });

    // Function to fetch citizen data from API - using same method as engineering page
    function fetchCitizenData(identityNumber, inputGroup) {
        $.ajax({
            url: '{{ route("citizen.forMokalaf") }}',
            method: 'GET',
            data: {
                czn_id: identityNumber
            },
            success: function(response) {
                isFetchingData = false;
                inputGroup.find('.spinner').remove();
                inputGroup.append('<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>');
                
                console.log('Citizen API Response:', response);
                
                if (response && response.error) {
                    $('#manual-input').show();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('تنبيه') }}',
                            text: response.error || '{{ __('لم يتم العثور على بيانات لهذا الرقم. يمكنك إدخال البيانات يدوياً') }}',
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                    return;
                }
                
                if (response) {
                    const citizen = response;
                    console.log('API Response:', response);
                    console.log('Response keys:', Object.keys(response));
                    console.log('First Name:', citizen.FNAME_ARB);
                    console.log('Father Name:', citizen.SNAME_ARB);
                    console.log('Grandfather Name:', citizen.TNAME_ARB);
                    console.log('Family Name:', citizen.LNAME_ARB);
                    
                    // Support both formats: FNAME_ARB (from getCitizenForMokalaf) and first_name_ar (from getCitizen)
                    var firstName = citizen.FNAME_ARB || citizen.first_name_ar || citizen.firstNameAr || citizen.first_name || '';
                    var secondName = citizen.SNAME_ARB || citizen.second_name_ar || citizen.secondNameAr || citizen.second_name || '';
                    var thirdName = citizen.TNAME_ARB || citizen.third_name_ar || citizen.thirdNameAr || citizen.third_name || '';
                    var lastName = citizen.LNAME_ARB || citizen.last_name_ar || citizen.lastNameAr || citizen.last_name || '';
                    
                    // If no name parts found, try to split full_name_ar
                    if (!firstName && !secondName && !thirdName && !lastName) {
                        var fullNameAr = citizen.full_name_ar || citizen.fullNameAr || citizen.name_ar || citizen.nameAr || citizen.name || '';
                        if (fullNameAr) {
                            var nameParts = fullNameAr.trim().split(/\s+/);
                            firstName = nameParts[0] || '';
                            secondName = nameParts[1] || '';
                            thirdName = nameParts[2] || '';
                            lastName = nameParts[3] || '';
                        }
                    }
                    
                    var fullName = (firstName + ' ' + secondName + ' ' + thirdName + ' ' + lastName).trim();
                    var mobile = citizen.MOBILE || citizen.mobile || citizen.mobile_number || citizen.PHONE || citizen.phone || citizen.phone_number || '';
                    var address = citizen.STREET_ARB || citizen.street_ar || citizen.address_ar || citizen.address || '';
                    
                    if (fullName) {
                        var nameInput = $('input[name="name"]');
                        nameInput.val(fullName);
                        // Make name field readonly after successful fetch
                        nameInput.attr('readonly', true);
                        nameInput.css({
                            'background-color': '#f3f6f9',
                            'cursor': 'not-allowed',
                            'color': '#7b7b7b'
                        });
                        nameInput.attr('title', '{{ __('تم جلب هذا الاسم من النظام') }}');
                    }
                    if (mobile) {
                        $('input[name="mobile"]').val(mobile);
                    }
                    if (address) {
                        $('textarea[name="address"]').val(address);
                    }
                    
                    $('#manual-input').show();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('تم') }}',
                            text: '{{ __('تم جلب البيانات بنجاح') }}',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    $('#manual-input').show();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('تنبيه') }}',
                            text: '{{ __('لم يتم العثور على بيانات لهذا الرقم. يمكنك إدخال البيانات يدوياً') }}',
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                isFetchingData = false;
                inputGroup.find('.spinner').remove();
                inputGroup.append('<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; pointer-events: none;"><i class="fas fa-id-card text-muted" style="font-size: 14px;"></i></span>');
                
                console.error('API Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                $('#manual-input').show();
                
                var errorMessage = '{{ __('لم يتم جلب البيانات من النظام. يمكنك إدخال البيانات يدوياً') }}';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __('تنبيه') }}',
                        text: errorMessage,
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }
        });
    }

    $('#license_number').off('blur').on('blur', function() {
        if ($(this).val().trim()) {
            $('#manual-input').show();
        }
    });

    // Form submission is handled by initializeMokalafFormScripts in index.blade.php
    // No need to handle form submission here - it's handled in the parent page
});
</script>
