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
{{--                Applications--}}
                @canany(['manage.*', 'application.*', 'application.view'])
                    <li class="nav-item">
                        <a href="{{ route('admin.application') }}"
                           class="nav-link @if(strstr(url()->current(), 'applications/')) active @endif">
                            <i class="fas fa-file-alt"></i>
                            <p>{{ trans_choice('custom.applications',2) }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.restore_requests') }}"
                           class="nav-link @if(strstr(url()->current(), 'restore-requests/')) active @endif">
                            <i class="fas fa-file-alt"></i>
                            <p>{{ trans_choice('custom.restore_request',2) }}</p>
                        </a>
                    </li>
                @endcanany
                <!-- RZS subjects and sections -->
                @canany(['manage.*', 'administration.*', 'administration.rzs_sections', 'administration.rzs_items'])
                    <li class="nav-item">
                        <a href="#" class="nav-link @if(strstr(url()->current(), 'rzs-')) active @endif">
                            <i class="nav-icon far fa-registered"></i>
                            <p>{{ __('custom.rzs_short') }}<i class="fas fa-angle-left right"></i></p>
                        </a>
                        @canany(['manage.*', 'administration.*', 'administration.rzs_sections'])
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="{{ route('admin.rzs.sections') }}"
                                       class="nav-link @if(strstr(url()->current(), 'rzs-sections')) active @endif">
                                        <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                        <p>{{ trans_choice('custom.rzs_sections', 1) }}</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                        @canany(['manage.*', 'administration.*', 'administration.rzs_items'])
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="{{ route('admin.rzs') }}"
                                       class="nav-link @if(strstr(url()->current(), 'rzs-subjects')) active @endif">
                                        <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                        <p>{{ trans_choice('custom.rzs_items', 1) }}</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                    </li>
                @endcanany
{{--                Public sections, menu and pages--}}
                @canany(['manage.*', 'menu_section.*', 'menu_section.section', 'menu_section.page'])
                    <li class="nav-item">
                        <a href="#" class="nav-link @if(str_contains(url()->current(), 'menu-section')) active @endif">
                            <i class="nav-icon fas fa-ellipsis-v"></i>
                            <p>{{ trans_choice('custom.menu_sections',2) }}<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            @canany(['manage.*', 'menu_section.*', 'menu_section.section'])
                                <li class="nav-item">
                                    <a href="{{ route('admin.menu_section') }}"
                                       class="nav-link @if(strstr(url()->current(), 'menu-section/section')) active @endif">
                                        <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                        <p>{{ trans_choice('custom.menu_section.sections', 2) }}</p>
                                    </a>
                                </li>
                            @endcan
                            @canany(['manage.*', 'menu_section.*', 'menu_section.page'])
                                <li class="nav-item">
                                    <a href="{{ route('admin.page') }}"
                                       class="nav-link @if(strstr(url()->current(), 'menu-section/page')) active @endif">
                                        <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                        <p>{{ trans_choice('custom.menu_section.pages', 2) }}</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                <!-- Mail templates -->
                @canany(['manage.*', 'administration.*', 'administration.mail_templates'])
                    <li class="nav-item">
                        <a href="{{ route('admin.mail_template') }}"
                           class="nav-link @if(strstr(url()->current(), 'mail-template/')) active @endif">
                            <i class="fas far fa-envelope"></i>
                            <p>{{ trans_choice('custom.mail_template',2) }}</p>
                        </a>
                    </li>
                @endcanany
{{--                Statistics--}}
                @canany(['manage.*', 'statistics.*'])
                    <li class="nav-item">
                        <a href="{{ route('admin.statistic') }}"
                           class="nav-link @if(str_contains(url()->current(), 'statistic')) active @endif">
                            <i class="fas fa-chart-line"></i>
                            <p>{{ trans_choice('custom.statistics',2) }}</p>
                        </a>
                    </li>
                @endcanany
{{--                Nomenlatures--}}
                @canany(['manage.*', 'administration.*', 'administration.system_classification'])
                    <li class="nav-header">{{ trans_choice('custom.nomenclatures', 2) }}</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link @if(Str::contains(url()->current(), ['nomenclature/country', 'nomenclature/area', 'nomenclature/municipality', 'nomenclature/settlement'])) active @endif">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>EKATTE<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.country') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/country')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.country', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.area') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/area')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.areas', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.municipality') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/municipality')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.municipalities', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.ekatte.settlement') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/settlement')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.settlements', 2) }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        @php($systemNomenclatureIsActive = Str::contains(url()->current(), ['nomenclature/profile-type'])
|| Str::contains(url()->current(), ['nomenclature/category'])
|| Str::contains(url()->current(), ['nomenclature/extend-terms'])
|| Str::contains(url()->current(), ['nomenclature/event'])
|| Str::contains(url()->current(), ['nomenclature/reason-refusal'])
|| Str::contains(url()->current(), ['nomenclature/no-consider-reason'])
|| Str::contains(url()->current(), ['nomenclature/change-decision-reason'])
)
                        <a href="#" class="nav-link @if($systemNomenclatureIsActive)) active @endif">
                            <i class="nav-icon fas fa-stream"></i>
                            <p>Системни<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.profile_type') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/profile-type')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.profile_type', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.category') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/category')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.category', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.extend_terms') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/extend-terms')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.extend_terms_reason', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.reason_refusal') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/reason-refusal')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.reason_refusal', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.no_consider_reason') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/no-consider-reason')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.no_consider_reason', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.change_decision_reason') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/change-decision-reason')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.change_decision_reason', 2) }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.nomenclature.event') }}"
                                   class="nav-link @if(strstr(url()->current(), 'nomenclature/event')) active @endif">
                                    <i class="fas fa-circle nav-icon nav-item-sub-icon"></i>
                                    <p>{{ trans_choice('custom.nomenclature.event', 2) }}</p>
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
                @canany(['manage.*', 'settings.*'])
                    <hr class="text-white">
                    <li class="nav-item">
                        <a href="{{ route('admin.settings') }}"
                           class="nav-link @if(strstr(url()->current(), 'settings')) active @endif">
                            <i class="fas fa-cogs"></i>
                            <p>{{ trans_choice('custom.settings', 1) }}</p>
                        </a>
                    </li>
                @endcanany
                @if(auth()->user()->hasRole([\App\Models\CustomRole::SUPER_USER_ROLE]))
                    <hr class="text-white">
                    <li class="nav-header">Support</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.support.notifications') }}"
                           class="nav-link">
                            <i class="fas fa-message"></i>
                            <p>Известия</p>
                        </a>
                    </li>
                @endcanany
            </ul>
        </nav>
    </div>
</aside>
