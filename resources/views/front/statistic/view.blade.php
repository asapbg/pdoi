@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        @include('front.partials.filter_form')
{{--            @if(isset($availablePeriods) && sizeof($availablePeriods))--}}
{{--                <div class="list-options mb-3 d-flex justify-content-end">--}}
{{--                    <div class="dropdown d-inline-block">--}}
{{--                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                            {{ __('custom.period') }}--}}
{{--                        </button>--}}
{{--                        <ul class="dropdown-menu">--}}
{{--                            @foreach($availablePeriods as $key => $name)--}}
{{--                                <li><a class="dropdown-item" href="{{ route('statistic.view', ['type' => $type, 'period' => $key]) }}">{{ $name }}</a></li>--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--           @endif--}}
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
