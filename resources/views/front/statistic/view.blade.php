@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        @include('front.partials.filter_form')
        @if(isset($availablePeriods) && sizeof($availablePeriods))
            <div class="list-options mb-3 d-flex justify-content-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        {{ __('custom.period') }}
                    </button>
                    <ul class="dropdown-menu" style="height: 200px;overflow-y: scroll;">
                        @foreach($availablePeriods as $key => $name)
                            <li><a class="dropdown-item"
                                   href="{{ route('statistic.view', ['type' => $type, 'period' => $key]) }}">{{ $name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="card card-light mb-4">
            {{--            <div class="card-header app-card-header py-1 pb-0">--}}
            {{--                <h4 class="fs-5">--}}
            {{--                    <i class="fa-solid fa-file me-2"></i> {{ trans_choice('custom.applications', 2) }} ({{ $applications ? $applications['pagination']['total'] : 0}})--}}
            {{--                    @include('front.partials.filter_search_info')--}}
            {{--                </h4>--}}
            {{--            </div>--}}
            <div class="card-body" style="height: 600px; overflow-y: scroll;">
                <div id="chart">
{{--                    <canvas id="chartCanvas" style="margin: 0 auto;width:100%;height:2000px;"></canvas>--}}
                    <canvas id="chartCanvas" style="margin: 0 auto;width:100%;"></canvas>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.3/chart.umd.js"
            integrity="sha512-wv0y1q2yUeK6D55tLrploHgbqz7ZuGB89rWPqmy6qOR9TmmzYO69YZYbGIYDmqmKG0GwOHQXlKwPyOnJ95intA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let statisticType = parseInt(<?php echo $type; ?>);
            let data = <?php echo json_encode($chartData); ?>;
            if (statisticType === parseInt(<?php echo \App\Enums\StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value; ?>)
                || statisticType === parseInt(<?php echo \App\Enums\StatisticTypeEnum::TYPE_APPLICATION_STATUS_TOTAL->value; ?>)
                || statisticType === parseInt(<?php echo \App\Enums\StatisticTypeEnum::TYPE_APPLICATION_MONTH->value; ?>) ) {
                const ctx = document.getElementById('chartCanvas').getContext('2d');
                const chart = new Chart(ctx,
                    {
                        type: 'bar',
                        data: data,
                        options: {
                            categoryPercentage: 0.5, // here
                            barPercentage: 1,  // here
                            maintainAspectRatio: false,
                            minBarLength:5,
                            maxBarThickness:5,
                            indexAxis: 'y',
                            scales: {
                                y: {
                                    min: 0,
                                    max: parseInt(<?php echo(isset($extraChartData['scaleX']['max']) ?? 0) ?>)
                                },
                                x: {
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            },
                            // Elements options apply to all of the options unless overridden in a dataset
                            // In this case, we are setting the border of each horizontal bar to be 2px wide
                            elements: {
                                bar: {
                                    borderWidth: 2,
                                    inflateAmount:7
                                }
                            },
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Период'
                                }
                            }
                        },
                    });
                console.log(chart);
            }
        });
    </script>
@endpush
