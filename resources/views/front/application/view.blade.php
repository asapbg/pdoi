@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $application['title'] }}</h3>
        </div>

            <div class="card card-light mb-4">
                <div class="card-header app-card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" role="button" aria-controls="application" aria-selected="true">{{ trans_choice('custom.applications',1) }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" role="button" aria-controls="history" aria-selected="false">{{ __('custom.history') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade active show" id="application" role="tabpanel" aria-labelledby="application-tab">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <p class="my-1 p-fs"><strong>{{ __('custom.reg_number') }}:</strong>
                                        {{ $application['uri'] }} | {{ $application['subject'] }} | {{ $application['statusName'] }} |
                                        <strong>{{ __('custom.date_apply') }}:</strong> {{ displayDate($application['created_at'] )}} |
                                        <strong>{{ __('custom.term') }}:</strong>{{ displayDate($application['term'] ) }}<br>
                                    </p>
                                    <p class="my-1 p-fs">
                                        <a href="#"><i class="fas fa-user text-dark fw-bold me-1"></i> {{ $application['user_name'] }}</a> |
                                    </p>
                                    <p class="p-fs">
                                        @if($application['phone'] && !empty($application['phone']))<i class="fas fa-phone text-dark fw-bold me-1"></i> {{ $application['phone'] }} |@endif
                                        @if($application['email'] && !empty($application['email']))<i class="fas fa-envelope text-dark fw-bold me-1"></i> {{ $application['email'] }} |@endif
                                        @if($application['address'] && !empty($application['address']))<i class="fas fa-location text-dark fw-bold me-1"></i> {{ $application['address'] }} |@endif
                                    </p>
                                </div>
                                <div class="col-12 mb-3">
                                    {!! html_entity_decode($application['request']) !!}
                                </div>
                                @if(!empty($application['response_date']))
                                    <div class="col-12 mb-3">
                                        <h4>{{ __('custom.decision') }}</h4>
                                        <hr>
                                        <p class="my-1 p-fs"><strong>{{ __('custom.date') }}: </strong> {{ $application['response_date'] }}</p>
                                        {!! html_entity_decode($application['response']) !!}
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
                                <div class="col-12 mb-3">
                                    <div class="share-buttons d-inline-block me-3">
                                        <div class="share-button d-inline-block p-2 rounded" role="button" data-color="#4267B2" data-selected="true" data-network="facebook" title="facebook" style="background-color: rgb(66, 103, 178);">
                                            <img alt="facebook" src="https://platform-cdn.sharethis.com/img/facebook.svg">
                                        </div>
                                        <div class="share-button d-inline-block p-2 rounded" role="button" data-color="#1DA1F2" data-selected="true" data-network="twitter" title="twitter" style="background-color: rgb(29, 161, 242);">
                                            <img alt="twitter" src="https://platform-cdn.sharethis.com/img/twitter.svg">
                                        </div>
                                    </div>
                                    <p class="my-1 d-inline-block p-fs"><i class="fas fa-eye text-primary me-1"></i>{{ $application['cnt_visits'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            <table class="table table-sm table-bordered table-responsive">
                                <thead>
                                <tr>
                                    <th>{{ __('custom.date') }}</th>
                                    <th>{{ trans_choice('custom.process', 1) }}</th>
                                </tr>
                                </thead>
                                <thead>
                                @if(isset($application['events']) && sizeof($application['events']))
                                    @foreach($application['events'] as $event)
                                        <tr>
                                            <td>{{ $event['date'] }}</td>
                                            <td>{{ $event['name'] }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2">{{ __('custom.no_results') }}</td></tr>
                                @endif
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">{{ __('custom.back') }}</a>
            </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
@endpush
