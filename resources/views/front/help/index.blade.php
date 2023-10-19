@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('custom.help') }}</h3>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <a href="{{ route('help.page', ['slug' => $appealPage->slug]) }}" title="{{ $appealPage->name }}"  class="home-section-button mb-lg-0 mb-4 light-yellow">
                    <span class="home-icon">
                        <i class="fa-solid fa-gavel text-warning"></i>
                    </span>
                    <span class="home-section-button-txt">
                        {{ $appealPage->name }}
                    </span>
                </a>
            </div>

            <div class="col-lg-4 col-md-12">
                <a href="{{ route('help.page', ['slug' => $videoInstructionPage->slug]) }}" title="{{ $videoInstructionPage->name }}" class="home-section-button mb-lg-0 mb-4 light-red">
                    <span class="home-icon">
                        <i class="fa-regular fa-file-lines text-danger"></i>
                    </span>
                    <span class="home-section-button-txt">
                        {{ $videoInstructionPage->name }}
                    </span>
                </a>
            </div>
            <div class="col-lg-4 col-md-12">
                <a href="{{ route('help.page', ['slug' => $guideManualPage->slug]) }}" title="{{ $guideManualPage->name }}" class="home-section-button light-green">
                    <span class="home-icon">
                        <i class="fa-regular fa-question text-success"></i>
                    </span>
                    <span class="home-section-button-txt">
                        {{ $guideManualPage->name }}
                    </span>
                </a>
            </div>
        </div>
{{--        <div class="card card-light mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="d-flex gap-md-4 flex-wrap justify-content-center">--}}
{{--                        @if(isset($appealPage) && $appealPage)--}}
{{--                        <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">--}}
{{--                            <a href="{{ route('help.page', ['slug' => $appealPage->slug]) }}" title="{{ $appealPage->name }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">--}}
{{--                                <span>--}}
{{--                                    <i class="fa-solid fa-gavel text-warning" style="font-size: 55px;"></i>--}}
{{--                                </span>--}}
{{--                                <span class="d-inline-block flex-grow-1">{{ $appealPage->name }}--}}
{{--                                </span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        @endif--}}
{{--                        <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">--}}
{{--                            <a href="{{ route('help.page', ['slug' => $videoInstructionPage->slug]) }}" title="{{ $videoInstructionPage->name }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">--}}
{{--                                <span>--}}
{{--                                    <i class="fa-regular fa-file-lines text-success" style="font-size: 55px;"></i>--}}
{{--                                </span>--}}
{{--                                <span class="d-inline-block flex-grow-1">{{ $videoInstructionPage->name }}--}}
{{--                                </span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">--}}
{{--                                <a href="{{ route('help.page', ['slug' => $guideManualPage->slug]) }}" title="{{ $guideManualPage->name }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">--}}
{{--                                <span>--}}
{{--                                    <i class="fa-regular fa-question text-danger" style="font-size: 55px;"></i>--}}
{{--                                </span>--}}
{{--                                    <span class="d-inline-block flex-grow-1">{{ $guideManualPage->name }}--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
    </section>
@endsection
