<script>
    window.initMokalafSelect2 = function(config) {
        var prefix = config.prefix || '';
        var formId = config.formId || (prefix + 'kt_form_2');
        var readOnlyMainSelect = config.readOnlyMainSelect !== undefined ? config.readOnlyMainSelect : true;
        var enableQueryPrefill = !!config.enableQueryPrefill;

        if (typeof $ === 'undefined' || !$.fn.select2) {
            return;
        }

        var isUpdating = false;

        function resetLookupSelect($select) {
            if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                $select.off('.mokalafLookup');
                $select.select2('destroy');
            }
        }

        function lookupDropdownParent() {
            var $modal = $('#' + prefix + 'kt_select2_111').closest('.modal');
            return $modal.length ? $modal : $(document.body);
        }

        function fillMokalafData(mokalaf) {
            if (isUpdating) {
                return;
            }
            isUpdating = true;

            var $name = $('#' + prefix + 'mokalaf_name');
            var $address = $('#' + prefix + 'address');
            var $mobile = $('#' + prefix + 'mobile');
            var $jebaia = $('#' + prefix + 'jebaia_no');
            var $mainSelect = $('#' + prefix + 'kt_select2_3');
            var $identitySelect = $('#' + prefix + 'kt_select2_111');
            var $nameSelect = $('#' + prefix + 'kt_select2_222');
            var $mokalafIdSearch = $('#' + prefix + 'kt_select2_333');

            var nameValue = mokalaf.name || '';
            $name.val(nameValue);

            if ($address.length) {
                var addressValue = mokalaf.address || '';
                $address.val(addressValue);
            }

            if ($mobile.length) {
                var mobileValue = mokalaf.mobile || '';
                $mobile.val(mobileValue);
            }

            if ($jebaia.length) {
                $jebaia.val(mokalaf.jebaia_no || mokalaf.license_number || '');
            }

            var readonlyStyle = {
                'background-color': '#f3f6f9',
                'color': '#7b7b7b',
                'cursor': 'not-allowed'
            };

            if ($address.length) {
                $address.attr('disabled', 'disabled').css(readonlyStyle);
            }
            if ($mobile.length) {
                $mobile.attr('readonly', 'readonly').css(readonlyStyle);
            }

            var identityValue = mokalaf.identity_number || mokalaf.license_number || '';

            if ($identitySelect.length) {
                if (identityValue) {
                    $identitySelect.empty();
                    var opt1 = new Option(identityValue + ' - ' + mokalaf.name, mokalaf.id, true, true);
                    $identitySelect.append(opt1).val(mokalaf.id).trigger('change.select2');
                }
            }

            if ($nameSelect.length) {
                $nameSelect.empty();
                if (mokalaf.name) {
                    var opt2 = new Option(mokalaf.name, mokalaf.id, true, true);
                    $nameSelect.append(opt2).val(mokalaf.id).trigger('change.select2');
                }
            }

            if ($mainSelect.length) {
                $mainSelect.empty();
                var opt3 = new Option(mokalaf.id, mokalaf.id, true, true);
                $mainSelect.append(opt3).val(mokalaf.id).trigger('change.select2');

                if (readOnlyMainSelect) {
                    $mainSelect.css({
                        'pointer-events': 'none',
                        'opacity': '0.6'
                    });
                }

                var hiddenId = prefix + 'mokalaf_id_hidden';
                if ($('#' + hiddenId).length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: hiddenId,
                        name: 'mokalaf_id',
                        value: mokalaf.id
                    }).appendTo('#' + formId);
                } else {
                    $('#' + hiddenId).val(mokalaf.id);
                }
            }

            if ($mokalafIdSearch.length) {
                $mokalafIdSearch.empty();
                var opt4 = new Option(mokalaf.id, mokalaf.id, true, true);
                $mokalafIdSearch.append(opt4).val(mokalaf.id).trigger('change.select2');
            }

            setTimeout(function() {
                isUpdating = false;
            }, 300);
        }

        function loadMokalafById(mokalafId) {
            if (!mokalafId) return;
            $.ajax({
                type: 'get',
                url: "{{ route('requests.get-mokalaf-data') }}",
                data: {
                    mokalaf_id: mokalafId
                },
                success: function(response) {
                    if (response.success && response.mokalaf) {
                        fillMokalafData(response.mokalaf);
                    } else {
                        console.error('Failed to load mokalaf data:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading mokalaf data:', status, error);
                    alert('{{ __('حدث خطأ أثناء تحميل بيانات المكلف. يرجى المحاولة مرة أخرى.') }}');
                }
            });
        }

        var lang = {
            noResults: function() {
                return "{{ __('لا توجد نتائج') }}";
            },
            searching: function() {
                return "{{ __('جاري البحث...') }}";
            }
        };

        var $identitySelect = $('#' + prefix + 'kt_select2_111');
        if ($identitySelect.length) {
            resetLookupSelect($identitySelect);
            $identitySelect.select2({
                placeholder: "{{ __('ابحث في الهوية | المعرف | مشتغل مرخص') }}",
                allowClear: true,
                language: lang,
                dropdownParent: lookupDropdownParent(),
                ajax: {
                    url: "{{ route('requests.autocomplete-identity') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        // تحويل البيانات إلى صيغة Select2 المطلوبة
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text || (item.identifier + ' - ' + item.name),
                                    mokalaf: item
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            }).on('select2:select.mokalafLookup', function(e) {
                if (isUpdating) return;
                // استخدام بيانات المكلف من result
                var mokalaf = e.params.data.mokalaf || e.params.data;
                if (mokalaf && typeof fillMokalafData === 'function') {
                    fillMokalafData(mokalaf);
                } else {
                    loadMokalafById(e.params.data.id);
                }
            });
        }

        var $nameSelect = $('#' + prefix + 'kt_select2_222');
        if ($nameSelect.length) {
            resetLookupSelect($nameSelect);
            $nameSelect.select2({
                placeholder: "{{ __('ابحث في الاسم') }}",
                allowClear: true,
                language: lang,
                dropdownParent: lookupDropdownParent(),
                ajax: {
                    url: "{{ route('requests.autocomplete-name') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            }).on('select2:select.mokalafLookup', function(e) {
                if (isUpdating) return;
                loadMokalafById(e.params.data.id);
            });
        }

        var $mokalafIdSearch = $('#' + prefix + 'kt_select2_333');
        if ($mokalafIdSearch.length) {
            resetLookupSelect($mokalafIdSearch);
            $mokalafIdSearch.select2({
                placeholder: "{{ __('ابحث في المكلفين') }}",
                allowClear: true,
                language: lang,
                dropdownParent: lookupDropdownParent(),
                ajax: {
                    url: "{{ route('requests.autocomplete-mokalaf') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 1
            }).on('select2:select.mokalafLookup', function(e) {
                if (isUpdating) return;
                loadMokalafById(e.params.data.id);
            });
        }

        var $mainSelect = $('#' + prefix + 'kt_select2_3');
        if ($mainSelect.length) {
            resetLookupSelect($mainSelect);
            $mainSelect.select2({
                placeholder: "{{ __('اختر المكلف') }}",
                allowClear: true,
                language: lang,
                dropdownParent: lookupDropdownParent(),
                ajax: {
                    url: "{{ route('requests.autocomplete-mokalaf') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.id
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 1
            }).on('select2:select.mokalafLookup', function(e) {
                if (isUpdating) return;
                loadMokalafById(e.params.data.id);
            }).on('select2:clear.mokalafLookup', function() {
                $('#' + prefix + 'mokalaf_name').val('');
                if ($('#' + prefix + 'address').length) {
                    $('#' + prefix + 'address').val('').removeAttr('disabled').css({
                        'background-color': '',
                        'color': '',
                        'cursor': ''
                    });
                }
                if ($('#' + prefix + 'mobile').length) {
                    $('#' + prefix + 'mobile').val('').removeAttr('readonly').css({
                        'background-color': '',
                        'color': '',
                        'cursor': ''
                    });
                }
                $('#' + prefix + 'mokalaf_id_hidden').remove();
                if (readOnlyMainSelect) {
                    $mainSelect.css({
                        'pointer-events': 'none',
                        'opacity': '0.6'
                    });
                }
            });

            if (readOnlyMainSelect) {
                $mainSelect.prop('disabled', true).css({
                    'pointer-events': 'none',
                    'opacity': '0.6'
                });
            }
        }

        if (enableQueryPrefill) {
            try {
                var params = new URLSearchParams(window.location.search);
                var mokalafId = params.get('mokalaf_id');
                var identityNumber = params.get('identity_number');
                var name = params.get('name');
                var address = params.get('address');
                var mobile = params.get('mobile');

                if (!mokalafId) return;
                var idNum = parseInt(mokalafId, 10);
                if (isNaN(idNum)) return;

                if (name) name = decodeURIComponent(name);
                if (address) address = decodeURIComponent(address);

                var mokalaf = {
                    id: idNum,
                    identity_number: identityNumber || '',
                    name: name || '',
                    address: address || '',
                    mobile: mobile || ''
                };

                setTimeout(function() {
                    fillMokalafData(mokalaf);
                }, 350);
            } catch (e) {}
        }
    };
</script>
