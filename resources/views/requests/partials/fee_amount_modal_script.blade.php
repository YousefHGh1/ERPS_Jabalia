<script>
(function ($) {
    function summaryText(prefix) {
        var $h = $('#' + prefix + 'request_fee_amount_hidden');
        var $s = $('#' + prefix + 'request_fee_amount_summary');
        if (!$s.length) return;
        var emptyLabel = $s.data('empty-label') || '—';
        var v = parseFloat(String($h.val()).replace(',', '.'));
        if (!isNaN(v) && v > 0) {
            $s.text(Number(v).toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₪');
        } else {
            $s.text(emptyLabel);
        }
    }

    function syncAllSummaries() {
        $('input[id$="request_fee_amount_hidden"]').each(function () {
            var id = $(this).attr('id') || '';
            var prefix = id.replace(/request_fee_amount_hidden$/, '');
            summaryText(prefix);
        });
    }

    $(document).on('show.bs.modal', '.fee-amount-modal', function () {
        var $m = $(this);
        var modalId = $m.attr('id');
        var p = $m.data('fee-prefix') || '';
        var cur = $('#' + p + 'request_fee_amount_hidden').val();
        $('#' + modalId + '_fee_input').val(cur === '' || cur === null ? '' : cur);

        // مودال مفتوح آخر (مثل إضافة الطلب): انقل مودال الرسم إلى body وارفع z-index فوقه — بدون backdrop ثانٍ (data-backdrop="false").
        var $otherShown = $('.modal.show').not($m);
        if ($otherShown.length) {
            if (!$m.parent().is('body')) {
                $m.appendTo('body');
            }
            var baseZ = parseInt($otherShown.first().css('z-index'), 10) || 1050;
            $m.css('z-index', baseZ + 20);
        } else {
            $m.css('z-index', '');
        }
    });

    $(document).on('hidden.bs.modal', '.fee-amount-modal', function () {
        $(this).css('z-index', '');
    });

    $(document).on('click', '.fee-amount-modal [id$="_fee_apply"]', function () {
        var $m = $(this).closest('.fee-amount-modal');
        var modalId = $m.attr('id');
        var p = $m.data('fee-prefix') || '';
        var raw = $('#' + modalId + '_fee_input').val();
        if (raw === '' || raw === null || String(raw).trim() === '') {
            $('#' + p + 'request_fee_amount_hidden').val('');
        } else {
            var num = parseFloat(String(raw).replace(',', '.'));
            if (isNaN(num) || num < 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: '{{ __("تنبيه") }}', text: '{{ __("أدخل مبلغاً صالحاً (رقم ≥ 0).") }}' });
                } else {
                    alert('{{ __("أدخل مبلغاً صالحاً.") }}');
                }
                return;
            }
            if (num === 0) {
                $('#' + p + 'request_fee_amount_hidden').val('');
            } else {
                $('#' + p + 'request_fee_amount_hidden').val(String(num));
            }
        }
        summaryText(p);
        $m.modal('hide');
    });

    $(document).on('click', '.fee-amount-modal [id$="_fee_clear"]', function () {
        var $m = $(this).closest('.fee-amount-modal');
        var modalId = $m.attr('id');
        var p = $m.data('fee-prefix') || '';
        $('#' + modalId + '_fee_input').val('');
        $('#' + p + 'request_fee_amount_hidden').val('');
        summaryText(p);
        $m.modal('hide');
    });

    $(document).ready(function () {
        syncAllSummaries();
    });

    window.syncFeeAmountSummaries = syncAllSummaries;
})(jQuery);
</script>
