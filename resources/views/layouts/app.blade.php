<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{isset($title) ? $title .' - '.__('custom.full_app_name') : __('custom.full_app_name') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
{{--    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}

    <!-- Styles -->
    <link href="{{ asset('fontawesome-6.4.0/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <script type="text/javascript">
        var max_upload_file_size = parseInt(<?php echo config('filesystems.max_upload_file_size')?>) * 1024; //kb to b
        var allowed_file_extensions = '<?php echo(implode('|', \App\Models\File::ALLOWED_FILE_EXTENSIONS))?>';
    </script>
    @include('feed::links')
</head>
<body>
<div id="app">
    <header>
        @include('layouts.partial.front.header')
        @include('layouts.partial.front.top_menu')
    </header>
    <main class="px-md-5 px-3 pt-3 pb-md-5 pb-3">
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
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
