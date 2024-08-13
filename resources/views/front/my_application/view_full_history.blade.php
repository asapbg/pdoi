@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('custom.full_history') }}: {{ $application['my_title'] }}</h3>
        </div>
        <div class="card card-light mb-4">
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="application" role="tabpanel" aria-labelledby="application-tab">
                        <div class="row">
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.reg_number') }}:  <span class="text-primary">{{ $application['uri'] }}</span></div>
                            <div class="col-md-4 col-12 fw-bold mb-2">{{ __('custom.status') }}:  <span class="text-primary">{{ $application['statusName'] }}</span></div>
                            <div class="col-md-5 col-12 fw-bold mb-2">{{ trans_choice('custom.pdoi_response_subjects', 1)  }}:  <span class="text-primary">{{ $application['response_subject_name'] }}</span></div>
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.date_apply') }}: <span class="text-primary">{{ displayDate($application['created_at']) }}</span></div>
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.term') }}: <span class="text-primary">{{ displayDate($application['term']) }}</span></div>
                            <div class="col-md-12 col-12 fw-bold mb-2">{{ trans_choice('custom.categories', 2) }}: <span class="text-primary">@if(sizeof($application['themes'])){{ implode(';', $application['themes']) }}@else{{ '---' }}@endif</span></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('custom.name') }} @if($application['public_names'])<sup class="me-1"><i class="fa-solid fa-eye text-warning fs" data-bs-toggle="tooltip" title="{{ __('front.public') }}"></i></sup>@endif:</label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['names'] }}" disabled>
                            </div>
                            <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('custom.email') }} @if($application['public_email'])<sup class="me-1"><i class="fa-solid fa-eye text-warning fs" data-bs-toggle="tooltip" title="{{ __('front.public') }}"></i></sup>@endif:</label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['email'] }}" disabled>
                            </div>
                            <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('custom.phone') }} @if($application['public_phone'])<sup class="me-1"><i class="fa-solid fa-eye text-warning fs" data-bs-toggle="tooltip" title="{{ __('front.public') }}"></i></sup>@endif:</label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['phone'] }}" disabled>
                            </div>
                            <hr>
                            <h5 class="app-title-bg py-1 px-2 mb-4">{{ __('custom.address_for_contact') }} @if($application['public_address'])({{ __('front.public') }})@endif</h5>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ trans_choice('custom.country',1) }}:</label>
                                <select class="form-control form-control-sm" disabled readonly="">
                                    <option>{{ $application['country'] }}</option>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ trans_choice('custom.area',1) }}:</label>
                                <select class="form-control form-control-sm" disabled>
                                    <option>{{ $application['area'] }}</option>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ trans_choice('custom.municipality',1) }}: </label>
                                <select class="form-control form-control-sm" disabled>
                                    <option>{{ $application['municipality'] }}</option>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ trans_choice('custom.settlement',1) }}: <span class="required">*</span></label>
                                <select class="form-control form-control-sm" disabled>
                                    <option>{{ $application['settlement'] }}</option>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('validation.attributes.post_code') }}:</label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['post_code'] }}" disabled>
                            </div>
                            <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('custom.address') }} : </label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['address'] }}" disabled>
                            </div>
                            <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('custom.address') }} 2:</label>
                                <input class="form-control form-control-sm" type="text" value="{{ $application['address_second'] }}" disabled>
                            </div>
                            <hr>
                            <h5 class="app-title-bg py-1 px-2 mb-4">{{ __('front.application.request_field.description') }}</h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <div class="w-100 border border-1 rounded-1 p-3" disabled>{!! html_entity_decode($application['request']) !!}</div>
                            </div>
                            <hr>
                            <h5 class="app-title-bg py-1 px-2 mb-4">{{ trans_choice('custom.documents',2) }}</h5>
                            <table class="table table-light table-sm table-bordered mb-4">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('front.file_name') }}</th>
                                    <th>{{ __('front.description') }}</th>
                                    <th>{{ __('custom.visible_m') }}</th>
                                    <th>{{ __('custom.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($application['files']) && isset($application['files']['data']) && sizeof($application['files']['data']))
                                    @foreach($application['files']['data'] as $key => $file)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $file['name'] }}</td>
                                            <td>{{ $file['description'] }}</td>
                                            <td><i class="fa-solid @if($file['visible']){{ 'fa-check text-success' }}@else{{ 'fa-minus text-danger' }}@endif"></i></td>
                                            <td>
                                                <a class="btn btn-sm btn-secondary" type="button" href="{{ route('download.file', ['file' => $file['id']]) }}">
                                                    <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                       data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>

                            <h5 class="app-title-bg py-1 px-2 mb-4">{{ trans_choice('custom.events',2) }}</h5>
                            @if(isset($application['events']) && sizeof($application['events']))
                                @foreach($application['events'] as $event)
                                    <div class="col-12 mb-md-4 mb-3">
                                        <div class="row border border-1 rounded-1 p-3">
                                            <span class="font-weight-bold ps-0"><i class="far fa-calendar-check me-2"></i>{{ $event['name'] }}</span>
                                            <hr>
                                            <div class="col-md-2 col-12 fw-semibold">{{ __('custom.date') }}:  <span class="text-primary">{{ $event['date'] }}</span></div>
                                            <div class="col-md-8 col-12 fw-semibold">{{ trans_choice('custom.users', 1) }}: <span class="text-primary fw-normal">{{ $event['user_name'] }}</span> @if(!empty($event['user_type']))<span class="fst-italic text-primary fw-normal">({{ $event['user_type'] }})</span>@endif</div>
                                            @if(!is_null($event['old_subject']) || !is_null($event['new_subject']))
                                                <div class="col-12 mt-3"></div>
                                                @if(!is_null($event['old_subject']))
                                                    <div class="col-md-6 col-12 fw-semibold">{{ __('custom.old_pdoi_subject') }}:  <span class="text-primary fw-normal">{{ $event['old_subject'] }}</span></div>
                                                @endif
                                                @if(!is_null($event['new_subject']))
                                                    <div class="col-md-6 col-12 fw-semibold">{{ __('custom.new_pdoi_subject') }}:  <span class="text-primary fw-normal">{{ $event['new_subject'] }}</span></div>
                                                @endif
                                            @endif
                                            @if(!is_null($event['court_decision']))
                                                <div class="col-12 fw-semibold mt-3">{{ __('custom.decision') }}:</div>
                                                <div class="col-12 mt-2">{{ $event['court_decision'] }}</div>
                                            @endif
                                            @if(!is_null($event['end_date']))
                                                <div class="col-12 fw-semibold mt-3">{{ __('custom.end_date') }}:</div>
                                                <div class="col-12 mt-2">{{ $event['end_date'] }}</div>
                                            @endif
                                            @if(!empty($event['no_consider_reason_name']) || !empty($event['no_consider_reason_text']))
                                                @if(!empty($event['no_consider_reason_name']))
                                                    <div class="col-12 fw-semibold mt-3">{{ __('custom.no_consider_reason') }}:  <span class="fw-normal">{{ $event['no_consider_reason_name'] }}</span></div>
                                                @else
                                                    <div class="col-12 fw-semibold mt-3">{{ __('custom.no_consider_reason') }}:</div>
                                                    <div class="col-12 mt-2">{!! html_entity_decode($event['no_consider_reason_text']) !!}</div>
                                                @endif
                                            @endif
                                            @if(!empty($event['text']))
                                                <div class="col-12 fw-semibold mt-3">{{ __('custom.additional_info') }}:</div>
                                                <div class="col-12 mt-2">{!! html_entity_decode($event['text']) !!}</div>
                                            @endif
                                            @if(isset($event['files']) && isset($event['files']['data']) && sizeof($event['files']['data']))
                                                <div class="col-12 fw-semibold mt-3">{{ trans_choice('custom.documents', 2) }}:</div>
                                                <table class="table table-sm">
                                                    <tbody>
                                                    @foreach($event['files']['data'] as $ef)
                                                        <tr>
                                                            <td>
                                                                {{ $loop->index + 1 }}
                                                                <a class="btn btn-sm btn-secondary ml-2" type="button" href="{{ route('download.file', ['file' => $ef['id']]) }}">
                                                                    <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                                       data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                                </a>
                                                            </td>
                                                            <td>{{ !empty($ef['description']) ? $ef['description'] : 'Няма описание' }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                            @if(!empty($event['edit_reason']))
                                                <div class="col-12 fw-semibold mt-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('custom.edited') }}:</div>
                                                <div class="col-12 mt-2">{!! html_entity_decode($event['edit_reason']) !!}</div>
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
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">{{ __('custom.back') }}</a>
                        <button class="btn btn-sm btn-success  print-window"><i class="text-white fas fa-print me-1"></i>{{ __('custom.print') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
