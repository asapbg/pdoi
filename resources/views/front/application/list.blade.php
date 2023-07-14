@extends('layouts.app')

@section('content')
@php($cntApplication = isset($applications) && isset($applications['data']) ? sizeof($applications['data']) : 0)
@php($applicationRouteName = isset($myList) && $myList ? 'application.my.show' : 'application.show')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
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
            <div class="card-header app-card-header py-1 pb-0">
                <h4 class="fs-5">
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
                                <p class="my-1 p-fs">{{ __('custom.reg_number') }}: {{ $item['uri'] }}</p>
                                <p class="my-1 p-fs">{{ __('custom.date_apply') }}: {{ displayDate($item['created_at']) }} | {{ __('custom.status') }}: {{ $item['statusName'] }} | <i class="fas fa-eye text-primary me-1"></i>{{ $item['cnt_visits'] }}</p>
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
