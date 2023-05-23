@php
    $user = currentUser();
    $current_locale = app()->getLocale();
    $switch_locale = ($current_locale == 'bg') ? "en" : "bg";
@endphp
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                <span class="hidden-xs">{{ mb_strtoupper($current_locale) }}</span>
            </a>
            <ul class="dropdown-menu language dropdown-menu-left p-2">
                <li>
                    <a href="{{ route('change-locale', ['locale' => $switch_locale]) }}">
                        {{ mb_strtoupper($switch_locale) }}
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <div class="navbar-nav mx-auto">
        <h4>
            @if ($user)
                {{ $user->roles()->first()->display_name }}
            @endif
        </h4>
    </div>

    <ul class="navbar-nav ml-auto">
        <!-- User Account: style can be found in dropdown.less -->
        <li class="nav-item dropdown">
            @php
                $name = (!is_null($user)) ? $user->fullName() : "";
            @endphp
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                <span class="hidden-xs">{{ $name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
                <a class="dropdown-item dropdown-footer" href="javascript:;"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Изход <i class="fas fa-sign-out-alt"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

        </li>
        <!-- Control Sidebar Toggle Button -->
    </ul>
</nav>
