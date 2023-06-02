<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Logo -->
    <a href="{{ route('admin.home') }}" class="brand-link">
        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="ml-2 brand-text"> {{ config('app.name') }}</span>
        <span class="ml-2 font-weight-light"></span>
    </a>

    @php
        $user = currentUser();
    @endphp
    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.home') }}"
                       class="nav-link @if(strstr(url()->current(), '/home')) active @endif">
                        <i class="fas fa-home"></i>
                        <p>{{ __('custom.home')  }}</p>
                    </a>
                </li>

                <!-- Admin -->
                @canany(['manage.*', 'administration.*', 'administration.pdoi_subjects'])
                    <li class="nav-item">
                        <a href="{{route('admin.pdo_subjects')}}"
                           class="nav-link @if(strstr(url()->current(), 'pdo-subjects')) active @endif">
                            <i class="far fa-registered"></i>
                            <p>{{ __('custom.pdoi_response_subjects_short') }}</p>
                        </a>
                    </li>
                @endcanany
                @canany(['manage.*', 'administration.*', 'administration.system_classification'])
                    <li class="nav-header">{{ trans_choice('custom.nomenclatures', 2) }}</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link @if(strstr(url()->current(), 'ekatte')) active @endif">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>EKATTE<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.area') }}"
                                   class="nav-link @if(strstr(url()->current(), 'ekatte/area')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.areas', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.municipality') }}"
                                   class="nav-link @if(strstr(url()->current(), 'ekatte/municipality')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.municipalities', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.settlement') }}"
                                   class="nav-link @if(strstr(url()->current(), 'ekatte/settlement')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.settlements', 2) }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany
                @canany(['manage.*', 'users.*', 'roles_permissions.*'])
                    <li class="nav-header">{{ trans_choice('custom.users', 2) }}</li>
                    @canany(['manage.*', 'roles_permissions.*'])
                        <li class="nav-item">
                            <a href="{{route('admin.roles')}}"
                               class="nav-link @if(strstr(url()->current(), 'roles')) active @endif">
                                <i class="fas fa-users"></i>
                                <p>{{ trans_choice('custom.roles', 2) }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.permissions')}}"
                               class="nav-link @if(strstr(url()->current(), 'permissions')) active @endif">
                                <i class="fas fa-gavel"></i>
                                <p>{{ trans_choice('custom.permissions', 2) }}</p>
                            </a>
                        </li>
                    @endcanany
                    @canany(['manage.*', 'users.*'])
                        <li class="nav-item">
                            <a href="{{route('admin.users')}}"
                               class="nav-link @if(strstr(url()->current(), 'users')) active @endif">
                                <i class="fas fa-user"></i>
                                <p>{{ trans_choice('custom.users', 2) }}</p>
                            </a>
                        </li>
                    @endcanany
                @endcanany
                @canany(['manage.*', 'administration.*', 'administration.activity_log'])
                    <li class="nav-header">{{ trans_choice('custom.activity_logs', 1) }}</li>
                    <li class="nav-item">
                        <a href="{{route('admin.activity-logs')}}"
                           class="nav-link @if(strstr(url()->current(), 'activity-logs')) active @endif">
                            <i class="fas fa-history"></i>
                            <p>{{ trans_choice('custom.activity_logs', 2) }}</p>
                        </a>
                    </li>
                @endcanany

                @if($user)
                    <li class="nav-header">Лични данни</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.profile.edit', $user->id) }}"
                           class="nav-link @if(strstr(url()->current(), 'users/profile/')) active @endif">
                            <i class="fas fa-user-cog"></i>
                            <p>{{ trans_choice('custom.profiles', 1) }}</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
