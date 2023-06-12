toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

function WebSocketPrinter(options) {
    var defaults = {
        url: "ws://127.0.0.1:12212/Citizen CL-S621",
        onConnect: function () {
        },
        onDisconnect: function () {
        },
        onUpdate: function () {
        },
    };

    var settings = Object.assign({}, defaults, options);
    var websocket;
    var connected = false;

    var onMessage = function (evt) {
        settings.onUpdate(evt.data);
    };

    var onConnect = function () {
        connected = true;
        settings.onConnect();
    };

    var onDisconnect = function () {
        connected = false;
        settings.onDisconnect();
        reconnect();
    };

    var connect = function () {
        websocket = new WebSocket(settings.url);
        websocket.onopen = onConnect;
        websocket.onclose = onDisconnect;
        websocket.onmessage = onMessage;
    };

    var reconnect = function () {
        connect();
    };

    this.submit = function (data) {
        if (Array.isArray(data)) {
            data.forEach(function (element) {
                websocket.send(JSON.stringify(element));
            });
        } else {
            console.log(JSON.stringify(data));
            websocket.send(JSON.stringify(data));
        }
    };

    this.isConnected = function () {
        return connected;
    };

    connect();
}

function populateModal(data, modal) {
    //console.log(data);return false;

    Object.keys(data).forEach(function (field, index) {
        let fieldName = field + '-plch';

        let $fields = modal.find('.' + fieldName);
        if ($fields && $fields.length) {
            Object.keys($fields).forEach(function (fieldIndex) {

                if (!Number.isInteger(Number.parseInt(fieldIndex))) return;

                let singleField = $fields[fieldIndex];
                let tag = singleField.localName;
                //console.log(singleField, tag, singleField.type, data[field]);return false;
                if (tag == 'select') {
                    if (typeof data[field] === "object") {
                        (data[field]).forEach(function (option) {
                            //console.log(singleField, newOption); return false;
                            $(singleField).find('option[value=' + option.id + ']').prop('selected', true).trigger('change');
                        })
                    } else {
                        $(singleField).find('option[value=' + data[field] + ']').prop('selected', true).trigger('change');
                    }
                } else if (tag == 'input') {
                    if (singleField.type == "checkbox") {
                        $(singleField).prop('checked', data[field]);
                    } else {
                        $(singleField).val(data[field]);
                    }
                } else if (tag == 'textarea') {
                    $(singleField).val(data[field]);
                } else if (tag == 'span' || tag == 'div') {
                    $(singleField).html(data[field]);
                }

                //console.log(fieldIndex, singleField, singleField.localName);
            })

        }
    })
}

function isEmpty(arg){
    return (
        arg == null || // Check for null or undefined
        arg.length === 0 || // Check for empty String (Bonus check for empty Array)
        (typeof arg === 'object' && Object.keys(arg).length === 0) // Check for empty Object or Array
    );
}

function ToggleBoolean(booleanType, entityId) {
    let form = "#" + booleanType + "_form_" + entityId;
    let model = $(form + " .model").val();
    let status = $(form + " .status").attr('data-status');
    showModalConfirm();
    console.log(model, status);
    $.ajax({
        type: 'GET',
        url: '/toggle-boolean',
        data: {entityId: entityId, model: model, booleanType: booleanType, status: status},
        success: function (res) {
            if (status == 1) {
                $(form + " .status").removeClass('bg-red').addClass('bg-green').attr('data-status', 0).html('Да');
            } else {
                $(form + " .status").removeClass('bg-green').addClass('bg-red').attr('data-status', 1).html('Не');
            }
            if (booleanType == "active") {
                $(form).closest('tr').remove();
            }
        },
        error: function () {
            // $periodUl.find('li').remove();
        }
    });
}

function TogglePermission(permission, entityId) {
    let form = "#" + permission + "_form_" + entityId;
    let model = $(form + " .model").val();
    let status = $(form + " .status").attr('data-status');
    //console.log(status);
    $.ajax({
        type: 'GET',
        url: '/toggle-permissions',
        data: {entityId: entityId, model: model, permission: permission, status: status},
        success: function (res) {
            if (status == 1) {
                $(form + " .status").removeClass('bg-red').addClass('bg-green').attr('data-status', 0).html('Да');
            } else {
                $(form + " .status").removeClass('bg-green').addClass('bg-red').attr('data-status', 1).html('Не');
            }
        },
        error: function () {
            // $periodUl.find('li').remove();
        }
    });
}

function ToggleCheckboxes(el,class_name) {
    if($(el).hasClass('checked')) {
        $(el).removeClass('checked');
        $('.'+class_name).prop('checked', false);
    }
    else {
        $(el).addClass('checked');
        $('.'+class_name).prop('checked', true);
    }
}

function SelectCheckboxes(action,class_name) {
    if(action == 'uncheck') {
        $('.'+class_name).prop('checked', false);
    }
    else {
        $('.'+class_name).prop('checked', true);
    }
}

// Handling Cookies
function createCookie(name,value,hours) {
    var expires = "";
    if (hours) {
        var date = new Date();
        date.setTime(date.getTime()+(hours*60*60*1000));
        expires = "; expires="+date.toGMTString();
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-3);
}

function showModalAlert(message,title = false) {
    if (title) {
        $("#modal-alert .modal-title").html(title);
    }
    $("#modal-alert .modal-body").html(message);
    $("#modal-alert").modal('show');
}

function showModalConfirm(url,message,title = false) {
    if (title) {
        $("#modal-confirm .modal-title").html(title);
    }
    $("#modal-confirm .modal-body p").html(message);
    $("#modal-confirm form").attr('action', url);
    $("#modal-confirm").modal('show');
}

function ConfirmToggleBoolean(booleanType, entityId, message, title = false) {
    if (title) {
        $("#modal-confirm .modal-title").html(title);
    }
    $("#modal-confirm .modal-body p").html(message);
    $("#modal-confirm button.btn-success").attr('onclick', "ToggleBoolean('"+booleanType+"','"+entityId+"')");
    $("#modal-confirm button.btn-success").attr('data-dismiss', "modal");
    $("#modal-confirm").modal('show');
}

$.fn.appendAttr = function(attrName, suffix) {
    this.attr(attrName, function(i, val) {
        return val + suffix;
    });
    return this;
};

$(document).on("select2:open", () => {
    document.querySelector(".select2-container--open .select2-search__field").focus()
})

$(document).ready(function (e) {

    $('.table-bordered.striped tbody tr:even').not($('.dataTable tbody tr, .table-with-toggle-content tr')).addClass('odd');

    if ($('.dataTable').length) {
        $('.dataTable').DataTable({
            "paging": false,
            "order": [1, 'asc'],
            "language": {
                url: '/js/dataTables.bulgarian.json'
            }
        });
    }

    $('.toggle').on('click', function (e) {
        e.preventDefault();
        let class_id = $(this).data('id');
        let toggle_class = $(this).data('class');
        let icon = $(this).find('i.fas');
        $(this).closest('tr').addClass('opened');
        if ($(icon).hasClass('fa-plus-circle')) {
            $(icon).removeClass('fa-plus-circle').addClass('fa-minus-circle');
        } else {
            $(this).closest('tr').removeClass('opened');
            $(icon).removeClass('fa-minus-circle').addClass('fa-plus-circle');
        }
        $('.'+toggle_class+'_'+class_id).toggle();
    });

    $('.show_confirm').on('click', function () {
        showModalConfirm($(this).data('url'), $(this).data('message'))
    });

    if ($('#must_change_password').length) {
        $('#must_change_password').click(function () {
            if ($(this).is(':checked')) {
                $(".passwords").attr('readonly', true);
            } else {
                $(".passwords").attr('readonly', false);
            }
        })
    }

    if ($('.form-group.disabled').length) {
        $('.form-group.disabled').each(function () {
            $(this).find('.btn-danger').remove();
            $(this).find('select, input').attr('disabled', true);
        })
    }

    if($('input[type="text"]').length) {
        $('input[type="text"]').attr('autocomplete', 'off');
    }

    if($('.summernote').length) {
        $('.summernote').summernote({
            height: 80,
            fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New'],
            styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5'],
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style','bold', 'italic', 'underline', 'clear']],
                ['font', ['superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen']],
                ['table', ['table']],
                ['insert', ['hr']]
            ]
        });
    }

    $('.navbar .sidebar-toggle').bind('click', function() {
        let body = $("body");
        if(body.hasClass("sidebar-collapse")) {
            eraseCookie('nav');
        }
        else {
            createCookie('nav','sidebar-collapse',2);
        }
    });

    if ($('.js-toggle-delete-resource-modal').length) {
        $('.js-toggle-delete-resource-modal').on('click', function(e) {
            e.preventDefault();

            // If delete url specify in del.btn use that url
            if($(this).data('resource-delete-url')) {
                $( $(this).data('target')).find('form').attr('action', $(this).data('resource-delete-url'));
            }

            $($(this).data('target')).find('span.resource-name').html($(this).data('resource-name'));
            $($(this).data('target')).find('#resource_id').attr('value', $(this).data('resource-id'));

            $($(this).data('target')).modal('toggle');
        })
    }

    if($('.select2').length) {
        $('.select2').select2({
            allowClear: true,
            placeholder: true,
            language: "bg"
        });
    }

    if ($('#investigation_year').length) {
        $('#investigation_year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            endDate: new Date(),
            todayHighlight: true,
            orientation: "bottom left",
            autoclose: true
        });
    }

    if($('.datepicker').length) {
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            orientation: "bottom left",
            autoclose: true
        });
    }

    if($('.datepicker_end_date').length) {
        $('.datepicker_end_date').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            orientation: "bottom left",
            endDate: new Date(),
            autoclose: true
        });
    }

    let start_date = (isEmpty($(".start_date").val())) ? moment().subtract(6, 'days').format('YYYY-MM-DD') : $(".start_date").val();
    let end_date = (isEmpty($(".end_date").val())) ? moment().format('YYYY-MM-DD') : $(".end_date").val();
    $(".start_date").val(start_date);
    $(".end_date").val(end_date);
    //console.log(start_date, end_date);

    $('.date_range').daterangepicker({
        ranges   : {
            'Днес'              : [moment(), moment()],
            'Вчера'             : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Последните 7 дена' : [moment().subtract(6, 'days'), moment()],
            'Последните 30 дена': [moment().subtract(29, 'days'), moment()],
            'Този месец'        : [moment().startOf('month'), moment().endOf('month')],
            'Миналият месец'    : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: start_date,
        endDate: end_date,
        alwaysShowCalendars: true,
        locale: {
            customRangeLabel: 'Персонализиран',
            applyLabel: 'Запази',
            cancelLabel: 'Откажи',
            format: 'YYYY-MM-DD'
        }
    }, function (start, end) {
        $(".start_date").val(start.format('YYYY-MM-DD'));
        $(".end_date").val(end.format('YYYY-MM-DD'));
    });

    if($('.simple-datatable').length) {
        $('.simple-datatable').DataTable({
            paging: false,
            // searching: false
        });
    }

    // allow only latin letters, disable paste so no possible cyrillic letters
    $(".latin_letters").on("keypress", function (event) {
        var englishAlphabetAndWhiteSpace = /^[-@./#&+\w\s\\]*$/;
        var key = String.fromCharCode(event.which);
        if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetAndWhiteSpace.test(key)) {
            return true;
        }
        return false;
    });
    $('.latin_letters').on("paste", function (e) {
        e.preventDefault();
    });

    $('[data-toggle="tooltip"]').tooltip();


    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.modal').modal('hide');
        }
    });

    $('.js-toggle-role-permission').change(function () {
        let token = $('[name="_token"]').val(),
            data = {
                'role': $(this).data('role'),
                'permission': $(this).data('permission'),
                'full_access': $(this).data('full'),
                'main': $(this).data('main'),
                'group': $(this).data('group'),
                'has': $(this).prop('checked') ? 1 : 0,
                '_token': token
            },
            url = $(this).data('url');
        console.log(data, url);
        $(this).prop('disabled', true);
        $.post(url, data)
            .then(res => {
                console.log(res);
                if (res.success) {
                    if(res.reload) {
                        location.reload();
                    }
                    toastr.success('Ролята е променена', 'Правата върху ролята са успешно променени');
                } else if (res.error) {
                    toastr.error('Грешка', 'Възникна грешка, моля опитайте по-късно');
                }
                $(this).prop('disabled', false);
            }).catch(function (err) {
            console.error(err);
            toastr.error('Грешка', 'Възникна грешка, моля опитайте по-късно');
            $(this).prop('disabled', false);
        })
    });

    $('input.user_permission').on('change', function (){
        if( parseInt($(this).data('full')) === 1 && $(this).is(':checked') ) {
            $("input.user_permission[data-full='0']").prop('checked', false);
        } else if( parseInt($(this).data('main')) === 1 && $(this).is(':checked') ) {
            $("input.user_permission[data-full='1']").prop('checked', false);
            $("input.user_permission[data-group='"+ $(this).data('group') +"'][data-main='0']").prop('checked', false);
        } else{
            $("input.user_permission[data-full='1']").prop('checked', false);
            $("input.user_permission[data-group='"+ $(this).data('group') +"'][data-main='1']").prop('checked', false);
        }
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
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body" id="' + _myModal.id + '-body' + '">\n' + _myModal.body +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>';
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        return  new bootstrap.Modal(document.getElementById(_myModal.id), {
            keyboard: false
        })
    }

    MyModal.prototype.showModal = function (_myModal){
        _myModal.modalObj.show();
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

})
