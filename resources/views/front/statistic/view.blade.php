@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        @include('front.partials.filter_form')
            <div class="list-options mb-3 d-flex justify-content-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('custom.period') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="">{{ __('custom.date_apply') }} - {{ __('custom.sort_asc') }}</a></li>
                        <li><a class="dropdown-item" href="">{{ __('custom.date_apply') }} - {{ __('custom.sort_desc') }}</a></li>
                    </ul>
                </div>
            </div>
        <div class="card card-light mb-4">
{{--            <div class="card-header app-card-header py-1 pb-0">--}}
{{--                <h4 class="fs-5">--}}
{{--                    <i class="fa-solid fa-file me-2"></i> {{ trans_choice('custom.applications', 2) }} ({{ $applications ? $applications['pagination']['total'] : 0}})--}}
{{--                    @include('front.partials.filter_search_info')--}}
{{--                </h4>--}}
{{--            </div>--}}
            <div class="card-body">
                data
            </div>
        </div>
    </section>
@endsection
