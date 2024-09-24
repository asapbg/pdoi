@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    @php($storeRoute = route($storeRouteName, $item->id ?? 0))
                    <form action="{{ $storeRoute }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">

                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2 mb-4">Примерни файлове</h5>
                            <div class="col-12 mb-2">
                                <a href="{{ route('admin.custom_statistic.download.example', \App\Enums\CustomStatisticTypeEnum::TYPE_BASE->value) }}"><i class="fas fa-link"></i> {{ __('custom.custom_statistics.'.\App\Enums\CustomStatisticTypeEnum::TYPE_BASE->name) }}</a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
                            <div class="col-md-4 col-12 mt-1">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">{{ __('validation.attributes.type') }}<span class="required">*</span></label>
                                    <div class="col-12">
                                        <select id="type" name="type"  class="form-control form-control-sm select2 @error('type'){{ 'is-invalid' }}@enderror">
                                            <option value="">---</option>
                                            @foreach(\App\Enums\CustomStatisticTypeEnum::options() as $key => $value)
                                                <option value="{{ $value }}" @if(old('type', ($item->id ? $item->type : '')) == $value) selected @endif>{{ __('custom.custom_statistics.'.$key) }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mt-1">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">
                                        {{ __('custom.date_public') }}
                                    </label>
                                    <div class="col-12">
                                        <input class="form-control form-control-sm datepicker-day @error('publish_from') is-invalid @enderror" type="text" name="publish_from"
                                               value="{{ displayDate(old('publish_from', ($item->id ? $item->publish_from : ''))) }}" >
                                        @error('publish_from')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mt-1">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">
                                        {{ __('custom.date_unpublic') }}
                                    </label>
                                    <div class="col-12">
                                        <input class="form-control form-control-sm datepicker-day @error('publish_to') is-invalid @enderror" type="text" name="publish_to"
                                               value="{{ displayDate(old('publish_to', ($item->id ? $item->publish_to : ''))) }}" >
                                        @error('publish_to')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"></div>
                            @include('admin.partial.edit_field_translate', ['field' => 'name'])

                            <div class="col-12 mt-1">
                                <div class="form-group">
                                    <label for="file" class="col-12 control-label">Изберете файл @if(!$item->id) <span class="required">*</span> @endif </label>
                                    <div class="col-12">
                                        <input class="form-control form-control-sm @error('file') is-invalid @enderror" id="file" type="file" name="file">
                                        @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($item->id)
                            <div class="row mb-4">
                                <h5 class="bg-primary py-1 px-2 mb-4">Графика</h5>
                                <div id="chart">
                                    <canvas id="chartCanvas" style="margin: 0 auto;width:100%;"></canvas>
                                </div>
                            </div>
                        @endif


                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route($listRouteName) }}"
                                   class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

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
