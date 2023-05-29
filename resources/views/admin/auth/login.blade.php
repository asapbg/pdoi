@extends('layouts.auth')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <div class="d-flex flex-row">
                <img src="{{ asset('img/logo.png') }}" width="60" height="auto">
                <span class="align-self-center ml-2 text-left font-weight-bold">{{ mb_strtoupper(__('custom.council_ministers')) }}<br>{{ config('app.name') }}</span>
            </div>
            <span class="fs-6 font-italic d-block mt-4">{{ __('auth.administration') }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login.admin.submit') }}">
                @csrf

                @error('username')
                <div class="text-danger mt-1">
                    {{ $message }}
                </div>
                @enderror

                @error('error')
                <div class="text-danger mt-1">
                    {{ $message }}
                </div>
                @enderror

                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" required
                           @if(old('username')) value="{{ old('username') }} @else placeholder="{{ __('auth.username') }}" @endif">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-1">
                    <input type="password" name="password" class="form-control" required autocomplete="current-password"
                           @if(old('password')) value="{{ old('password') }} @else placeholder="{{ __('auth.password') }}" @endif">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                {{--If more then one guard is used in the app use this--}}
{{--                <div class="form-group d-none">--}}
{{--                    <select name="provider" id="provider" class="form-control">--}}
{{--                        <option value="ldap" @if(old('provider') == 'ldap') @endif>Активна директория(ActiveDirectory)</option>--}}
{{--                        <option value="db" @if(old('provider') == 'db') @endif selected>Вътрешен потребител</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
                <div class="row mb-3">
                    <div class="col-12 text-right">
                        <a href="#" class="font-italic">{{ __('auth.forgot_password_link') }}</a>
                    </div>
                </div>
                <div class="row mb-2">
{{--                    <div class="col-8">--}}
{{--                        <div class="icheck-primary">--}}
{{--                            <input type="checkbox" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}
{{--                            <label for="remember">--}}
{{--                                {{ __('validation.attributes.rememberme') }}--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('auth.login') }}</button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-8 col-12 text-md-left text-center ">
                        <a href="#">{{ __('auth.with_cert') }}</a>
                    </div>
{{--                    <div class="col-md-4 col-12 text-md-left text-center ">--}}
{{--                        <a href="#">{{ __('auth.registration') }}</a>--}}
{{--                    </div>--}}
                </div>

            </form>
        </div>
    </div>
@endsection
