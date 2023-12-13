@extends('layouts.admin')

@section('title')
    {{__('custom.home')}}
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="row mt-3 p-3">
            @if($page)
                {!! $page->content !!}
            @endif
        </div>
    </div>
@endsection
