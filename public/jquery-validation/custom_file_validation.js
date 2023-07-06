$(function() {
    $(document).ready(function () {
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
                                    $($('.file-error')[index]).html('Максималният размер на файла трябва да е '+ formatBytes(param));
                                    isValid = true;
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
    });
});
