<script>
(function ($) {
    var cancelPaymentBase = @json(rtrim(url('/request-payments'), '/'));

    $(document).on('change', '.js-req-edit-join', function () {
        $(this).closest('form').find('.js-req-edit-common-wrap').toggleClass('d-none', !$(this).is(':checked'));
    });

    $(document).on('change', '.request-edit-root select[name="mokalaf_id"]', function () {
        var txt = $(this).find('option:selected').text().trim();
        $(this).closest('form').find('.js-req-edit-mokalaf-name').val(txt);
    });

    $(document).on('show.bs.modal', '.req-nested-submodal', function () {
        var $m = $(this);
        if ($('#editRequestModal').hasClass('show')) {
            if (!$m.parent().is('body')) {
                $m.appendTo('body');
            }
            var z = parseInt($('#editRequestModal').css('z-index'), 10) || 1050;
            $m.css('z-index', z + 25);
        }
    });

    $(document).on('hidden.bs.modal', '.req-nested-submodal', function () {
        $(this).css('z-index', '');
    });

    /**
     * ربط إرسال الدفعات وحذف المرفقات وإلغاء الدفعة داخل حاوية (صفحة التعديل أو مودال الفهرس).
     */
    window.bindRequestEditInteractions = function ($root) {
        if (!$root || !$root.length) {
            return;
        }

        $root.find('.js-req-add-payment-form').off('submit.repay').on('submit.repay', function (e) {
            e.preventDefault();
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                success: function (response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'تمت إضافة الدفعة');
                        }
                        if (typeof window.reloadEditRequestModal === 'function') {
                            window.reloadEditRequestModal();
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'حدث خطأ أثناء حفظ الدفعة';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        $root.find('.js-req-cancel-payment-form').off('submit.recan').on('submit.recan', function (e) {
            e.preventDefault();
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                success: function (response) {
                    if (response.success) {
                        $form.closest('.modal').modal('hide');
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'تم إلغاء الدفعة');
                        }
                        if (typeof window.reloadEditRequestModal === 'function') {
                            window.reloadEditRequestModal();
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'حدث خطأ';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        $root.find('.js-req-open-cancel-payment').off('click.recopen').on('click.recopen', function () {
            var paymentId = $(this).data('payment-id');
            var action = cancelPaymentBase + '/' + paymentId + '/cancel';
            var prefix = $(this).closest('.request-edit-root').data('field-prefix');
            if (prefix === undefined || prefix === null) {
                prefix = '';
            }
            var modalSel = '#' + prefix + 'cancelRequestPaymentModal';
            $(modalSel).find('.js-req-cancel-payment-form').attr('action', action);
            $(modalSel).modal('show');
        });

        $root.find('.js-req-delete-attachment').off('click.rea').on('click.rea', function (e) {
            e.preventDefault();
            var attachmentId = $(this).data('id');
            var requestId = $(this).data('request-id');
            if (!confirm('{{ __('هل أنت متأكد من حذف هذا المرفق؟') }}')) {
                return;
            }
            var delUrl = '{{ route("requests.delete-attachment", [":request", ":attachment"]) }}'
                .replace(':request', requestId)
                .replace(':attachment', attachmentId);
            $.ajax({
                url: delUrl,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    if (typeof window.reloadEditRequestModal === 'function') {
                        window.reloadEditRequestModal();
                    } else {
                        window.location.reload();
                    }
                },
                error: function () {
                    alert('{{ __('حدث خطأ أثناء حذف المرفق') }}');
                }
            });
        });
    };
})(jQuery);
</script>
