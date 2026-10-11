/**
 * Unified Notification System
 * Provides consistent success/error notifications using Toastr
 */

// Configure Toastr defaults
toastr.options = {
    closeButton: true,
    progressBar: true,
    timeOut: 3000,
    positionClass: 'toast-top-center',
    preventDuplicates: true,
    newestOnTop: true,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

/**
 * Show success notification
 * @param {string} message - The message to display
 * @param {string} title - Optional title
 * @param {object} options - Additional toastr options
 */
function showSuccess(message, title = null, options = {}) {
    const defaultTitle = title || __('Success');
    toastr.success(message, defaultTitle, {
        ...toastr.options,
        ...options
    });
}

/**
 * Show error notification
 * @param {string} message - The error message
 * @param {string} title - Optional title
 * @param {object} options - Additional toastr options
 */
function showError(message, title = null, options = {}) {
    const defaultTitle = title || __('Error');
    toastr.error(message, defaultTitle, {
        ...toastr.options,
        timeOut: 5000, // Errors stay longer
        ...options
    });
}

/**
 * Show warning notification
 * @param {string} message - The warning message
 * @param {string} title - Optional title
 * @param {object} options - Additional toastr options
 */
function showWarning(message, title = null, options = {}) {
    const defaultTitle = title || __('Warning');
    toastr.warning(message, defaultTitle, {
        ...toastr.options,
        ...options
    });
}

/**
 * Show info notification
 * @param {string} message - The info message
 * @param {string} title - Optional title
 * @param {object} options - Additional toastr options
 */
function showInfo(message, title = null, options = {}) {
    const defaultTitle = title || __('Info');
    toastr.info(message, defaultTitle, {
        ...toastr.options,
        ...options
    });
}

/**
 * Clear all notifications
 */
function clearNotifications() {
    toastr.clear();
}

/**
 * Handle AJAX response with automatic notifications
 * @param {object} response - The AJAX response
 * @param {string} successMessage - Custom success message
 * @param {string} errorMessage - Custom error message
 */
function handleResponse(response, successMessage = null, errorMessage = null) {
    if (response.success) {
        showSuccess(successMessage || response.message || __('Operation completed successfully'));
        return true;
    } else {
        showError(errorMessage || response.message || __('An error occurred'));
        return false;
    }
}

/**
 * Handle form submission with loading state and notifications
 * @param {jQuery} form - The form element
 * @param {string} successMessage - Success message
 * @param {string} errorMessage - Error message
 */
function handleFormSubmit(form, successMessage = null, errorMessage = null) {
    const $form = $(form);
    const $button = $form.find('button[type="submit"]');
    const originalText = $button.html();
    
    // Show loading state
    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span> جاري الحفظ...');
    
    $.ajax({
        url: $form.attr('action'),
        method: $form.attr('method'),
        data: $form.serialize(),
        success: function(response) {
            if (handleResponse(response, successMessage, errorMessage)) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else if (response.reload) {
                    window.location.reload();
                } else {
                    $form[0].reset();
                }
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || xhr.statusText || __('An error occurred');
            showError(message, errorMessage);
        },
        complete: function() {
            // Reset button state
            $button.prop('disabled', false).html(originalText);
        }
    });
}

// Make functions globally available
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.clearNotifications = clearNotifications;
window.handleResponse = handleResponse;
window.handleFormSubmit = handleFormSubmit;
