@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $application['title'] }}</h3>
        </div>

            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5">
                        <i class="fa-solid fa-file me-2"></i> {{ $application['subject'] }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <p class="my-1" style="font-size: 14px;"><strong>{{ __('custom.reg_number') }}:</strong>
                                {{ $application['uri'] }} | {{ $application['statusName'] }} |
                                <strong>{{ __('custom.date_apply') }}:</strong> {{ displayDate($application['created_at'] )}} |
                                <strong>{{ __('custom.term') }}:</strong>{{ displayDate($application['term'] ) }} |
                                <a href="#"><i class="fas fa-user text-dark fw-bold me-1"></i> {{ $application['user_name'] }}</a> |
                                <a href="/application-full-history.html"><i class="fas fa-history text-dark fw-bold me-1"></i>{{ __('custom.application.full_history') }}</a>
                            </p>
                            <p style="font-size: 14px;">
                                @if($application['phone'] && !empty($application['phone']))<i class="fas fa-phone text-dark fw-bold me-1"></i> {{ $application['phone'] }} |@endif
                                @if($application['email'] && !empty($application['email']))<i class="fas fa-envelope text-dark fw-bold me-1"></i> {{ $application['email'] }} |@endif
                                @if($application['address'] && !empty($application['address']))<i class="fas fa-location text-dark fw-bold me-1"></i> {{ $application['address'] }} |@endif
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            {!! html_entity_decode($application['request']) !!}
                        </div>
                        <div class="col-12 mb-3">
                            <div class="share-buttons d-inline-block me-3">
                                <div class="share-button d-inline-block p-2 rounded" role="button" data-color="#4267B2" data-selected="true" data-network="facebook" title="facebook" style="background-color: rgb(66, 103, 178);">
                                    <img alt="facebook" src="https://platform-cdn.sharethis.com/img/facebook.svg">
                                </div>
                                <div class="share-button d-inline-block p-2 rounded" role="button" data-color="#1DA1F2" data-selected="true" data-network="twitter" title="twitter" style="background-color: rgb(29, 161, 242);">
                                    <img alt="twitter" src="https://platform-cdn.sharethis.com/img/twitter.svg">
                                </div>
                            </div>
                            <p class="my-1 d-inline-block" style="font-size: 14px;"><i class="fas fa-eye text-primary me-1"></i>0</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <a href="{{ route('application.list') }}" class="btn btn-sm btn-primary">{{ __('custom.back') }}</a>
            </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
@endpush
