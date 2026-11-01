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

        // ==================== اختيار الموظف المسؤول ====================
        var prefix = $root.data('field-prefix') || '';
        var $workflowSteps = $root.find('.js-req-edit-workflow-steps');
        var existingAssignees = $workflowSteps.data('assignees') || {};

        function loadEditWorkflowSteps(requestTypeId) {
            if (!$workflowSteps.length) return;
            if (!requestTypeId) {
                $workflowSteps.text('اختر نوع الطلب لعرض مساره');
                return;
            }

            $workflowSteps.html('<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> جارٍ تحميل المسار</span>');
            $.get("{{ route('requests.request-type-workflows') }}", { request_type_id: requestTypeId })
                .done(function (response) {
                    if (!response.workflows.length) {
                        $workflowSteps.html('<div class="alert alert-warning mb-0">لا توجد خطوات مفعّلة لهذا النوع.</div>');
                        return;
                    }

                    var html = '<div class="list-group">';
                    response.workflows.forEach(function (step) {
                        var selectedEmployeeId = existingAssignees[step.id] || step.default_employee_id || '';
                        html += '<div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">';
                        html += '<div class="mb-2"><span class="badge badge-primary">' + step.step_order + '</span> <strong>' + $('<div>').text(step.name).html() + '</strong><div class="small text-muted">' + $('<div>').text(step.unit).html() + '</div></div>';
                        html += '<div style="min-width:260px"><label class="small mb-1">الموظف المسؤول</label><select class="form-control form-control-sm" name="workflow_employee_ids[' + step.id + ']">';
                        html += '<option value="">اختر الموظف</option>';
                        var localEmployees = step.employees.filter(function (employee) { return employee.in_unit; });
                        var otherEmployees = step.employees.filter(function (employee) { return !employee.in_unit; });
                        localEmployees.forEach(function (employee) {
                            html += '<option value="' + employee.id + '" ' + (String(employee.id) === String(selectedEmployeeId) ? 'selected' : '') + '>' + $('<div>').text(employee.name).html() + '</option>';
                        });
                        if (otherEmployees.length) {
                            html += '<optgroup label="──────── موظفون خارج الجهة المعنية ────────">';
                            otherEmployees.forEach(function (employee) {
                                html += '<option value="' + employee.id + '" ' + (String(employee.id) === String(selectedEmployeeId) ? 'selected' : '') + '>' + $('<div>').text(employee.name + ' — ' + employee.unit).html() + '</option>';
                            });
                            html += '</optgroup>';
                        }
                        var selectedName = step.employees.find(function (employee) { return String(employee.id) === String(selectedEmployeeId); });
                        html += '</select><div class="small mt-1 text-primary workflow-assignee-name">' + (selectedName ? 'الموظف المختار: ' + $('<div>').text(selectedName.name).html() : 'لم يتم اختيار موظف بعد') + '</div>';
                        if (otherEmployees.length) html += '<small class="text-warning d-block">تنبيه: الموظفون بعد الفاصل خارج القسم أو الدائرة المعنية.</small>';
                        if (!step.employees.length) html += '<small class="text-danger">لا يوجد موظفون نشطون مرتبطون بهذه الجهة.</small>';
                        html += '</div></div>';
                    });

                    $workflowSteps.html(html + '</div>');
                    $workflowSteps.find('select[name^="workflow_employee_ids"]').on('change', function () {
                        var selectedName = $(this).find('option:selected').text();
                        $(this).closest('div').find('.workflow-assignee-name').text(this.value ? 'الموظف المختار: ' + selectedName : 'لم يتم اختيار موظف بعد');
                    });
                })
                .fail(function () {
                    $workflowSteps.html('<div class="alert alert-danger mb-0">تعذر تحميل مسار الطلب.</div>');
                });
        }

        $root.find('select[name="request_type_id"]').off('change.reqEditWorkflow').on('change.reqEditWorkflow', function () {
            loadEditWorkflowSteps($(this).val());
        });
        loadEditWorkflowSteps($root.find('select[name="request_type_id"]').val());

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
