$(document).ready(function (){
    if( $('#profile-form').length ) {
        $('#profile-form').validate({
            errorClass: 'is_invalid',
            errorPlacement: function (error, element) {
                $("#error-" + element.attr("name")).html(error);
            },
            rules : {
                legal_form : {
                    required: true,
                    number: true
                },
                names : {
                    required: true,
                    maxlength: 255 //alphaspace
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 255
                },
                phone : {
                    maxlength: 50
                },
                country : {
                    required: true,
                    number: true
                },
                area : {
                    required: true,
                    number: true
                },
                municipality : {
                    required: true,
                    number: true
                },
                settlement : {
                    required: true,
                    number: true
                },
                post_code : {
                    maxlength: 10
                },
                address : {
                    required: true,
                    maxlength: 255
                },
                address_second : {
                    maxlength: 255,
                },
                delivery_method : {
                    required: true,
                    number: true
                },
            },
            // errorPlacement: function (error, element) {
            //     $("#error-" + element.attr("name")).html(error);
            // },
            invalidHandler: function(e, validation){
                console.log("invalidHandler : event", e);
                console.log("invalidHandler : validation", validation);
            }
        });
    }

    $('button.nav-application').on('click', function (){
        let lastFormId = 'rzs';
        let currentBtn = $(this);
        if( currentBtn.hasClass('apply-application') ) {
            //validate current form
            let formToValidate = $('#' + currentBtn.data('validate'));
            if( typeof formToValidate != 'undefined') {
                //ugly way to fix '<p><br><p>' on empty summernote
                let requestTextarea = $('#request_summernote');
                let requestValue = requestTextarea.summernote('isEmpty')? '' : requestTextarea.summernote('code');
                $('#request').val(requestValue); //using this input to validate with jquery

                formToValidate.validate({
                    ignore: ':hidden:not(.do-not-ignore)',
                    errorClass: 'is_invalid',
                    rules : formRules(currentBtn.data('validate')),
                    errorPlacement: function (error, element) {
                        $("#error-" + element.attr("name")).html(error);
                    },
                    // invalidHandler: function(e, validation){
                    //     console.log("invalidHandler : event", e);
                    //     console.log("invalidHandler : validation", validation);
                    // }
                });
                $('.disabled-item').prop('disabled', false);
                if (formToValidate.valid()) {
                    if( currentBtn.data('validate') === lastFormId ) {
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

    function submitApplication(){
        let navBtns = $('.nav-application');
        navBtns.prop('disable', true);
        $.ajax({
            url  : $('#applicationUrl').val(),
            type : 'POST',
            data : $('#info, #rzs').serialize(),
            success : function(data) {
                if( typeof data.errors != 'undefined' ) {
                    console.log(data.errors)
                } else {
                    if (typeof data.applicationInfo != 'undefined' ) {
                        $('form').addClass('d-none');
                        $('.form-legend').addClass('d-none');
                        $('div#apply').removeClass('d-none');
                        activateTab('apply');
                        console.log(data.applicationInfo);
                    } else {
                        console.log('something wrong');
                    }
                }
            },
            error : function() {
                console.log('system error');
            }
        });
        navBtns.prop('disable', false);
        $('.disabled-item').prop('disabled', true);
    }

    function applicationNavigate(currentBtn){
        currentBtn.parent().addClass('d-none');
        let toSectionId = typeof currentBtn.data('prev') != 'undefined' ? currentBtn.data('prev') : currentBtn.data('next');
        $('form#' + toSectionId).removeClass('d-none');
        //tabs
        activateTab(toSectionId);
    }

    function activateTab(toSectionId){
        let tabNavigationItems = $('a.application-navigation-tab');
        let tabToActivate = $('a.application-navigation-tab#tab-' + toSectionId);
        tabNavigationItems.removeClass('active');
        tabNavigationItems.removeClass('disabled');
        tabNavigationItems.addClass('disabled');
        //activate only current tab
        tabToActivate.addClass('active');
        tabToActivate.removeClass('disabled');
    }

    function formRules(formId){
        let rules = {
            info: {
                legal_form : {
                    required: true,
                    number: true
                },
                names : {
                    required: true,
                    maxlength: 255 //alphaspace
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 255
                },
                phone : {
                    maxlength: 50
                },
                country : {
                    required: true,
                    number: true
                },
                area : {
                    required: true,
                    number: true
                },
                municipality : {
                    required: true,
                    number: true
                },
                settlement : {
                    required: true,
                    number: true
                },
                post_code : {
                    maxlength: 10
                },
                address : {
                    required: true,
                    maxlength: 255
                },
                address_second : {
                    maxlength: 255,
                },
                delivery_method : {
                    required: true,
                    number: true
                },
                request : {
                    required: true
                },
                email_publication : {
                    number: true
                },
                names_publication : {
                    number: true
                },
                address_publication : {
                    number: true
                },
                phone_publication : {
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

