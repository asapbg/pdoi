$(function() {
    $(document).ready(function () {
        // ==========================
        // START Upload file and add in table section
        // use in admin and public
        //==========================
        if ($('#tmpFile').length) {
            let uploadInput = $('#tmpFile');
            let fileListId = $('#' + uploadInput.data('container'));
            let inAdministration = typeof uploadInput.data('admin') != 'undefined' ? parseInt(uploadInput.data('admin')) : 0;
            uploadInput.on('change', function () {
                if ((uploadInput.val()).length > 0) {
                    //validate file
                    if (true) {//file is validated
                        let fileNumber = $('.file-row').length + 1;
                        let fileName = (uploadInput.val()).replace(/.*(\/|\\)/, '');
                        //add file row
                        fileListId.find('tbody').append('<tr class="file-row" id="file-row-' + fileNumber + '" style="vertical-align: middle;">\n' +
                            '                            <td></td>\n' +
                            '                            <td><span class="filename"></span>' + fileName + '</td>\n' +
                            '                            <td>\n' +
                            '                                <input type="text" name="file_description[]" class="form-control form-control-sm" value="">\n' +
                            '                            </td>\n' +
                            '                            <td>\n' +
                            // '                                <i class="fa-solid fa-download text-primary me-1" data-file="'+ fileNumber +'" role="button"></i>\n' +
                            '                                <i class="'+ (inAdministration ? 'fas fa-times-circle ' : 'fa-solid fa-circle-xmark ') +'text-warning me-1 remove-file" data-file="' + fileNumber + '" role="button" data-bs-toggle="tooltip" title="Премахни"></i>\n' +
                            // '                                <i class="fa-solid fa-trash text-danger me-1" data-file="'+ fileNumber +'" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __(\'front.remove_btn\') }}"></i>\n' +
                            '                            </td>\n' +
                            '                        </tr>');

                        //clone input
                        let newFileInput = uploadInput.clone(true);
                        newFileInput.attr('name', 'files[]');
                        newFileInput.removeAttr('id');
                        $('#file-row-' + fileNumber + ' td span.filename').html(newFileInput);
                        //clear tmp input
                        uploadInput.val('');
                    }
                }
            });

            $(document).on('click', '.remove-file', function () {
                $('#file-row-' + $(this).data('file')).remove();
            });
        }
        // ==========================
        // END Upload file and add in table section
        //==========================
    });
});
