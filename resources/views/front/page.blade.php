@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $item->name }}</h3>
        </div>
        <div class="card card-light mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            {!! $item->content !!}
                        </div>
                        @if(isset($pages) && $pages->count())
                            @foreach($pages as $page)
                                <div class="mb-3">
                                    <a class="d-inline-block a-fs mb-1"href="{{ route('page', ['section_slug' => $page->section->slug, 'slug' => $page->slug]) }}">{{ $page->name }}</a>
                                    @if(!empty($page->short_content))
                                        <p class="p-fs">{{ $page->short_content }}</p>
                                    @endif
                                </div>
                            @endforeach
                            {{ $pages->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
    </section>
@endsection
