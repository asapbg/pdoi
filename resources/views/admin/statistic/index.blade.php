@extends('layouts.admin')

@section('content')

    <section class="content statistic">
        <div class="container-fluid">
            @if(isset($statistics) && sizeof($statistics))
                <hr>
                <div class="d-flex gap-md-4 flex-wrap justify-content-center">
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
                </div>
            @endif
            @if(isset($statisticLinks) && sizeof($statisticLinks))
                <hr>
                <div class="card">
                    <div class="card-body">
                        <ul>
                            @foreach($statisticLinks as $link)
                                <li>
                                    <a href="{{ $link['url'] ?? '' }}" title="{{ $link['name'] ?? '' }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                                        {{ $link['name'] ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </section>

@endsection
