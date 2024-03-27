$(function() {
    $(document).ready(function () {
        //=================================
        //use in admin and public
        //===============================

        function select2OptionFilter(option) {
            if (typeof option.element != 'undefined' && option.element.className === 'd-none' ) {
                return false
            }
            return option.text;
        }

        var select2Options = {
            allowClear: true,
            placeholder: true,
            templateResult: select2OptionFilter,
            language: "bg"
        };
        if($('.select2').length) {
            $('.select2').select2(select2Options);
        }

        //=================================
        //START CONNECTED EKATTE SELECTS
        //===============================
        var areaSelect = $('#area-select');
        var municipalitySelect = $('#municipality-select');
        var settlementSelect = $('#settlement-select');

        function updateSelect2Options(select2El, dataToCompare, valueToCompare) {
            select2El.find('option').addClass('d-none');
            select2El.find('option[data-' + dataToCompare + '="' + valueToCompare + '"]').removeClass('d-none');
            select2El.val('');
            select2El.select2(select2Options);
        }

        function resetSelect2Options(select2El) {
            select2El.find('option').removeClass('d-none');
            select2El.val('');
            select2El.select2(select2Options);
        }

        //area-select
        if (areaSelect.length) {
            areaSelect.on('change', function () {
                if (areaSelect.val() === '') {
                    if (municipalitySelect.length) {
                        resetSelect2Options(municipalitySelect);
                    }
                    if (settlementSelect.length) {
                        resetSelect2Options(settlementSelect);
                    }
                } else {
                    if (municipalitySelect.length) {
                        updateSelect2Options(municipalitySelect, 'area', areaSelect.find(':selected').data('code'));
                    }
                    if (settlementSelect.length) {
                        updateSelect2Options(settlementSelect, 'area', areaSelect.find(':selected').data('code'));
                    }
                }
            });
        }

        //municipality-select
        if (municipalitySelect.length) {
            municipalitySelect.on('change', function () {
                if (settlementSelect.length) {
                    if (municipalitySelect.val() === '') {
                        if (areaSelect.length) {
                            if (areaSelect.val() === '') {
                                resetSelect2Options(settlementSelect);
                            } else {
                                updateSelect2Options(settlementSelect, 'area', areaSelect.find(':selected').data('code'));
                            }
                        }
                    } else {
                        updateSelect2Options(settlementSelect, 'full', municipalitySelect.find(':selected').data('area') + municipalitySelect.find(':selected').data('code'));
                    }
                }
            });
        }
        //=================================
        //END CONNECTED EKATTE SELECTS
        //===============================
    });
});
