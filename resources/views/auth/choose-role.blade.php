@extends('layouts.auth')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h1">WEB {{ __('custom.select')." ".trans_choice('custom.roles', 1) }}</span>
        </div>
        <div class="card-body">
            @php
                if (session()->has('user_sector_roles')) {
                    $user_sector_roles = session('user_sector_roles');
                } else {
                    $user = currentUser();
                    if ($user) {
                        $user_sector_roles = $user->getUserSectorRolesWithNames();
                    }
                }
            @endphp
            @if($user_sector_roles)
                @foreach($user_sector_roles as $sector_roles)
                    @if(!empty($sector_roles['sector_name']))
                        <span class="dropdown-header">{{ $sector_roles['sector_name'] }}</span>
                        <div class="dropdown-divider"></div>
                    @endif
                    @php
                        $roles = $sector_roles['roles'];
                    @endphp
                    @foreach($roles as $role)
                        <a href="{{ route('change-sector-role', [
                                            'sector' => $sector_roles['sector_id'],
                                            'storage_location_id' => $sector_roles['storage_location_id'],
                                            'role' => $role['role_id']
                                        ]) }}" class="dropdown-item">
                            {{ $role['role_name'] }}
                            <span class="float-right text-muted text-sm">
                                <i class="fas fa-arrow-alt-circle-right"></i>
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
@endsection
