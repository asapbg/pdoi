@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        <div class="card card-light mb-4">
{{--            <div class="card-header app-card-header py-1 pb-0">--}}
{{--                <h4 class="fs-5">--}}
{{--                    <i class="fa-solid fa-file me-2"></i> {{ trans_choice('custom.applications', 2) }} ({{ $applications ? $applications['pagination']['total'] : 0}})--}}
{{--                    @include('front.partials.filter_search_info')--}}
{{--                </h4>--}}
{{--            </div>--}}
            <div class="card-body">
                <div class="row">
                    @foreach(\App\Enums\StatisticTypeEnum::options() as $key => $s)
                        <div class="mb-3">
                            <a class="d-inline-block a-fs mb-1" href="{{ route('statistic.view', ['type' => $s]) }}">{{ __('front.statistic.type.'.$key) }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
