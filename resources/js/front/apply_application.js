$(function() {
    $(document).ready(function () {
        var apllyErrorDiv = $('#error-apply');
        $('button.nav-application').on('click', function () {
            let lastFormId = 'rzs';
            let currentBtn = $(this);
            if (currentBtn.hasClass('apply-application')) {
                //validate current form
                let formToValidate = $('#' + currentBtn.data('validate'));
                if (typeof formToValidate != 'undefined') {
                    //ugly way to fix '<p><br><p>' on empty summernote
                    let requestTextarea = $('#request_summernote');
                    let requestValue = requestTextarea.summernote('isEmpty') ? '' : requestTextarea.summernote('code');
                    $('#request').val(requestValue); //using this input to validate with jquery
                    formToValidate.validate({
                        ignore: ':hidden:not(.do-not-ignore)',
                        errorClass: 'is_invalid',
                        rules: formRules(currentBtn.data('validate')),
                        errorPlacement: function (error, element) {
                            if( element.attr("name") != 'files[]' ) {
                                $("#error-" + element.attr("name")).html(error);
                            } else{
                                error.insertAfter(element);
                            }
                        },
                        invalidHandler: function (e, validation) {
                            console.log("invalidHandler : event", e);
                            console.log("invalidHandler : validation", validation);
                        }
                    });
                    $('.disabled-item').prop('disabled', false);
                    if (formToValidate.valid()) {
                        if (currentBtn.data('validate') === lastFormId) {
                            submitApplication();
                        } else {
                            $('.disabled-item').prop('disabled', true);
                            applicationNavigate(currentBtn);
                        }
                    } else {
                        $('.disabled-item').prop('disabled', true);
                    }
                }
            } else {
                //navigate
                applicationNavigate(currentBtn);
            }
        });

        function submitApplication() {
            apllyErrorDiv.html('');
            //disable other button actions
            $('.disable-on-send').prop('disabled', true);
            let applayModal = new MyModal({
                destroyListener: true,
                dismissible: false,
                body: '<p>Изпълнява се, моля изчакайте процеса да приключи!</p>'
            });

            let navBtns = $('.nav-application');
            navBtns.prop('disable', true);
            //merge forms
            let formData = new FormData($('#info')[0]); //use this to catch file inputs
            //merge two forms
            let rzsForm = $('#rzs').serializeArray();
            for (let i = 0; i < rzsForm.length; i++) {
                formData.append(rzsForm[i].name, rzsForm[i].value);
            }
            $.ajax({
                url: $('#applicationUrl').val(),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data);
                    if (typeof data.errors != 'undefined') {
                        apllyErrorDiv.html(data.errors);
                        applayModal.hideModal(applayModal.id);
                    } else {
                        if (typeof data.applicationsInfo != 'undefined' && data.applicationsInfo.length > 0) {
                            $('div#apply').html(data.html);
                            $('form').addClass('d-none');
                            $('.form-legend').addClass('d-none');
                            $('div#apply').removeClass('d-none');
                            activateTab('apply');
                        } else {
                            apllyErrorDiv.html('Нещо се обърка по врене на запис, заявлението не е завършено.');
                            applayModal.hideModal(applayModal.id);
                        }
                    }
                    //enable actions
                    $('.disable-on-send').prop('disabled', false);
                    applayModal.hideModal(applayModal.id);
                },
                error: function () {
                    apllyErrorDiv.html('Системна грешка, презаредете и опитайте отново.');
                    //enable actions
                    $('.disable-on-send').prop('disabled', false);
                    applayModal.modalObj.hide();
                }
            });
            navBtns.prop('disable', false);
            $('.disabled-item').prop('disabled', true);
            $('.disable-on-send').prop('disabled', false);
        }

        function applicationNavigate(currentBtn) {
            currentBtn.parent().addClass('d-none');
            let toSectionId = typeof currentBtn.data('prev') != 'undefined' ? currentBtn.data('prev') : currentBtn.data('next');
            $('form#' + toSectionId).removeClass('d-none');
            //tabs
            activateTab(toSectionId);
        }

        function activateTab(toSectionId) {
            let tabNavigationItems = $('a.application-navigation-tab');
            let tabToActivate = $('a.application-navigation-tab#tab-' + toSectionId);
            tabNavigationItems.removeClass('active');
            tabNavigationItems.removeClass('disabled');
            tabNavigationItems.addClass('disabled');
            //activate only current tab
            tabToActivate.addClass('active');
            tabToActivate.removeClass('disabled');
        }

        function formRules(formId) {
            let rules = {
                info: {
                    'files[]': {
                        myextension: allowed_file_extensions,
                        myfilesize: max_upload_file_size
                    },
                    legal_form: {
                        required: true,
                        number: true
                    },
                    names: {
                        required: true,
                        maxlength: 255 //alphaspace
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    phone: {
                        maxlength: 50
                    },
                    country: {
                        required: true,
                        number: true
                    },
                    area: {
                        required: true,
                        number: true
                    },
                    municipality: {
                        required: true,
                        number: true
                    },
                    settlement: {
                        required: true,
                        number: true
                    },
                    post_code: {
                        maxlength: 10
                    },
                    address: {
                        required: true,
                        maxlength: 255
                    },
                    address_second: {
                        maxlength: 255,
                    },
                    delivery_method: {
                        required: true,
                        number: true
                    },
                    request: {
                        required: true
                    },
                    email_publication: {
                        number: true
                    },
                    names_publication: {
                        number: true
                    },
                    address_publication: {
                        number: true
                    },
                    phone_publication: {
                        number: true
                    }
                },
                rzs: {
                    'subjects[]': {
                        required: true
                    }
                }
            }
            return rules[formId];
        }

    });
});
