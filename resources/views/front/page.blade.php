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
                        <hr>
                    @endif
                    @if($item->files->count())
                        @foreach($item->files as $f)
                            <div class="row">
                                <a class="w-100 mb-2" target="_blank" href="{{ route('download.page.file', ['file' => $f->id]) }}">
                                    <i class="fas fa-file-download text-secondary me-1"></i> {{ $f->description }}
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @if(isset($contacts) && sizeof($contacts))
            <div class="card card-light mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="fw-bold">{{ trans_choice('custom.administrator_moderators', 2) }}</p>
                            <hr>
                            <select class="form-control form-control-sm select2" name="subject" id="subjectContact" data-url="{{ route('subject.contact_info') }}">
                                <option value="0">{{ __('custom.search_by_subject') }}</option>
                                @foreach($contacts as $c)
                                    @include('partials.pdoi_tree.tree_row_select2', ['subject' => $c])
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-bold">{{ trans_choice('custom.contacts', 2) }}</p>
                            <hr>
                            <div id="subjectContactResult"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
