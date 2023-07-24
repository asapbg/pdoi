@extends('layouts.admin')

@section('content')

    <section class="content statistic">
        <div class="container-fluid">
            <hr>
            <div class="d-flex gap-md-4 flex-wrap justify-content-center">
                @if(isset($statistics) && sizeof($statistics))
                    @foreach($statistics as $item)
                        <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-white statistic-box">
                            <a href="{{ $item['url'] ?? '' }}" title="{{ $item['name'] ?? '' }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                                <span>
                                    <i class="{{ $item['icon_class'] ?? '' }}" style="font-size: 55px;"></i>
                                </span>
                                <span class="d-inline-block flex-grow-1">
                                    {{ $item['name'] ?? '' }}
                                <span class="d-inline-block fsi">@if(isset($item['description']) && !empty($item['description'])){{ $item['description'] }}@else &nbsp; @endif</span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="font-weight-bold">{{ __('custom.statistics.not_available_statistic') }}</p>
                @endif
            </div>
        </div>
    </section>

@endsection
