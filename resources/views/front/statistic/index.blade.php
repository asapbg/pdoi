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
        @if(isset($customStatistics) && $customStatistics->count())
{{--            <div class="row">--}}
                @foreach($customStatistics as $customStatistic)
                    <div class="card card-light mb-4 col-md-6">
                        <div class="card-header app-card-header py-1 pb-0">
                            <h4 class="fs-5">
                                <i class="fa-solid fa-chart-line me-2"></i> {{ $customStatistic->name }}
                                <a class="d-inline-block" target="_blank" href="{{ route('custom_statistic.view', $customStatistic->id) }}" style="float: right;" data-bs-toggle="tooltip" title="{{ __('front.statistics.full_screen') }}"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <canvas id="chartCanvas{{ $customStatistic->id }}" style="margin: 0 auto;width:100%;" class="chart-container"></canvas>
                            </div>
                        </div>
                    </div>
                @endforeach
{{--            </div>--}}
        @endif
    </section>
@endsection
@if(isset($customStatistics) && $customStatistics->count())
    @push('scripts')
        <script src="{{ asset('\js\chart_js_v4.3.3.js') }}"></script>
        <script src="{{ asset('\js\hammer_2.0.7.js') }}"></script>
        <script src="{{ asset('\js\chart_js_plugin_zoom_2.0.1.js') }}"></script>
        <script type="text/javascript"  nonce="2726c7f26c">
            $(document).ready(function () {

                function initChart(chartType, chartData, chartTitle, chartId){
                    let statisticType = parseInt(chartType);
                    let data = chartData;
                    console.log(chartData);
                    if (statisticType === parseInt(<?php echo \App\Enums\CustomStatisticTypeEnum::TYPE_BASE->value; ?>)) {
                        const ctx = document.getElementById('chartCanvas' + chartId).getContext('2d');
                        const chart = new Chart(ctx,
                            {
                                type: 'line',
                                data: data,
                                options: {
                                    indexAxis: 'x',
                                    responsive: true,
                                    elements: {
                                        point:{
                                            radius:2,
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: chartTitle
                                        },
                                        zoom: {
                                            zoom: {
                                                drag: {
                                                    enabled: true
                                                },
                                                wheel: {
                                                    enabled: true,
                                                },
                                                pinch: {
                                                    enabled: true
                                                },
                                                mode: 'xy',
                                            }
                                        }
                                    }
                                },
                            });
                    }
                }
                @foreach($customStatistics as $cs)
                    initChart(@json($cs->type), @json(json_decode($cs->data, true)), @json($cs->name), @json($cs->id));
                @endforeach
            });
        </script>
    @endpush
@endif
