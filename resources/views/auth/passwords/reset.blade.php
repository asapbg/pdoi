@extends('layouts.app')
@section('content')
    <section class="content container pt-md-5 pt-2" style="max-width: 400px;">
        <div class="card card-light mb-1">
            <div class="card-header app-card-header py-2">
                <h4 class="fs-5 m-0">{{ __('auth.reset_password') }}</h4>
            </div>
            <div class="card-body">
                @foreach(['success', 'warning', 'danger', 'info'] as $msgType)
                    @if(Session::has($msgType))
                        <div class="alert alert-{{$msgType}} mt-1" role="alert">{{Session::get($msgType)}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endforeach
                <form method="POST" action="{{ route('forgot_pass.password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group row">
                        <label for="email"
                               class="col-12 col-form-label">{{ __('auth.email') }}</label>

                        <div class="col-12">
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback text-danger">
                                 {{ $message }}
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password"
                               class="col-12 col-form-label">{{ __('validation.attributes.password') }}</label>

                        <div class="col-12">
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                   required autocomplete="new-password">

                            @error('password')
                            <span class="invalid-feedback text-danger">
                                 {{ $message }}
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm"
                               class="col-12 col-form-label">{{ __('validation.attributes.password_confirm') }}</label>

                        <div class="col-12">
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="mt-2 w-100 btn btn-primary">
                                {{ __('custom.update') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
