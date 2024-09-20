@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $item->name }}</h3>
        </div>
        <div class="card card-light mb-4">
            <div class="card-body" style="height: 600px; overflow-y: scroll;">
                <div id="chart">
                    <canvas id="chartCanvas" style="margin: 0 auto;width:100%;"></canvas>
                </div>

                <div class="row">

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('\js\chart_js_v4.3.3.js') }}"></script>
    <script src="{{ asset('\js\hammer_2.0.7.js') }}"></script>
    <script src="{{ asset('\js\chart_js_plugin_zoom_2.0.1.js') }}"></script>
    <script type="text/javascript"  nonce="2726c7f26c">
        $(document).ready(function () {
            let statisticType = parseInt(<?php echo $item->type; ?>);
            let statisticTitle = @json($item->name);
            let data = @json(json_decode($item->data, true));
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
                                    text: statisticTitle
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
