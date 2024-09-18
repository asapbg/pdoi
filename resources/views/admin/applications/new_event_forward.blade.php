@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="in-platform-tab" data-toggle="pill" href="#in-platform" role="tab" aria-controls="in-platform" aria-selected="false">Препращане към ЗС от платформата</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="out-platform-tab" data-toggle="pill" href="#out-platform" role="tab" aria-controls="out-platform" aria-selected="false">Препращане извън платформата</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade active show" id="in-platform" role="tabpanel" aria-labelledby="in-platform-tab">
                            <form action="{{ route('admin.application.event.new.store') }}" method="post" name="form" id="formInPlatform" enctype="multipart/form-data" data-rule="in">
                                @csrf
                                <input type="hidden" name="event" value="{{ $event->id }}">
                                <input type="hidden" name="application" value="{{ $application->id }}">
                                <input type="hidden" name="old_subject" value="{{ $application->response_subject_id }}">
                                <input type="hidden" name="in_platform" value="1" >
                                <div class="row mb-4">
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >Запитване:</label>
                                        <span>{!! html_entity_decode($application->request) !!}</span>
                                    </div>
                                    <h5 class="bg-primary py-1 px-2">{{ __('custom.data_for_event') }} {{ $event->name }}</h5>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.old_pdoi_subject') }}:</label>
                                        <span>{{ $application->responseSubject ? $application->responseSubject->subject_name : '---' }}</span>
                                    </div>
                                    <div class="col-md-9 col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.new_pdoi_subject') }}: <span class="required">*</span></label>
                                        <div class="col-12 d-flex flex-row">
                                            <div class="input-group">
                                                <select required class="form-control form-control-sm select2" name="new_resp_subject_id" id="subjects">
                                                    <option value="">---</option>
                                                    @if(isset($subjects) && sizeof($subjects))
                                                        @foreach($subjects as $option)
                                                            <option value="{{ $option['value'] }}" @if($option['value'] == old('new_resp_subject_id', '')) selected @endif>{{ $option['name'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject rounded"
                                                    data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                                    data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&&admin=1' }}">
                                                <i class="fas fa-list"></i>
                                            </button>
                                        </div>
                                        <span class="text-danger" id="error-new_resp_subject_id"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.mail_templates.placeholders.to_name') }}:</label>
                                        <input type="text" class="form-control form-control-sm" name="to_name" value="{{ old('to_name', '') }}" id="to_name">
                                        <span class="text-danger" id="error-to_name"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.user_request') }}: <span class="required">*</span></label>
                                        <textarea class="col-12 form-control summernote-custom-clone">@if(!empty(old('subject_user_request', ''))){!! old('subject_user_request') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="subject_user_request" value="@if(!empty(old('subject_user_request', ''))){!! old('subject_user_request') !!}@endif" id="subject_user_request">
                                        <span class="text-danger" id="error-subject_user_request"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{!! __('custom.comment_to_new_subject') !!}:</label>
                                        <textarea class="col-12 form-control summernote-standard-clone">@if(!empty(old('add_text', ''))){!! old('add_text') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="add_text" value="@if(!empty(old('add_text', ''))){!! old('add_text') !!}@endif" id="add_text">
                                        <span class="text-danger" id="error-add_text"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{!! __('custom.request_to_current_subject') !!}:</label>
                                        <textarea class="col-12 form-control summernote-standard-clone">@if(!empty(old('current_subject_user_request', ''))){!! old('current_subject_user_request') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="current_subject_user_request" value="@if(!empty(old('current_subject_user_request', ''))){!! old('current_subject_user_request') !!}@endif" id="current_subject_user_request">
                                        <span class="text-danger" id="error-current_subject_user_request"></span>
                                    </div>
                                    @if($event->files)
                                        <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                                        @error('files')
                                        <span class="d-block text-danger">{{ $message }}</span>
                                        @enderror
                                        <p class="text-info fw-bold">Максимален размер на файл: {{ displayBytes(config('filesystems.max_upload_file_size')) }}
                                            <br>Максимален брой файлове: {{ config('filesystems.max_file_uploads') }}
                                            <br>Разрешени формати: {{ implode(',', \App\Models\File::ALLOWED_FILE_EXTENSIONS) }}</p>
                                        <table class="table table-light table-sm table-bordered mb-4" id="attachFiles">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{ __('front.file_name') }}</th>
                                                <th>{{ __('front.description') }}</th>
                                                <th>
                                                    <div>
                                                        <label for="tmpFile" class="form-label p-0 m-0">
                                                            <i class="fas fa-upload text-primary p-1" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __('front.upload_btn') }}"></i>
                                                        </label>
                                                        <input class="form-control d-none do-not-ignore" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles" data-admin="1" data-check="1">
                                                    </div>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="save" type="button" class="save btn btn-success" data-form="formInPlatform">{{ __('custom.apply') }}</button>
                                        <a href="{{ route('admin.application.view', ['item' => $application->id]) }}"
                                           class="btn btn-primary">{{ __('custom.back') }}</a>
                                    </div>
                                </div>
                                <br/>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="out-platform" role="tabpanel" aria-labelledby="out-platform-tab">
                            <form action="{{ route('admin.application.event.new.store') }}" method="post" name="form" id="formOutPlatform" enctype="multipart/form-data"  data-rule="out">
                                @csrf
                                <input type="hidden" name="event" value="{{ $event->id }}">
                                <input type="hidden" name="application" value="{{ $application->id }}">
                                <input type="hidden" name="old_subject" value="{{ $application->response_subject_id }}">
                                <input type="hidden" name="in_platform" value="0" >
                                <div class="row mb-4">
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >Запитване:</label>
                                        <span>{!! html_entity_decode($application->request) !!}</span>
                                    </div>
                                    <h5 class="bg-primary py-1 px-2">{{ __('custom.data_for_event') }} {{ $event->name }}</h5>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.old_pdoi_subject') }}:</label>
                                        <span>{{ $application->responseSubject ? $application->responseSubject->subject_name : '---' }}</span>
                                    </div>
                                    <div class="col-md-3 col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.new_pdoi_subject') }} ({{ __('validation.attributes.eik') }}): <span class="required">*</span></label>
                                        <input type="text" name="new_resp_subject_eik" value="{{ old('new_resp_subject_eik', '') }}" class="form-control form-control-sm @error('new_resp_subject_eik') is-invalid @endif">
                                        <span class="text-danger" id="error-new_resp_subject_eik"></span>
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.new_pdoi_subject') }} ({{ __('validation.attributes.name') }}): <span class="required">*</span></label>
                                        <input type="text" name="new_resp_subject_name" value="{{ old('new_resp_subject_name', '') }}" class="form-control form-control-sm @error('new_resp_subject_name') is-invalid @endif">
                                        <span class="text-danger" id="error-new_resp_subject_name"></span>
                                    </div>
                                    <div class="col-md-3 col-12 mb-3">
                                        <label class="fw-semibold" >
                                            <input type="checkbox" name="subject_is_child" value="1" class="mr-1">{{ __('custom.rzs.subordinate') }}:
                                        </label>
                                        <span class="text-danger" id="error-subject_is_child"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.mail_templates.placeholders.to_name') }}:</label>
                                        <input type="text" class="form-control form-control-sm" name="to_name" value="{{ old('to_name', '') }}" id="to_name">
                                        <span class="text-danger" id="error-to_name"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.user_request') }}:</label>
                                        <textarea class="col-12 form-control summernote-custom-clone">@if(!empty(old('subject_user_request', ''))){!! old('subject_user_request') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="subject_user_request" value="@if(!empty(old('subject_user_request', ''))){!! old('subject_user_request') !!}@endif" id="subject_user_request">
                                        <span class="text-danger" id="error-subject_user_request"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{!! __('custom.comment_to_new_subject') !!}:</label>
                                        <textarea class="col-12 form-control summernote-standard-clone">@if(!empty(old('add_text', ''))){!! old('add_text') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="add_text" value="@if(!empty(old('add_text', ''))){!! old('add_text') !!}@endif" id="add_text">
                                        <span class="text-danger" id="error-add_text"></span>
                                    </div>
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{!! __('custom.request_to_current_subject') !!}:</label>
                                        <textarea class="col-12 form-control summernote-standard-clone">@if(!empty(old('current_subject_user_request', ''))){!! old('current_subject_user_request') !!}@endif</textarea>
                                        <input type="hidden" class="do-not-ignore summernote-val" name="current_subject_user_request" value="@if(!empty(old('current_subject_user_request', ''))){!! old('current_subject_user_request') !!}@endif" id="current_subject_user_request">
                                        <span class="text-danger" id="error-current_subject_user_request"></span>
                                    </div>
                                    @if($event->files)
                                        <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                                    @error('files')
                                        <span class="d-block text-danger">{{ $message }}</span>
                                    @enderror
                                        <p class="text-info fw-bold">Максимален размер на файл: {{ displayBytes(config('filesystems.max_upload_file_size')) }}
                                            <br>Максимален брой файлове: {{ config('filesystems.max_file_uploads') }}
                                            <br>Разрешени формати: {{ implode(',', \App\Models\File::ALLOWED_FILE_EXTENSIONS) }}</p>
                                        <table class="table table-light table-sm table-bordered mb-4" id="attachFiles">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{ __('front.file_name') }}</th>
                                                <th>{{ __('front.description') }}</th>
                                                <th>
                                                    <div>
                                                        <label for="tmpFile" class="form-label p-0 m-0">
                                                            <i class="fas fa-upload text-primary p-1" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __('front.upload_btn') }}"></i>
                                                        </label>
                                                        <input class="form-control d-none do-not-ignore" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles" data-admin="1" data-check="1">
                                                    </div>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 col-md-offset-3">
{{--                                        <p class="text-danger">Очаква се уточнение как ще се обработват тези препращания преди функционалността да бъде отключена</p>--}}
                                        <button id="save" type="button" class="save btn btn-success" data-form="formOutPlatform">{{ __('custom.apply') }}</button>
                                        <a href="{{ route('admin.application.view', ['item' => $application->id]) }}"
                                           class="btn btn-primary">{{ __('custom.back') }}</a>
                                    </div>
                                </div>
                                <br/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
    <script src="{{ asset('jquery-validation/custom_file_validation.js') }}"></script>
    <script type="text/javascript"  nonce="2726c7f26c">
        $(document).ready(function (){
            let applicationRequest = <?php echo json_encode(html_entity_decode($application->request));?>;
            let formInPlatform = $('#formInPlatform');
            let formOutPlatform = $('#formOutPlatform');

            // ----------------------------
            //Start Summernote custom button and init
            // ----------------------------
            var FullRequestBtn = function (context) {
                let ui = $.summernote.ui;
                let button = ui.button({
                    contents: '<i class="fas fa-copy"></i> Пълен текст',
                    tooltip: 'Постави пълния текст от запитването',
                    click: function () {
                        context.code(applicationRequest);
                    }
                });
                return button.render();
            }

            if($('.summernote-custom-clone').length) {
                //summernote with custom button and clone value to input for jquery validation
                $('.summernote-custom-clone').summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['view', ['fullscreen']],
                        ['customActions', ['full_request']]
                    ],
                    buttons: {
                        full_request: FullRequestBtn
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $editable.parent().parent().parent().find('.summernote-val').val(contents);
                        }
                    }
                });
            }

            if($('.summernote-standard-clone').length) {
                //standard summernote and clone value to input for jquery validation
                $('.summernote-standard-clone').summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['view', ['fullscreen']]
                    ],
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $editable.parent().parent().parent().find('.summernote-val').val(contents);
                        }
                    }
                });
            }
            // ----------------------------
            //End Summernote custom button and init
            // ----------------------------

            [formInPlatform, formOutPlatform].forEach(function (form) {
                form.validate({
                    ignore: ':hidden:not(.do-not-ignore)',
                    errorClass: 'is_invalid',
                    rules: getRules(form.data('rule')),
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
            });

            $('.save').on('click', function (){
                if ($("#" + $(this).data('form')).valid()) {
                    $("#" + $(this).data('form')).submit();
                }
            });


            function getRules (type){
                if( type === 'in' ) {
                    return {
                        'files[]': {
                            myextension: allowed_file_extensions,
                            myfilesize: max_upload_file_size
                        },
                        new_resp_subject_id: {
                            required: true
                        },
                        subject_user_request: {
                            required: true,
                        },
                        add_text: {
                            required: function(element){
                                if($('#formInPlatform input[name="subject_user_request"]').val() !== applicationRequest) {
                                    return true;
                                }
                                return false;
                            }
                        }
                    };
                } else {
                    return {
                        'files[]': {
                            myextension: allowed_file_extensions,
                            myfilesize: max_upload_file_size
                        },
                        new_resp_subject_eik: {
                            required: true
                        },
                        new_resp_subject_name: {
                            required: true
                        },
                        subject_user_request: {
                            required: true,
                        },
                        add_text: {
                            required: function(element){
                                if($('#formOutPlatform input[name="subject_user_request"]').val() !== applicationRequest) {
                                    return true;
                                }
                                return false;
                            }
                        }
                    };
                }

            }
        });
    </script>
@endpush
