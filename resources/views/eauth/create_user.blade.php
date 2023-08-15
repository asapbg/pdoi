@extends('layouts.app')

@section('content')
    <section class="content container w-25 pt-md-5 pt-2">
        <div class="card card-light mb-1">
            <div class="card-header app-card-header py-1 pb-0">
                <h4 class="fs-5">{{ __('eauth.with_e_auth') }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('eauth.user.create') }}">
                    @csrf
                    <input type="hidden" name="legal_form" value="{{ $userInfo['legal_form'] ?? 0 }}">
                    <input type="hidden" name="identity_number" value="{{ $userInfo['identity_number'] ?? '' }}">
                    <input type="hidden" name="name" value="{{ $userInfo['name'] ?? '' }}">
                    <input type="hidden" name="phone" value="{{ $userInfo['phone'] ?? '' }}">
                    <div class="row">
                        @if(isset($validationErrors) && sizeof($validationErrors))
                            <p class="text-danger fs-6">
                                @foreach($validationErrors as $err)
                                    <span class="d-inline-block mb-2">{{ $err[0] }}</span>
                                @endforeach
                            </p>
                        @endif
                    </div>
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" for="email">{{ __('eauth.label_email') }}: <span class="required">*</span></label>
                            <input class="form-control form-control-sm @if(isset($validationErrors) && isset($validationErrors['email'])) is-invalid @endif" type="email" value="{{ $userInfo['email'] ?? '' }}" name="email" id="email" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary mt-3">{{ __('eauth.register_with_this_mail') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
