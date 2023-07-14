<nav class="navbar navbar-expand-lg main-nav">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <div class="d-flex">
                <img src="{{ asset('img/coat_arms.png') }}" width="110px" height="auto" alt="{{ __('custom.ministry') }}">
                <div class="project_name align-self-center ps-2">
                    <h1>{{ mb_strtoupper(__('custom.ministry')) }}</h1>
                    <h2 class="text-wrap">{{ __('custom.full_app_name') }}</h2>
                </div>
            </div>
        </a>
        <div class="navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 flex-row flex-wrap">
                @if(auth()->user())
                    <li class="nav-item">
                        <div id="front-timer">
                            @include('layouts.partial.count-down-timer')
                        </div>
                    </li>
                    <li class="nav-item d-md-none w-100"></li>
                @endif
                <li class="nav-item order-1 me-md-0 me-3">
                    @foreach(config('available_languages') as $locale)
                        @if(!$loop->first) | @endif
                        <a href="{{ route('change-locale', ['locale' => $locale['code']]) }}" class="nav-link d-inline-block @if(app()->getLocale() == $locale['code']) fw-bold @endif">{{ mb_strtoupper($locale['code']) }}</a>
                    @endforeach
                </li>
                @if(auth()->user())
                    <li class="nav-item order-md-2 order-5 flex-grow-1">
                        <a href="{{ route('profile') }}" class="nav-link"><i class="fa-solid fa-user text-primary me-1"></i>{{ auth()->user()->username }}</a>
                    </li>
                @else
                    <li class="nav-item order-4 me-md-0 me-1">
                        <a href="{{ route('login') }}" class="nav-link d-inline-block">{{ __('custom.login') }}</a> |
                        <a href="{{ route('register') }}" class="nav-link d-inline-block">{{ __('custom.register') }}</a>
                    </li>
                @endif
                <li class="nav-item order-md-3 order-2 me-md-0 me-1">
                    <a href="" class="nav-link"><i class="fa-solid fa-circle-question text-primary me-1" data-bs-toggle="tooltip" data-bs-title="{{ __('custom.instructions') }}"></i></a>
                </li>
                <li class="nav-item order-md-4 order-3 me-md-0 me-3">
                    <a href="" class="nav-link"><i class="fa-solid fa-eye-slash text-primary" data-bs-toggle="tooltip" data-bs-title="{{ __('custom.options_for_blind') }}"></i></a>
                </li>
                @if(auth()->user())
                    <li class="nav-item order-4 me-md-0 me-3">
                        <a href="{{ route('front.logout') }}" class="nav-link">{{ __('custom.logout') }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
