<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($seo_title) && !empty($seo_title) ? $seo_title : __('seo.default_title') }}</title>
    <meta name="description" content="{{ $seo_description ?? __('seo.default_description') }}">
    <meta name="keywords" content="{{ $seo_keywords ?? __('seo.default_keywords') }}">

{{--    <meta property="og:url"           content="https://www.your-domain.com/your-page.html" />--}}
{{--    <meta property="og:type"          content="website" />--}}
{{--    <meta property="og:title"         content="Your Website Title" />--}}
{{--    <meta property="og:description"   content="Your description" />--}}
{{--    <meta property="og:image"         content="https://www.your-domain.com/path/image.jpg" />--}}
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
{{--    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}

    <!-- Styles -->
    <link href="{{ asset('fontawesome-6.4.0/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <script type="text/javascript"  nonce="2726c7f26c">
        var max_upload_file_size = parseInt(<?php echo config('filesystems.max_upload_file_size')?>) * 1024; //kb to b
        var allowed_file_extensions = '<?php echo(implode('|', \App\Models\File::ALLOWED_FILE_EXTENSIONS))?>';
        var max_upload_file = parseInt(<?php echo config('filesystems.max_file_uploads')?>);
        var vo_font_percent = parseInt('<?php echo($vo_font_percent)?>');
        var vo_high_contrast = parseInt('<?php echo($vo_high_contrast)?>');
        var vo_ajax = false;
        var GlobalLang = "{{ str_replace('_', '-', app()->getLocale()) }}";
    </script>
    @include('feed::links')
</head>
<body class="@if($vo_high_contrast) high-contrast @endif">
-
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v17.0"></script>
<!-- END Load Facebook SDK for JavaScript -->

<div id="app">
    <header>
        @include('layouts.partial.front.header')
        @include('layouts.partial.front.top_menu')
    </header>
    <div class="container-fluid py-1 px-4 social d-flex justify-content-between align-items-center" id="top-panel">
        @include('partials.breadcrumbs_front')
        <div class="d-flex justify-content-center align-items-center">
            <div class="fb-share-button me-1"
                 data-href="{{ request()->url() }}"
                 data-layout="button" data-size="small">
                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore"></a>
            </div>
            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button  me-1" data-lang="en" data-show-count="false"></a>
            <a href="{{ config('feed.feeds.main.url') }}" id="rss-link"
               class="text-decoration-none  me-1"
               target="_blank" title="{{ __('custom.subscribe') }}"><span class="d-none">RSS</span>
                <i class="fas fa-rss-square mr-2 bg-white"></i>
            </a>
        </div>
    </div>
    <main class="px-md-5 px-3 pt-3 pb-md-3 pb-3 @if(isset($mainClass)) {{ $mainClass }} @endif">
        @foreach(['success', 'warning', 'danger', 'info'] as $msgType)
            @if(Session::has($msgType))
                <div class="alert alert-{{$msgType}} mt-1 alert-dismissible py-2" role="alert">{!! Session::get($msgType) !!}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        @yield('content')
    </main>
    @include('layouts.partial.front.footer')
</div>
<!-- Scripts -->
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<script src="{{ asset('js/app.js') }}" nonce="2726c7f26c"></script>
@if($vo_font_percent)
<script type="text/javascript"  nonce="2726c7f26c">
   $(document).ready(function (){
       setDomElFontSize(vo_font_percent, true);
   });
</script>
@endif
@stack('scripts')
<div id="visual-option-div" class="collapse p-3">
    <ul class="p-0 m-0">
        <li class="d-flex flex-row justify-content-between">
            <span>{{ __('custom.options_for_blind') }}</span>
            <i class="fas fa-close text-secondary" id="vo-close" role="button"></i>
        </li>
        <hr class="m-0">
        <li class="visual-option vo-increase-text py-2" role="button"><i class="text-primary me-2">A+</i>{{ __('custom.increase_text') }}</li>
        <li class="visual-option vo-decrease-text py-2" role="button"><i class="text-primary me-2">A-</i>{{ __('custom.decrease_text') }}</li>
        <li class="visual-option vo-contrast py-2" role="button" id="vo-contrast"><i class="fa-solid fa-palette text-primary me-2"></i><span class="height @if($vo_high_contrast) d-none @endif">{{ __('custom.height_contrast') }}</span><span class="low @if(!$vo_high_contrast) d-none @endif">{{ __('custom.low_contrast') }}</span></li>
        <li class="visual-option vo-reset py-2" role="button"><i class="fas fa-sync-alt text-primary me-2"></i>{{ __('custom.clear') }}</li>
    </ul>
</div>
</body>
</html>
