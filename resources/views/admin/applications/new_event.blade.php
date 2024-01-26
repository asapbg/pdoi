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
                            @if($event->days)
                                @if(isset($newEndDate))
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label fw-semibold" >{{ __('custom.term') }}:</label>
                                        <span>{{ $newEndDate }}</span>
                                    </div>
                                @endif
                            @endif
                            @if($event->app_event == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value)
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                    <label class="form-label fw-semibold" >{{ __('custom.event.FINAL_DECISION') }}:</label>
                                    <select name="final_status" class="form-control form-control-sm" required id="final_status">
                                        <option value=""></option>
                                        @foreach(\App\Enums\PdoiApplicationStatusesEnum::finalStatuses() as $status)
                                            @if($status->value != \App\Enums\PdoiApplicationStatusesEnum::FORWARDED->value)
                                            <option value="{{ $status->value }}" @if($status->value == \App\Enums\PdoiApplicationStatusesEnum::NOT_APPROVED->value) data-refuse="1" @endif>{{ __('custom.application.status.'.$status->name) }}</option>
                                               @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12"></div>
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3 d-none" id="refuse_reason_box">
                                    <label class="form-label fw-semibold" >{{ trans_choice('custom.reason_refusals', 1) }}:</label>
                                    <select name="refuse_reason" class="form-control form-control-sm" id="refuse_reason">
                                        <option value=""></option>
                                        @if($refusalReasons->count())
                                            @foreach($refusalReasons as $refuse)
                                                <option value="{{ $refuse->id }}">{{ $refuse->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            @endif


                            @if($event->new_resp_subject)
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label fw-semibold" >{{ __('custom.old_pdoi_subject') }}:</label>
                                    <span>стария задължен субект</span>
                                </div>
                                <div class="col-9 mb-3">
                                    <label class="form-label fw-semibold" >{{ __('custom.new_pdoi_subject') }}:</label>
                                    <div class="col-12 d-flex flex-row">
                                        <div class="input-group">
                                            <select required class="form-control form-control-sm select2" multiple="multiple" name="new_resp_subject_id" id="subjects">
                                                @if(isset($subjects) && sizeof($subjects))
                                                    @foreach($subjects as $option)
                                                        <option value="{{ $option['value'] }}" @if($option['value'] == old('new_resp_subject_id', '')) selected @endif>{{ $option['name'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject rounded"
                                                data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                                data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=0&admin=1' }}">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            @if($event->add_text)
                                <h5 class="bg-primary py-1 px-2">{{ __('custom.additional_info') }}</h5>
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label fw-semibold" ></label>
                                    @php($request = old('add_text', ''))
                                    <textarea class="form-control summernote w-100 @error('add_text') is-invalid @enderror" name="add_text" >{{ $request }}</textarea>
                                    @error('add_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            @if($event->files)
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                                @php($err = 0)
                                @php($inx = 0)
                                @while($inx < 20 || $err)
                                    @error('files.'.$inx)
                                    <div class="text-danger">{{ $message }}</div>
                                    @php($inx = 20)
                                    @enderror
                                    @php($inx += 1)
                                @endwhile
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
                                                <input class="form-control d-none" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles" data-admin="1" @if($event->app_event == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value) data-visibleoption="1" @endif>
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
    <script type="text/javascript">
        $(document).ready(function (){
            let reasonRefuseSelect = $('#refuse_reason');
            let finalDecisionSelect = $('#final_status');
            if(finalDecisionSelect) {
                finalDecisionSelect.on('change', function (){
                    let isRefuse = $('#final_status').find(':selected').data('refuse');
                    if( typeof isRefuse != 'undefined' && parseInt(isRefuse) == 1 ) {
                        $('#refuse_reason_box').removeClass('d-none');
                    } else {
                        $('#refuse_reason_box').addClass('d-none');
                        reasonRefuseSelect.val('');
                    }
                });
            }
        });
    </script>
@endpush
