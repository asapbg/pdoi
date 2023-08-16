@extends('layouts.app')

@section('content')
    <section class="content container pt-md-5 pt-2" style="max-width: 400px;">
        <div class="card card-light mb-1">
            <div class="card-header app-card-header py-1 pb-0">
                <h4 class="fs-5">{{ __('custom.login') }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
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
                    <div class="row mt-2">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label for="username" class="form-label fw-semibold">{{ __('front.username_email') }}: <span class="required">*</span></label>
                            <input name="username" id="username" class="form-control form-control-sm" type="text" value="{{ old('username', '') }}" required>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label for="password" class="form-label fw-semibold">{{ __('validation.attributes.password') }}: <span class="required">*</span></label>
                            <input name="password" id="password" class="form-control form-control-sm" type="password" required>
                        </div>
                    </div>
                    <div class="row">
                        <a href="{{ route('forgot_pass') }}" class="font-italic">{{ __('auth.forgot_password_link') }}</a>
                        <a href="{{ route('eauth.login') }}">{{ __('eauth.with_e_auth') }}</a>
                        <a href="{{ route('register') }}" title="" class="d-inline-block col-12 text-left">{{ __('front.do_not_have_account') }}</a>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary mt-3">{{ __('custom.login') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
