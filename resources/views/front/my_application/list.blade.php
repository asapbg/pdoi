@extends('layouts.app')

@section('content')
@php($cntApplication = isset($application) && isset($application['data']) ? sizeof($application['data']) : 0)
    <section class="content container">
        <div class="page-title mb-md-5 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('front.my_application.title') }}</h3>
        </div>
        <div class="list-options mb-3 d-flex justify-content-between">
            <div class="d-inline-block">Заявления ({{ $application['pagination']['total'] }})</div>
            <div class="dropdown d-inline-block">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Сортиране
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('application.my').'?' }}">{{ __('validation.attributes.subject') }}</a></li>
                    <li><a class="dropdown-item" href="#">{{ __('custom.date_apply') }}</a></li>
                </ul>
            </div>
        </div>
        @if($cntApplication)
            @foreach($application['data'] as $item)
                <div class="card card-light mb-4">
                    <div class="card-body">
                        <a class="short-request text-decoration-none d-inline-block mb-2" role="button">
                            @php($itemContent = strip_tags(html_entity_decode($item['request'])))
                            {{ $itemContent }}@if(strlen($itemContent) > 500){{ '...' }}@endif
                        </a>
                        <div>{{ $item['subject'] }} | {{ __('custom.reg_number') }}: {{ $item['uri'] }}</div>
                        <div>
                            {{ __('custom.date_apply') }}: {{ $item['created_at'] }} | {{ __('custom.status') }}:{{ $item['statusName'] }}
                        </div>
                    </div>
                </div>
            @endforeach
            {{ $application['links'] }}
        @else
            @include('front.partials.empty_list')
        @endif
    </section>
@endsection
