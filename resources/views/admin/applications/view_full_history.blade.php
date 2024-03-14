@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                            <div class="row">
                                @if($item->manual)
                                    <div class="col-12 bg-info mb-3">{{ __('custom.application.manual_application') }}</div>
                                @endif
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.reg_number') }}:  <span class="text-primary">{{ $item->application_uri }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.status') }}:  <span class="text-primary">{{ $item->statusName }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.last_event') }}:  <span class="text-primary">{{ $item->currentEvent->event->name }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ trans_choice('custom.pdoi_response_subjects', 1)  }}:  <span class="text-primary">@if($item->response_subject_id) <a href="{{route('admin.rzs.edit', ['item' => $item->responseSubject])}}" target="_blank">{{ $item->responseSubject->subject_name }}</a>@else {{ $item->nonRegisteredSubjectName }} @endif</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ !$item->manual ? __('custom.date_apply') : __('custom.date_public') }}: <span class="text-primary">{{ displayDate($item->created_at) }}</span></div>
                                @if(!$item->manual)
                                    <div class="col-md-4 col-12 fw-bold">{{ __('custom.term') }}: <span class="text-primary">{{ displayDate($item->response_end_time) }}</span></div>
                                @endif
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.legal_form') }}:</label> <br>
                                    <label class="form-label me-3" role="button">
                                        <input type="radio" disabled name="legal_form" @if($item->applicant_type == \App\Models\User::USER_TYPE_PERSON) checked @endif>
                                        {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_PERSON] }}
                                    </label>
                                    <label class="form-label" role="button">
                                        <input type="radio" disabled name="legal_form" @if($item->applicant_type == \App\Models\User::USER_TYPE_COMPANY) checked @endif>
                                        {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_COMPANY] }}
                                    </label>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold" disabled>{{ trans_choice('custom.profile_type', 1)  }}: </label>
                                    <select class="form-control form-control-sm" disabled readonly="">
                                        <option>{{ $item->profileType ? $item->profileType->name : '' }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.name') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->full_names }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.email') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->email }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.phone') }}:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->phone }}" disabled>
                                </div>
                                <hr>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.country',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled readonly="">
                                        <option>{{ $item->country->name }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.area',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->area ? $item->area->ime : '' }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.municipality',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->municipality ? $item->municipality->ime : '' }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.settlement',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->settlement ? $item->settlement->ime : '' }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('validation.attributes.post_code') }}:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->post_code }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.address') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->address }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.address') }} 2:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->address_second }}" disabled>
                                </div>
                                <hr>
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('front.application.request_field.description') }}</label>
                                    <div class="w-100 border border-1 rounded-1 p-3" disabled>{!! html_entity_decode($item->request) !!}</div>
                                </div>
                                <hr>
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',2) }}</h5>
                                <table class="table table-light table-sm table-bordered mb-4">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('front.file_name') }}</th>
                                        <th>{{ __('front.description') }}</th>
                                        <th>{{ __('custom.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if($item->files->count())
                                            @foreach($item->files as $key => $file)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $file->filename }}</td>
                                                    <td>{{ $file->description }}</td>
                                                    <td>
                                                        <a class="btn btn-sm btn-secondary" type="button" href="{{ route('admin.download.file', ['file' => $file->id]) }}">
                                                            <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                               data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @if($item->categories->count())
                                    <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.categories',1) }}</h5>
                                    <div class="input-group input-group-sm mb-3">
                                        @foreach($item->categories as $row)
                                            <span class="badge badge-info pill rounded-1 text-bg-light ms-2 fw-normal" style="font-size: 12px;">{{ $row->name }}
                                                <i class="fas fa-times remove-category" data-application="{{ $item->id }}" data-category="{{ $row->id }}"
                                                   role="button" data-toggle="tooltip" title="{{ __('custom.remove') }}"></i>
                                            </span>
                                        @endforeach
                                        <span class="text-danger" id="remove-category-error"></span>
                                    </div>
                                @endif
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.events',2) }}</h5>
                                @if($item->events->count())
                                    @foreach($item->events as $event)
                                        <div class="col-12 mb-md-4 mb-3">
                                            <div class="row border border-1 rounded-1 p-3">
                                                <span class="font-weight-bold pl-0"><i class="far fa-calendar-check mr-2 text-primary"></i>{{ $event->eventReasonName }}</span>
                                                <hr>
                                                <div class="col-md-2 col-12 font-weight-semibold">{{ __('custom.date') }}:  <span class="text-primary">{{ displayDateTime($event->created_at) }}</span></div>
                                                <div class="col-md-8 col-12 font-weight-semibold">{{ trans_choice('custom.users', 1) }}: @if($event->user_reg > 0)<span class="text-primary"><a href="{{ route('admin.users.edit', ['user' => $event->user]) }}" target="_blank">{{ $event->user->names }}</a></span>@endif <span class="fst-italic text-primary">({{ $event->user_reg > 0 ? ($event->user->user_type == \App\Models\User::USER_TYPE_EXTERNAL ? __('custom.applicant') : __('custom.admin') ) : 'Системен' }})</span></div>
                                                @if(!empty($event->old_resp_subject_id) || !empty($event->new_resp_subject_id))
                                                    <div class="col-12 mt-2"></div>
                                                    @if(!empty($event->old_resp_subject_id))
                                                        <div class="col-md-6 col-12 font-weight-semibold">{{ __('custom.old_pdoi_subject') }}:  <span class="text-primary">{{ $event->oldSubject->subject_name }}</span></div>
                                                    @endif
                                                    @if(!empty($event->new_resp_subject_id))
                                                        <div class="col-md-6 col-12 font-weight-semibold">{{ __('custom.new_pdoi_subject') }}:  <span class="text-primary">{{ $event->newSubject->subject_name }}</span></div>
                                                    @endif
                                                @endif
                                                @if($event->court_decision)
                                                    <div class="col-12 font-weight-semibold mt-2">{{ __('custom.decision') }}:</div>
                                                    <div class="col-12 p-3">{{ __('custom.court_decision.'.\App\Enums\CourtDecisionsEnum::keyByValue((int)$event->court_decision)) }}</div>
                                                @endif
                                                @if(!empty($event->event_end_date))
                                                    <div class="col-12 font-weight-semibold mt-2">{{ __('custom.end_date') }}:</div>
                                                    <div class="col-12 p-3">{{ displayDate($event->event_end_date) }}</div>
                                                @endif
                                                @if(!empty($event->add_text))
                                                    <div class="col-12 font-weight-semibold mt-2">{{ __('custom.additional_info') }}:</div>
                                                    <div class="col-12 p-3">{!! html_entity_decode($event->add_text) !!}</div>
                                                @endif
                                                @if($event->files->count())
                                                    <div class="col-12 font-weight-semibold mt-2">{{ trans_choice('custom.documents', 2) }}:</div>
                                                    <table class="table table-sm mt-2">
                                                        <tbody>
                                                        @foreach($event->files as $ef)
                                                            <tr>
                                                                <td>
                                                                    {{ $loop->index + 1 }}
                                                                    <a class="btn btn-sm btn-secondary ml-2" type="button" href="{{ route('admin.download.file', ['file' => $ef->id]) }}">
                                                                        <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                                           data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                                    </a>
                                                                </td>
                                                                <td>{{ !empty($ef->description) ? $ef->description : 'Няма описание' }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr><td colspan="3">{{ __('custom.no_results') }}</td></tr>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-md-offset-3">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">{{ __('custom.back') }}</a>
                            <button class="btn btn-success print-window"><i class="text-white fas fa-print me-1"></i>{{ __('custom.print') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
