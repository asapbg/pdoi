@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.application.event.new.store') }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="event" value="{{ $event->id }}">
                        <input type="hidden" name="application" value="{{ $application->id }}">
                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2">{{ __('custom.data_for_event') }} {{ $event->name }}</h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >{{ __('custom.old_pdoi_subject') }}:</label>
                                <span>{{ $application->responseSubject ? $application->responseSubject->subject_name : '---' }}</span>
                            </div>
                            <div class="col-9 mb-3">
                                <label class="form-label fw-semibold" >{{ __('custom.new_pdoi_subject') }}:</label>
                                <div class="col-12 d-flex flex-row">
                                    <div class="input-group">
                                        <select required class="form-control form-control-sm select2" multiple="multiple" name="new_resp_subject_id[]" id="subjects">
                                            @if(isset($subjects) && sizeof($subjects))
                                                @foreach($subjects as $option)
                                                    <option value="{{ $option['value'] }}" @if($option['value'] == old('new_resp_subject_id', '')) selected @endif>{{ $option['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject rounded"
                                            data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                            data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=1&admin=1' }}">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 row mb-4" id="subjects-notes"></div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >Запитване:</label>
                                <span>{!! html_entity_decode($application->request) !!}</span>
                            </div>

                            <h5 class="bg-primary py-1 px-2">Отговор на част от запитването</h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" ></label>
                                @php($request = old('add_text', ''))
                                <textarea class="form-control summernote w-100 @error('add_text') is-invalid @enderror" name="add_text">{{ $request }}</textarea>
                                @error('add_text')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($event->files)
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
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
                                                <input class="form-control d-none" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles" data-admin="1">
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
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.apply') }}</button>
                                <a href="{{ route('admin.application.view', ['item' => $application->id]) }}"
                                   class="btn btn-primary">{{ __('custom.cancel') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function (){
            let subjectNotesContainer = $('#subjects-notes');
            let applicationRequest = '<?php echo html_entity_decode($application->request)?>';

            var FullRequestBtn = function (context) {
                let ui = $.summernote.ui;
                let button = ui.button({
                    contents: '<i class="fas fa-copy"></i> Пълен текст',
                    tooltip: 'Постави пълния текс от запитването',
                    click: function () {
                        context.invoke('editor.pasteHTML', applicationRequest);
                    }
                });
                return button.render();
            }

            function setAsSummernote(domEl) {
                domEl.summernote({
                    height:200,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['view', ['fullscreen']],
                        ['customActions', ['full_request']]
                    ],
                    buttons: {
                        full_request: FullRequestBtn
                    },
                    // callbacks: {
                    //     onChange: function(contents, $editable) {
                    //         // console.log('onChange:', contents, $editable);
                    //         $editable.parent().find('textarea').text(contents);
                    //     }
                    // }
                });
            }

            $('#subjects').on('change', function (){
                let subjectIds = $('#subjects').val();
                if( subjectIds.length > 0 ) {
                    subjectIds.forEach(function (val, i){
                        if( $('#subject-' + val).length == 0 ) {
                            subjectNotesContainer.append('<div id="subject-'+ val +'" class="col-md-6 col-12 new-subject" data-subject="'+ val +'">' +
                                '<label class="col-12 form-label fw-semibold left-border">'+ $('#subjects option[value="'+ val +'"]').html() +'</label>' +
                                '<textarea class="col-12 form-control summernote" name="subject-notes[]"></textarea>' +
                                '</div>');
                            setAsSummernote($('#subject-' + val + ' textarea'));
                        }
                    });

                    $('.new-subject').each(function (){
                        console.log($(this).data('subject'));
                        let found = false;
                        for(let i = 0; i <= subjectIds.length; i++){
                            if(parseInt(subjectIds[i]) === parseInt($(this).data('subject'))) {
                                found = true;
                                console.log('found', $(this).data('subject'));
                            }
                        }
                        if( !found ) {console.log('remove', $(this));$(this).remove();}
                    });
                } else{
                    subjectNotesContainer.html('');
                }
            });

            $('#form').validate({
                ignore: ':hidden:not(.do-not-ignore)',
                errorClass: 'is_invalid',
                rules: {
                    // 'files[]': {
                    //     myextension: allowed_file_extensions,
                    //     myfilesize: max_upload_file_size
                    // },
                    'new_resp_subject_id[]': {
                        required: true
                    },
                    'subject-notes[]': {
                        required: true,
                    },
                },
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
    </script>
@endpush
