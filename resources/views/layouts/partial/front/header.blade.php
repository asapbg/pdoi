<nav class="navbar navbar-expand-lg main-nav">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="{{ route('home') }}">
            <div class="d-flex">
                <img src="{{ asset('img/coat_arms.png') }}" width="110px" height="auto" alt="{{ __('custom.ministry') }}">
                <div class="project_name align-self-center ps-2">
                    <h1>{{ mb_strtoupper(__('custom.ministry')) }}</h1>
                    <h2>{{ __('custom.full_app_name') }}</h2>
                </div>
            </div>
        </a>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item pull-right">
                    @foreach(config('available_languages') as $locale)
                        @if(!$loop->first) | @endif
                        <a href="{{ route('change-locale', ['locale' => $locale['code']]) }}" class="nav-link d-inline-block @if(app()->getLocale() == $locale['code']) fw-bold @endif">{{ mb_strtoupper($locale['code']) }}</a>
                    @endforeach
                </li>
                <li class="nav-item pull-right">
                    <a href="/profile.html" class="nav-link"><i class="fa-solid fa-user text-primary me-1"></i>k.ivanov</a>
                </li>
                <li class="nav-item pull-right">
                    <a href="/register.html" class="nav-link"><i class="fa-solid fa-circle-question text-primary me-1"></i></a>
                </li>
                <li class="nav-item pull-right">
                    <a href="/profile.html" class="nav-link"><i class="fa-solid fa-eye-slash text-primary"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>