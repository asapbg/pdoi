@extends('layouts.app')

@section('content')
    <section class="content container w-50 pt-md-5 pt-2">
        <div class="card card-light mb-1">
            <div class="card-header app-card-header py-2">
                <h4 class="fs-5 m-0">{{ __('auth.verify_email') }}</h4>
            </div>
            <div class="card-body">
                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ __('auth.fresh_link_is_send') }}
                    </div>
                @endif
                {{ __('auth.verification_link') }}<br>
                    <hr>
                {{ __('auth.receive_email') }}
                <form class="pt-2" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <div class="form-group form-group-sm col-12 mb-3">
                        <label class="form-label fw-semibold" for="email">{{ __('validation.attributes.email') }}:</label>
                        <input class="form-control form-control-sm @error('email') is-invalid @enderror" type="text" value="{{ old('email', '') }}" name="email" id="email" required autocomplete="off">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('auth.resend_link') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
