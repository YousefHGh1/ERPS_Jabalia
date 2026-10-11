<script>
    window.initRequestFormEnhancements = function(config) {
        var prefix = config.prefix || '';
        var formId = config.formId || (prefix + 'kt_form_2');
        var draftKey = config.draftKey || ('request_form_draft_' + (prefix || 'page'));
        var $form = $('#' + formId);

        if (!$form.length) {
            return;
        }

        var $attachments = $('#' + prefix + 'request_attachments');
        if ($attachments.length && !$('#' + prefix + 'request_attachments_preview').length) {
            var $preview = $('<div>', {
                id: prefix + 'request_attachments_preview',
                class: 'mt-3'
            });
            $attachments.after($preview);

            $attachments.on('change.requestEnhancements', function() {
                var files = Array.prototype.slice.call(this.files || []);
                $preview.empty();

                files.forEach(function(file) {
                    $('<span>', {
                        class: 'badge badge-light-primary mr-2 mb-2',
                        text: file.name
                    }).appendTo($preview);
                });
            });
        }

        function draftFields() {
            return $form.find('input, textarea, select')
                .not('[type="file"], [type="hidden"], [name="_token"], [name="_method"]');
        }

        function readDraft() {
            try {
                return JSON.parse(localStorage.getItem(draftKey) || '{}');
            } catch (e) {
                return {};
            }
        }

        function writeDraft() {
            var data = {};
            draftFields().each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                if (!name) return;

                if ($field.attr('type') === 'checkbox') {
                    data[name] = $field.is(':checked') ? '1' : '';
                    return;
                }

                data[name] = $field.val();
            });

            localStorage.setItem(draftKey, JSON.stringify(data));
        }

        function restoreDraft() {
            var data = readDraft();
            Object.keys(data).forEach(function(name) {
                var $field = $form.find('[name="' + name + '"]').first();
                if (!$field.length || $field.val()) return;

                if ($field.attr('type') === 'checkbox') {
                    $field.prop('checked', data[name] === '1').trigger('change');
                    return;
                }

                $field.val(data[name]).trigger('change.select2');
            });
        }

        restoreDraft();
        $form.off('.requestDraft').on('input.requestDraft change.requestDraft', 'input, textarea, select', writeDraft);
        $form.off('submit.requestDraft').on('submit.requestDraft', function() {
            localStorage.removeItem(draftKey);
        });
    };
</script>
