$(function() {
    var pdoiTreeUrl = '/get-pdoi-subjects';

    $(document).ready(function() {
        $('[data-bs-toggle=tooltip]').tooltip();

        $(document).on('click', 'input.identity', function (){
            let identityId = $(this).val();
            $('div.identity').hide();
            $('#identity_'+identityId).show();
        });

        if($('.summernote').length) {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']], //, 'picture', 'video'
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        if($('.select2').length) {
            $('.select2').select2({
                allowClear: true,
                placeholder: true,
                // language: "bg"
            });
        }

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
            });
        }

        $(document).on('click', '#select-subject', function (){
            let subjectsFormSelect = $('#subjects');
            let checked = $('#pdoiSubjectsTree input[name="subjects-item"]:checked');
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
            $(this).closest('.modal').remove();
            $('.modal-backdrop.show').remove();
        });


        //===============================
        // START MyModal
        // Create modal and show it with option for load body from url or pass direct content
        // available params:
        // title, body (content), destroyListener (boolean : do destroy modal on close), bodyLoadUrl (url for loading body content)
        //===============================

        function MyModal(obj){
            var _myModal = Object.create(MyModal.prototype)
            _myModal.id = (new Date()).getTime();
            _myModal.title = typeof obj.title != 'undefined' ? obj.title : '';
            _myModal.body = typeof obj.body != 'undefined' ? obj.body : '';
            _myModal.bodyLoadUrl = typeof obj.bodyLoadUrl != 'undefined' ? obj.bodyLoadUrl : null;
            _myModal.destroyListener = typeof obj.destroy != 'undefined' ? obj.destroy : false;
            _myModal.init(_myModal);
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
                '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' +
                '      </div>\n' +
                '      <div class="modal-body" id="' + _myModal.id + '-body' + '">\n' + _myModal.body +
                '      </div>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</div>';
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        MyModal.prototype.showModal = function (_myModal){
            $('#' + _myModal.id).modal('show');
        }

        MyModal.prototype.setDestroyListener = function (_myModal){
            $(document).on('hidden.bs.modal', '#' + _myModal.id, function (e) {
                console.log('ok');
                $('#' + _myModal.id).remove();
                $('.modal-backdrop.show').remove();
            })
        }

        MyModal.prototype.loadModalBody = function (_myModal) {
            $('#' + _myModal.id + '-body').load(_myModal.bodyLoadUrl, function (){
                _myModal.showModal(_myModal);
            });
        }

        //==========================
        // End MyModal
        //==========================
    });
});

