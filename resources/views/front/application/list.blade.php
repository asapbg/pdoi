@extends('layouts.app')

@section('content')
@php($cntApplication = isset($applications) && isset($applications['data']) ? sizeof($applications['data']) : 0)
@php($applicationRouteName = isset($myList) && $myList ? 'application.my.show' : 'application.show')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $titlePage }}</h3>
        </div>
        @include('front.partials.filter_form')
        <div class="list-options mb-3 d-flex justify-content-end">
            @if(isset($sort) && $sort)
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('custom.sort') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('application.my').'?sort=subject&ord=asc' }}">{{trans_choice('custom.pdoi_response_subjects', 1) }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('application.my').'?sort=apply_date&ord=desc' }}">{{ __('custom.date_apply') }}</a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card card-light mb-4">
            <div class="card-header app-card-header py-1 pb-0">
                <h4 class="fs-5"><i class="fa-solid fa-file me-2"></i> {{ trans_choice('custom.applications', 2) }} ({{ $applications ? $applications['pagination']['total'] : 0}})</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($cntApplication)
                        @foreach($applications['data'] as $item)
                            <div class="col-12 mb-4 mb-3">
                                <a href="{{ route($applicationRouteName, ['id' => $item['id']]) }}" style="font-size: 16px;">
                                    @php($itemContent = strip_tags(html_entity_decode($item['request'])))
                                    {{ $itemContent }}@if(strlen($itemContent) > 500){{ '...' }}@endif
                                </a>
                                <p class="my-1" style="font-size: 14px;">{{ $item['subject'] }} | {{ __('custom.reg_number') }}: {{ $item['uri'] }}</p>
                                <p class="my-1" style="font-size: 14px;">{{ __('custom.date_apply') }}: {{ displayDate($item['created_at']) }} | {{ __('custom.status') }}: {{ $item['statusName'] }} | <i class="fas fa-eye text-primary me-1"></i>0</p>
                            </div>
                        @endforeach
                        {{ $applications['links'] }}
                    @else
                        @include('front.partials.empty_list', ['custom_message' => !request()->input('search') ? __('custom.use_filter') : null ])
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
