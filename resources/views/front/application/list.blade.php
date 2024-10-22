@extends('layouts.app')

@section('content')
@php($cntApplication = isset($applications) && isset($applications['data']) ? sizeof($applications['data']) : 0)
@php($applicationRouteName = isset($myList) && $myList ? 'application.my.show' : 'application.show')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        @if(isset($applicationsCnt) && sizeof($applicationsCnt))
            <div class="mb-3">
                @foreach($applicationsCnt as $status => $cnt)
                    <div class="legend-element">
                        <span class="app-badge {{ \App\Enums\PdoiApplicationStatusesEnum::styleByValue($status) }}">{{ $cnt }}</span>
                        <a href="{{ route('application.list').'?status='.$status }}">{{ __('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($status)) }}</a>
                    </div>
                @endforeach
            </div>
        @endif
        @include('front.partials.filter_form')
        @if($cntApplication)
            <div class="list-options mb-3 d-flex justify-content-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('custom.sort') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ addSortToUrlQuery(request()->query(), 'apply_date', 'asc') }}">{{ __('custom.date_apply') }} - {{ __('custom.sort_asc') }}</a></li>
                        <li><a class="dropdown-item" href="{{ addSortToUrlQuery(request()->query(), 'apply_date', 'desc') }}">{{ __('custom.date_apply') }} - {{ __('custom.sort_desc') }}</a></li>
                    </ul>
                </div>
            </div>
        @endif
        <div class="card card-light mb-4">
            <div class="card-header app-card-header py-2">
                <h4 class="fs-5 m-0">
                    <i class="fa-solid fa-file me-2"></i> {{ trans_choice('custom.applications', 2) }} ({{ $applications ? $applications['pagination']['total'] : 0}})
                    @include('front.partials.filter_search_info')
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($cntApplication)
                        @foreach($applications['data'] as $item)
                            <div class="col-12 mb-4 mb-3">
                                <a class="a-fs" href="{{ route($applicationRouteName, ['id' => $item['id']]) }}">@if(isset($myList) && $myList){{ $item['my_title'] }}@else{{ $item['title'] }}@endif</a>
                                <p class="my-1 p-fs"><i class="fas fa-building text-primary me-1"></i>{{ $item['subject'] }}</p>
                                <p class="my-1 p-fs">{{ __('custom.reg_number') }}: {{ $item['uri'] }}</p>
                                @php($itemContent = clearText(strip_tags(html_entity_decode($item['request']))))
                                <div class="my-2">
                                    {{ mb_substr($itemContent, 0, 300) }}@if(strlen($itemContent) > 300){{ '...' }}@endif
                                </div>
                                <p class="my-1 p-fs">{{ __('custom.date_apply') }}: {{ displayDate($item['created_at']) }} | {{ __('custom.status') }}: <span class="app-badge {{ $item['statusStyle'] }} fs-12">{{ $item['statusName'] }}</span> | <i class="fas fa-eye text-primary me-1"></i>{{ $item['cnt_visits'] }}</p>
                            </div>
                        @endforeach
                        {{ $applications['links'] }}
                    @else
                        @include('front.partials.empty_list', ['custom_message' => !isset($myList) && !request()->input('search') ? __('custom.use_filter') : null ])
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
