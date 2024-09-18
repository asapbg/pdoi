@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $application['my_title'] }}</h3>
        </div>
        <div class="card card-light mb-4">
            <div class="card-header app-card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" role="button" aria-controls="application" aria-selected="true">{{ trans_choice('custom.applications',1) }}</a>
                    </li>
                    @if(!empty($application['response_date']))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="answer-tab" data-bs-toggle="tab" data-bs-target="#answer" role="button" aria-controls="answer" aria-selected="false">{{ __('custom.answer') }}</a>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" role="button" aria-controls="history" aria-selected="false">{{ __('custom.history') }}</a>
                    </li>
                    @if(isset($application['children']) && sizeof($application['children']))
                        <li class="nav-item">
                            <a class="nav-link" id="sub-application-tab" data-bs-toggle="tab" data-bs-target="#sub-application" role="button" aria-controls="sub-application" aria-selected="false">Препратени заявления</a>
                        </li>
                    @endif
                    @if(isset($application['renewRequests']) && sizeof($application['renewRequests']))
                        <li class="nav-item">
                            <a class="nav-link" id="renews-tab" data-bs-toggle="tab" data-bs-target="#renews" role="button" aria-controls="renews" aria-selected="false">Завки за възобновяване</a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="application" role="tabpanel" aria-labelledby="application-tab">
                        @if(!empty($needInfoSection))
                            <div class="row">
                                <h5 class="app-title-bg py-1 px-2 mb-4 bg-danger">{{ $needInfoSection['event_name'] }}</h5>
                                <form method="post" action="{{ route('application.my.send_info') }}">
                                    <input type="hidden" value="{{ $application['id'] }}" name="item">
                                    @csrf
                                    <p><strong>{{ __('front.need_info_message', ['date' => displayDate($needInfoSection['event_date'])]) }}</strong>:
                                    <br>{!! html_entity_decode($needInfoSection['msg']) !!}</p>
                                    <p><strong>{{ __('front.need_info_end_date') }}</strong>: {{ displayDate($needInfoSection['event_end']) }}</p>
                                    <div class="form-group form-group-sm col-12">
{{--                                        <label class="form-label fw-semibold">{{ __('validation.attributes.extra_info') }}:</label>--}}
                                        @php($oldInfo = old('extra_info', ''))
                                        <textarea class="summernote w-100 @error('extra_info') is-invalid @enderror" name="extra_info" id="extra_info">{{ $oldInfo }}</textarea>
                                        @error('extra_info')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary mt-3">{{ __('custom.send') }}</button>
                                </form>
                            </div>
                            <hr>
                        @endif

                        <div class="row">
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.reg_number') }}:  <span class="text-primary">{{ $application['uri'] }}</span></div>
                            <div class="col-md-4 col-12 fw-bold mb-2">
                                {{ __('custom.status') }}:  <span class="text-primary">{{ $application['statusName'] }}</span>
                                @if($application['canRenewRequest'])
                                    <a href="{{ route('application.my.renew', $application['id']) }}" class="btn btn-sm btn-success ms-2">{{ __('custom.renew') }}</a>
                                @endif
                            </div>
                            <div class="col-md-5 col-12 fw-bold mb-2">{{ trans_choice('custom.pdoi_response_subjects', 1)  }}:  <span class="text-primary">{{ $application['response_subject_name'] }}</span></div>
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.date_apply') }}: <span class="text-primary">{{ displayDate($application['created_at']) }}</span></div>
                            <div class="col-md-3 col-12 fw-bold mb-2">{{ __('custom.term') }}: <span class="text-primary">{{ displayDate($application['term']) }}</span></div>
                            <div class="col-md-12 col-12 fw-bold mb-2">{{ trans_choice('custom.categories', 2) }}: <span class="text-primary">@if(sizeof($application['themes'])){{ implode(';', $application['themes']) }}@else{{ '---' }}@endif</span></div>
                            <div class="col-md-12 col-12 fw-bold mb-2">
                                <a href="{{ route('application.my.show.history', ['id' => $application['id']]) }}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square fw-bold me-1"></i>{{ __('custom.application.full_history') }}</a>
                            </div>

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
                        </div>
                    </div>
                    @if(!empty($application['response_date']))
                        <div class="tab-pane fade" id="answer" role="tabpanel" aria-labelledby="answer-tab">
                            @if(isset($application['response_is_changed_message']) && $application['response_is_changed_message'])
                                <p class="my-1 p-fs"><i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('custom.change_after_final_decision') }}</p>
                            @endif
                            <p class="my-1 p-fs"><strong>{{ __('custom.status') }}: </strong> {{ $application['statusName'] }}</p>
                            <p class="my-1 p-fs"><strong>{{ __('custom.date') }}: </strong> {{ $application['response_date'] }}</p>

                            @if(!empty($application['no_consider_reason_name']) || !empty($application['no_consider_reason_text']))
                                @if(!empty($application['no_consider_reason_name']))
                                    <p class="my-1 p-fs"><strong>{{ __('custom.no_consider_reason') }}: </strong> {{ $application['no_consider_reason_name'] }}</p>
                                @else
                                    <p class="my-1 p-fs"><strong>{{ __('custom.no_consider_reason') }}: </strong></p>
                                    {!! html_entity_decode($application['no_consider_reason_text']) !!}
                                @endif
                            @endif

                            @if(!empty($application['response']))
                                {!! html_entity_decode($application['response']) !!}
                            @endif

                            @if(isset($application['final_files']) && isset($application['final_files']['data']) && sizeof($application['final_files']['data']))
                                <hr>
                                <p class="my-1 p-fs"><strong>{{ trans_choice('custom.documents', 2) }}: </strong></p>
                                <table class="table table-sm mе-4">
                                    <tbody>
                                    @foreach($application['final_files']['data'] as $file)
                                        <tr>
                                            <td>
                                                {{ $loop->index + 1 }}
                                                <a class="btn btn-sm btn-secondary ms-2" type="button" href="{{ route('download.file', ['file' => $file['id']]) }}">
                                                    <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                       data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                </a>
                                            </td>
                                            <td>{{ !empty($file['description']) ? $file['description'] : 'Няма описание' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <table class="table table-sm table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th>{{ __('custom.date') }}</th>
                                <th>{{ trans_choice('custom.process', 1) }}</th>
                                <th>{{ trans_choice('custom.users', 1) }}</th>
                            </tr>
                            </thead>
                            <thead>
                                @if(isset($application['events']) && sizeof($application['events']))
                                    @foreach($application['events'] as $event)
                                        <tr>
                                            <td class="text-nowrap">{{ $event['date'] }}</td>
                                            <td>{{ $event['name'] }}</td>
                                            <td><a href="">{{ $event['user_name'] }}</a>
                                                @if(!empty($event['user_type']))<span class="fst-italic">({{ $event['user_type'] }})</span>@endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="3">{{ __('custom.no_results') }}</td></tr>
                                @endif
                            </thead>
                        </table>
                    </div>
                    @if(isset($application['children']) && sizeof($application['children']))
                        <div class="tab-pane fade" id="sub-application" role="tabpanel" aria-labelledby="sub-application-tab">
                            <table class="table table-sm table-bordered table-responsive">
                                <thead>
                                <tr>
                                    <th>{{ __('custom.reg_number') }}</th>
                                    <th>{{ __('custom.new_pdoi_subject') }}</th>
                                    <th>{{ __('custom.created_at') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                </tr>
                                </thead>
                                <thead>
                                @foreach($application['children'] as $child)
                                    <tr>
                                        <td>
                                            <a href="{{ route('application.show', ['id' => $child['id']]) }}" target="_blank">
                                                <i class="fas fa-external-link-alt text-primary"></i> {{ $child['reg_num'] }}</a>
                                        </td>
                                        <td>{{ $child['subject'] }}</td>
                                        <td class="text-nowrap">{{ $child['date'] }}</td>
                                        <td>{{ $child['status'] }}</td>
                                    </tr>
                                @endforeach
                                </thead>
                            </table>
                        </div>
                    @endif
                </div>
                @if(isset($application['renewRequests']) && sizeof($application['renewRequests']))
                        <div class="tab-pane fade" id="renews" role="tabpanel" aria-labelledby="renews-tab">
                            <table class="table table-sm table-bordered table-responsive">
                                <thead>
                                <tr>
                                    <th>{{ __('custom.date_apply') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                    <th>{{ __('custom.status_date') }}</th>
                                    <th>Обработено от</th>
                                    <th>{{ __('custom.answer') }}</th>
                                </tr>
                                </thead>
                                <thead>
                                @foreach($application['renewRequests'] as $renew)
                                    <tr>
                                        <td>{{ displayDate($renew['created_at']) }}</td>
                                        <td>{{ $renew['status'] }}</td>
                                        <td>{{ displayDate($renew['status_date']) }}</td>
                                        <td>{{ $renew['statusUser'] }}</td>
                                        <td>{!! $renew['reason_refuse'] !!}</td>
                                    </tr>
                                @endforeach
                                </thead>
                            </table>
                        </div>
                    @endif
                <div class="form-group row mt-2">
                    <div class="col-md-6 col-md-offset-3">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">{{ __('custom.back') }}</a>
                    </div>
                </div>
                </div>

            </div>
        </div>
    </section>
@endsection
<link href="{{ asset('summernote/summernote-lite.min.css') }}" rel="stylesheet">
@push('scripts')
    <script src="{{ asset('summernote/summernote-lite.min.js') }}"></script>
@endpush
