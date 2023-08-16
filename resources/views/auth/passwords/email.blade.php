@extends('layouts.app')
@section('content')
    <section class="content container pt-md-5 pt-2" style="max-width: 400px;">
        <div class="card card-light mb-1">
            <div class="card-header app-card-header py-1 pb-0">
                <h4 class="fs-5">{{ __('auth.reset_password') }}</h4>
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

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('forgot_pass.password.send') }}">
                    @csrf
                    <div class="form-group row">
                        <label for="email" class="col-md-12 col-form-label">{{ __('auth.email') }}</label>
                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-12">
                            <button type="submit" class="w-100 mt-2 btn btn-primary">
                                {{ __('auth.sent_reset_link') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
