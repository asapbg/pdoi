@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $item->name }}</h3>
        </div>
        <div class="row">
            {!! $item->content !!}
        </div>
        <div class="row">
            @foreach(config('instructions.videos') as $key => $section)
                <div class="col-md-6 p-3">
                    <div class="accordion" id="accordionExample_{{ $key }}">
                        @foreach($section as $name)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading_{{ $name }}">
                                    <button class="accordion-button py-2 @if(!$loop->first){{ 'collapsed' }}@endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $name }}" aria-expanded="@if($loop->first){{ 'true' }}@else{{ 'false' }}@endif" aria-controls="collapse_{{ $name }}">
                                        {{ __('custom.instructions.'.$name) }}</button>
                                </h2>
                                <div id="collapse_{{ $name }}" class="accordion-collapse collapse @if($loop->first){{ 'show' }}@endif" aria-labelledby="heading_{{ $name }}" data-bs-parent="#accordionExample_{{ $key }}" style="">
                                    <div class="accordion-body">
                                        {!! __('custom.instructions.'.$name.'.content') !!}
{{--                                        <div class="video-container" style="position: relative;overflow: hidden;width: 100%;">--}}
{{--                                            --}}
{{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
