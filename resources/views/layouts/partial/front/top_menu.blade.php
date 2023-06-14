<nav class="navbar navbar-expand-lg bg-body-tertiary top-menu-nav py-0">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topMenuNav" aria-controls="topMenuNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-md-center" id="topMenuNav">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                <li class="nav-item px-1">
                    <a class="nav-link @if(request()->route()->getName() == 'home') active @endif" aria-current="page" href="{{ route('home') }}">Начало</a>
                </li>
                @if(auth()->user())
                    <li class="nav-item px-1">
                        <a class="nav-link @if(request()->route()->getName() == 'application.create') active @endif" href="{{ route('application.create') }}">Подаване на заявление</a>
                    </li>
                @endif
                @if(auth()->user())
                    <li class="nav-item px-1">
                        <a class="nav-link @if(request()->route()->getName() == 'application.my') active @endif" href="{{ route('application.my') }}">Моите заявления</a>
                    </li>
                @endif
                <li class="nav-item px-1">
                    <a class="nav-link" href="/search.html">Търсене</a>
                </li>
                <li class="nav-item px-1">
                    <a class="nav-link" href="#">Документи</a>
                </li>
                <li class="nav-item px-1">
                    <a class="nav-link" href="#">Статистика</a>
                </li>
                <li class="nav-item px-1">
                    <a class="nav-link" href="#">Контакти</a>
                </li>
{{--                <li class="nav-item dropdown">--}}
{{--                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                        Link--}}
{{--                    </a>--}}
{{--                    <ul class="dropdown-menu">--}}
{{--                        <li><a class="dropdown-item" href="#">Action</a></li>--}}
{{--                        <li><a class="dropdown-item" href="#">Another action</a></li>--}}
{{--                        <li><hr class="dropdown-divider"></li>--}}
{{--                        <li><a class="dropdown-item" href="#">Something else here</a></li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
            </ul>
        </div>
    </div>
</nav>
