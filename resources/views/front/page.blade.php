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
                                <a class="d-inline-block a-fs mb-1" href="{{ route('page', ['section_slug' => $page->section->slug, 'slug' => $page->slug]) }}">{{ $page->name }}</a>
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
                                    {!! fileIcon($f->content_type) !!} {{ $f->description }}
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

            <div class="card card-light mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="fw-bold">{{ __('custom.asap_support') }}</p>
                            <p>АСАП ЕООД -
                                <script type="text/javascript"  nonce="2726c7f26c">
                                    for(var ioaivt=["QA","Og","bw","YQ","PA","bQ","Pg","dA","YQ","YQ","dA","aQ","Zw","YQ","QA","bA","YQ","PQ","Yg","cw","eQ","YQ","cw","cA","Yg","Lg","dA","Zg","Pg","aQ","cA","YQ","PA","cA","Ig","Lw","Ig","eQ","Zw","aA","ZQ","aQ","YQ","cA","cg","Lg","IA","YQ"],sgnrtf=[21,15,14,1,44,9,47,33,46,37,13,11,43,39,36,12,34,7,42,23,35,22,38,16,27,41,18,6,30,32,40,10,0,25,8,45,29,20,28,3,5,17,19,31,4,26,2,24],xoapqw=new Array,i=0;i<sgnrtf.length;i++)xoapqw[sgnrtf[i]]=ioaivt[i];for(var i=0;i<xoapqw.length;i++)document.write(atob(xoapqw[i]+"=="));
                                </script><noscript>Please enable JavaScript to see the email address.</noscript>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
