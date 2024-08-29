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
        @if($page->files->count())
            @foreach($page->files as $f)
                <div class="row p-3">
                    <a class="w-100 mb-2" target="_blank" href="{{ route('download.page.file', ['file' => $f->id]) }}">
                        {!! fileIcon($f->content_type) !!} {{ $f->description }}
                    </a>
                </div>
            @endforeach
        @endif
    </div>
@endsection
