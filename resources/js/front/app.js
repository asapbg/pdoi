function formatBytes(bytes, decimals = 2) {
    if (!+bytes) return '0 Bytes'

    const k = 1024
    const dm = decimals < 0 ? 0 : decimals
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
}

//===============================
// START MyModal
// Create modal and show it with option for load body from url or pass direct content
// available params:
// title, body (content), destroyListener (boolean : do destroy modal on close), bodyLoadUrl (url for loading body content)
//===============================

function MyModal(obj){
    var _myModal = Object.create(MyModal.prototype)
    _myModal.id = (new Date()).getTime();
    _myModal.dismissible = typeof obj.dismissible != 'undefined' ? obj.dismissible : true;
    _myModal.title = typeof obj.title != 'undefined' ? obj.title : '';
    _myModal.body = typeof obj.body != 'undefined' ? obj.body : '';
    _myModal.bodyLoadUrl = typeof obj.bodyLoadUrl != 'undefined' ? obj.bodyLoadUrl : null;
    _myModal.destroyListener = typeof obj.destroyListener != 'undefined' ? obj.destroyListener : false;
    _myModal.modalObj = _myModal.init(_myModal);
    if( _myModal.destroyListener ) {
        _myModal.setDestroyListener(_myModal);
    }
    if( _myModal.bodyLoadUrl ) {
        _myModal.loadModalBody(_myModal)
    } else {
        _myModal.showModal(_myModal);
    }
    return _myModal;
}

MyModal.prototype.init = function (_myModal) {
    let modalHtml = '<div id="' + _myModal.id + '" class="modal fade myModal" role="dialog" style="display: none">\n' +
        '  <div class="modal-dialog">\n' +
        '    <!-- Modal content-->\n' +
        '    <div class="modal-content">\n' +
        '      <div class="modal-header">\n' +
        '        <h4 class="modal-title">' + _myModal.title + '</h4>\n' +
        (_myModal.dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' : '') +
        '      </div>\n' +
        '      <div class="modal-body" id="' + _myModal.id + '-body' + '">\n' + _myModal.body +
        '      </div>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div>';
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    return  new bootstrap.Modal(document.getElementById(_myModal.id), {
        keyboard: false,
        backdrop: 'static'
    })
}

MyModal.prototype.showModal = function (_myModal){
    _myModal.modalObj.show();
}

MyModal.prototype.hideModal = function (id){
    $('#' + id).remove();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('overflow','visible');
}

MyModal.prototype.setDestroyListener = function (_myModal){
    $('#' + _myModal.id).on('hidden.bs.modal', function(){
        _myModal.modalObj.dispose();
        $('#' + _myModal.id).remove();
    });
}

MyModal.prototype.loadModalBody = function (_myModal) {
    $('#' + _myModal.id + '-body').load(_myModal.bodyLoadUrl, function (){
        _myModal.showModal(_myModal);
    });
}

//==========================
// End MyModal
//==========================

function setCookie(name, value){
    vo_ajax = true;
    $.ajax({
        url: "/set-cookie?name="+ name +"&value="+ value,
        type: 'GET',
        success: function() {
            vo_ajax = false;
        }
    });
}
//==========================
// Start Blind options
//==========================

function resetVisualOptions() {
    vo_ajax = true;
    $.ajax({
        url: "/reset-visual-options",
        type: 'GET',
        success: function() {
            location.reload(true);
        }
    });
}

function setDomElFontSize(newSize, ignoreOriginalSize){
    if( !ignoreOriginalSize || (newSize != 100 ) ) {
        $("div, span, p, a, i, h1, h2, h3, h4, h5").css({ "font-size": newSize + "%" });
        $(".select2 span").css({ "font-size": 100 + "%" });
        $('.vo-reset').removeClass('d-none');
        vo_font_percent = newSize;
        setCookie('vo_font_percent', vo_font_percent);
    }
}

function changeFontSize(increase) {
    let percentStep = 5;
    let newSize = vo_font_percent;
    if (increase) {
        newSize = vo_font_percent <= 200 ? (vo_font_percent + percentStep) : vo_font_percent;
    } else {
        newSize = vo_font_percent >= 50 ? (vo_font_percent - percentStep) : vo_font_percent;
    }

    if( newSize != vo_font_percent ) {
        setDomElFontSize(newSize);
    }
}
//==========================
// End Blind options
//==========================

$(function() {
    $(document).ready(function() {
        $.datepicker.regional = {
            bg: {
                days: ["Неделя", "Понеделник", "Вторник", "Сряда", "Четвъртък", "Петък", "Събота", "Неделя"],
                daysShort: ["Нед", "Пон", "Вт", "Ср", "Чет", "Пет", "Съб", "Нед"],
                daysMin: ["Н", "П", "В", "С", "Ч", "П", "С", "н"],
                months: ["Януари","Февруари","Март","Април","Май","Юни","Юли","Август","Септември","Октомври","Ноември","Декември"],
                monthsShort: ["Ян","Фев","Мар","Апр","Май","Юн","Юл","Авг","Сеп","Окт","Ное","Дек"],
                today: "Днес",
                clear: "Изчисти"
            },

            en: {
                days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
                months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                today: "Today",
                clear: "Clear"
            }

        };

        $.datepicker.setDefaults( $.datepicker.regional[typeof GlobalLang != 'undefined' ? GlobalLang : ''] );

        //blind options
        $('#visual-option-div').on("click", '#vo-close', function() {
            $('#vo-option-btn').click();
        });

        $('li.visual-option').on('click', function (){
            if($(this).hasClass('vo-contrast') && !vo_ajax ) {
               $('body').toggleClass('high-contrast');
                vo_high_contrast = $('body').hasClass('high-contrast') ? 1 : 0;
                if( vo_high_contrast ) {
                    $('.vo-reset').removeClass('d-none');
                    $('#vo-contrast .low').removeClass('d-none');
                    $('#vo-contrast .height').addClass('d-none');
                } else{
                    $('#vo-contrast .height').removeClass('d-none');
                    $('#vo-contrast .low').addClass('d-none');
                }

                setCookie('vo_high_contrast', vo_high_contrast);
            }
            if($(this).hasClass('vo-increase-text') && !vo_ajax ) {
                changeFontSize(true);
            }
            if($(this).hasClass('vo-decrease-text') && !vo_ajax ) {
                changeFontSize(false);
            }
            if($(this).hasClass('vo-reset') && !vo_ajax ) {
                resetVisualOptions();
            }
        });
        //menu
        $('.dropdown-toggle-arrow').on('click', function (e){
            e.preventDefault();
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        if($('.summernote').length) {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['view', ['fullscreen']]
                ]
            });
        }

        if($('.datepicker').length) {
            $('.datepicker').datepicker({
                language: typeof GlobalLang != 'undefined' ? GlobalLang : 'en',
                format: 'dd.mm.yyyy',
                todayHighlight: true,
                orientation: "bottom left",
                autoclose: true,
                weekStart: 1,
                changeMonth: true,
                changeYear: true,
            });
        }

        //Custom file jquery inline validation for dynamical added files
        if($.validator) {
            $.validator.addMethod('myfilesize', function(value, element, param) {
                let _this = this;
                let isValid = true;
                if($(element).hasClass('file-validate')) {
                    $('.file-validate').each(function (index, el){
                        var length = ( el.files.length );
                        var fileSize = 0;
                        if (length > 0) {
                            for (var i = 0; i < length; i++) {
                                fileSize = el.files[i].size; // get file size
                                if( !(fileSize <= param) ) {
                                    $($('.file-error')[index]).html('Максималният размер на файла трябва да е '+ formatBytes(param));//$.validator.messages.myfilesize
                                    isValid = false;
                                }
                            }
                        }
                    });
                }
                return isValid;
            }, '');

            $.validator.addMethod('myextension', function(value, element, param) {
                let _this = this;
                let isValid = true;
                if($(element).hasClass('file-validate')) {
                    $('.file-validate').each(function (index, el){
                        var length = ( el.files.length );
                        if (length > 0) {
                            for (var i = 0; i < length; i++) {
                                if( !(el.files[i].name).match(new RegExp(".(" + param + ")$", "i")) ) {
                                    console.log(param);
                                    $($('.file-error')[index]).html('Разрешените файлови формати са '+ param);//$.validator.messages.myfilesize
                                    isValid = false;
                                }
                            }
                        }
                    });
                }
                return isValid;
            }, '');
        }

        //========================================================
        //Control identity fields depending on selected legal form
        //======================================================
        $(document).on('click', 'input.identity', function (){
            let identityId = $(this).val();
            $('div.identity').hide();
            $('#identity_'+identityId).show();
        });

        //======================================
        // START PDOI SUBJECTS SELECT FROM MODAL
        // use <select name="subjects[]" id="subjects" class="select2">
        // and button with class 'pick-subject' and data-url attribute to call modal with the tree
        //you can add to url get parameters to set tree as selectable or just view : select=1
        // and if you need more than one subject to be selected use parameter 'multiple=1'
        //======================================
        if( $('.pick-subject').length ) {
            $('.pick-subject').on('click', function (){
                let subjectModal = new MyModal({
                    title: $(this).data('title'),
                    destroyListener: true,
                    bodyLoadUrl: $(this).data('url')
                });

                $(document).on('click', '#select-subject', function (){
                    let subjectsFormSelect = $('#subjects');
                    let checked = $('#'+ subjectModal.id +' input[name="subjects-item"]:checked');
                    if( checked.length ) {
                        if( checked.length === 1 ) {
                            subjectsFormSelect.val(checked.val());
                        } else if( checked.length > 1 ) {
                            let subjectValues = [];
                            checked.each(function(){
                                subjectValues.push($(this).val());
                            });
                            subjectsFormSelect.val(subjectValues);
                        }
                        subjectsFormSelect.trigger('change');
                    }
                    subjectModal.modalObj.hide();
                });
            });
        }

        // ==========================
        // START Profile form validation
        //==========================
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
                invalidHandler: function(e, validation){
                    console.log("invalidHandler : event", e);
                    console.log("invalidHandler : validation", validation);
                }
            });
        }
        // ==========================
        // END Profile form validation
        //==========================


        // ==========================
        // START Upload file and add in table section
        //==========================
        if( $('#tmpFile').length ) {
            let uploadInput = $('#tmpFile');
            let fileListId = $('#' + uploadInput.data('container'));
            uploadInput.on('change', function(){
                if( (uploadInput.val()).length > 0 ) {
                    //validate file
                    // uploadInput.valid();
                    if( true ) {//file is validated
                        let fileNumber = $('.file-row').length + 1;
                        let fileName = (uploadInput.val()).replace(/.*(\/|\\)/, '');
                        //add file row
                        fileListId.find('tbody').append('<tr class="file-row" id="file-row-'+ fileNumber +'" style="vertical-align: middle;">\n' +
                            '                            <td></td>\n' +
                            '                            <td><span class="filename"></span>'+ fileName +'<span class="file-error d-block text-danger"></span></td>\n' +
                            '                            <td>\n' +
                            '                                <input type="text" name="file_description[]" class="form-control form-control-sm" value="">\n' +
                            '                            </td>\n' +
                            '                            <td>\n' +
                            // '                                <i class="fa-solid fa-download text-primary me-1" data-file="'+ fileNumber +'" role="button"></i>\n' +
                            '                                <i class="fa-solid fa-circle-xmark text-warning me-1 remove-file" data-file="'+ fileNumber +'" role="button" data-bs-toggle="tooltip" title="Премахни"></i>\n' +
                            // '                                <i class="fa-solid fa-trash text-danger me-1" data-file="'+ fileNumber +'" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __(\'front.remove_btn\') }}"></i>\n' +
                            '                            </td>\n' +
                            '                        </tr>');

                        //clone input
                        let newFileInput = uploadInput.clone(true);
                        newFileInput.attr('name', 'files[]');
                        newFileInput.addClass('file-validate');
                        newFileInput.removeAttr('id');
                        $('#file-row-'+ fileNumber + ' td span.filename').html(newFileInput);
                        //clear tmp input
                        uploadInput.val('');
                    }
                }
            });

            $(document).on('click', '.remove-file', function(){
                $('#file-row-'+ $(this).data('file')).remove();
            });
        }
        // ==========================
        // END Upload file and add in table section
        //==========================

        //==================================
        // START Contact page subject info
        //==================================
        $('#subjectContact').on('change', function (){
            if( parseInt($(this).val()) > 0 ){
                $('#subjectContactResult').load($(this).data('url') + '?s=' + $(this).val());
            } else {
                $('#subjectContactResult').html('');
            }

        });
        // ==================================
        // START Contact page subject info
        //==================================

    });
});

