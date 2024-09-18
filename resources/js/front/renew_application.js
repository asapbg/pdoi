$(function() {
    $(document).ready(function () {
        var apllyErrorDiv = $('#error-apply');
        $('button.renew-application').on('click', function () {
            let currentBtn = $(this);
                //validate current form
                let formToValidate = $('#' + currentBtn.data('validate'));
                if (typeof formToValidate != 'undefined') {
                    formToValidate.validate({
                        ignore: ':hidden:not(.do-not-ignore)',
                        errorClass: 'is_invalid',
                        rules: renewFormRules(currentBtn.data('validate')),
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
                    if (formToValidate.valid()) {
                        let extraValidation = true;
                        if($('.file-row').length == 0){
                            extraValidation = false;
                            apllyErrorDiv.html('Прилагането на съдебно решение е задължително');
                        }

                        if($('.file-row').length != 0){
                            $('.file-row').each(function(){
                                let row = $(this);
                                if($(row).find('input[name="file_description[]"]').val().trim().length == 0){
                                    extraValidation = false;
                                    apllyErrorDiv.html('Въведете описание за всички приложени файлове');
                                }
                            });
                        }
                        if(extraValidation){
                            submitRenewApplication();
                        }
                    }
                }
        });

        function submitRenewApplication() {
            apllyErrorDiv.html('');
            //disable other button actions
            let applayModal = new MyModal({
                destroyListener: true,
                dismissible: false,
                body: '<p>Изпълнява се, моля изчакайте процеса да приключи!</p>'
            });

            let formData = new FormData($('#renew_form')[0]); //use this to catch file inputs
            $.ajax({
                url: $('#applicationUrl').val(),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    // console.log(data);
                    if (typeof data.errors != 'undefined') {
                        apllyErrorDiv.html(data.errors);
                        applayModal.hideModal(applayModal.id);
                    } else {
                        if (typeof data.redirect_url != 'undefined') {
                            window.location.href = data.redirect_url;
                        } else {
                            apllyErrorDiv.html('Нещо се обърка по врене на запис, заявлението не е завършено.');
                            applayModal.hideModal(applayModal.id);
                        }
                    }
                    applayModal.hideModal(applayModal.id);
                },
                error: function () {
                    apllyErrorDiv.html('Системна грешка, презаредете и опитайте отново.');
                    applayModal.modalObj.hide();
                }
            });
        }

        function renewFormRules(formId) {
            let rules = {
                renew_form: {
                    'files[]': {
                        myextension: allowed_file_extensions,
                        myfilesize: max_upload_file_size
                    }
                }
            }
            return rules[formId];
        }

    });
});
