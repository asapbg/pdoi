@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    @php($event = $item['event'])
                    <div class="row mb-4">
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
                        @if($event->event_type == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value && $event->event_reason == \App\Enums\PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value)
                            @if($event->noConsiderReason)
                                <div class="col-md-6 col-12 font-weight-semibold mt-2">{{ __('custom.no_consider_reason') }}:  <span class="font-weight-normal">{{ $event->noConsiderReason->name }}</span></div>
                                @if(!empty($event->add_text))
                                    <div class="col-12 font-weight-semibold mt-2 mt-2">{{ __('custom.additional_info') }}:</div>
                                    <div class="col-12 p-3">{!! html_entity_decode($event->add_text) !!}</div>
                                @endif
                            @else
                                @if(!empty($event->add_text))
                                    <div class="col-12 font-weight-semibold mt-2">{{ __('custom.no_consider_reason') }}:</div>
                                    <div class="col-12 p-3">{!! html_entity_decode($event->add_text) !!}</div>
                                @endif
                            @endif
                        @else
                            @if(!empty($event->add_text))
                                <div class="col-12 font-weight-semibold mt-2">{{ __('custom.additional_info') }}:</div>
                                <div class="col-12 p-3">{!! html_entity_decode($event->add_text) !!}</div>
                            @endif
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
                        @if(!empty($event->edit_final_decision_reason))
                            <div class="col-12 font-weight-semibold mt-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('custom.edited') }}:</div>
                            <div class="col-12 p-3">{!! html_entity_decode($event->edit_final_decision_reason) !!}</div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-md-offset-3">
                            <a href="{{ route('admin.application.view', ['item' => $event['pdoi_application_id']]) }}"
                               class="btn btn-primary">{{ __('custom.back') }}</a>
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </section>
@endsection

