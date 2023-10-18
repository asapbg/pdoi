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
    <script src="{{ asset('\js\chart_js_v4.3.3.js') }}"></script>
    <script src="{{ asset('\js\hammer_2.0.7.js') }}"></script>
    <script src="{{ asset('\js\chart_js_plugin_zoom_2.0.1.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let statisticType = parseInt(<?php echo $type; ?>);
            let data = <?php echo json_encode($chartData); ?>;
            if (statisticType === parseInt(<?php echo \App\Enums\StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value; ?>)
                || statisticType === parseInt(<?php echo \App\Enums\StatisticTypeEnum::TYPE_APPLICATION_MONTH->value; ?>) ) {
                const ctx = document.getElementById('chartCanvas').getContext('2d');
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
                                    text: '<?php echo $titlePeriod ?? 'Период'; ?>'
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
        });
    </script>
@endpush
