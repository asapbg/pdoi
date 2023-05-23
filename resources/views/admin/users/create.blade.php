@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.users.store') }}" method="post" name="form" id="form">
                        @csrf

                        <div class="row">

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="username">
                                        {{ __('validation.attributes.username') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}">
                                        @error('username')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="first_name">
                                        {{ __('validation.attributes.first_name') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                               value="{{ old('first_name') }}">
                                        @error('first_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="middle_name">
                                        {{ __('validation.attributes.middle_name') }}
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="middle_name" name="middle_name" class="form-control"
                                               value="{{ old('middle_name') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="last_name">
                                        {{ __('validation.attributes.last_name') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                               value="{{ old('last_name') }}">
                                        @error('last_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="email">
                                        {{ __('validation.attributes.email') }}
                                    </label>
                                    <div class="col-12">
                                        <input type="email" id="email" name="email" class="form-control"
                                               value="{{ old('email') }}">
                                        @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group d-none">
                                    <span class="col-sm-12 control-label">&nbsp;</span>
                                    <div class="icheck-primary col-12">
                                        <input class="form-check-input" type="checkbox" name="must_change_password"
                                               id="must_change_password" {{ old('must_change_password') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="must_change_password">
                                            {{ __('validation.attributes.must_change_password') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="password">
                                        {{ __('validation.attributes.password') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="password" name="password" class="form-control passwords"
                                               autocomplete="new-password">
                                        <i>{{ __('auth.password_format') }}</i>
                                        @error('password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="password_confirmation">
                                        {{ __('validation.attributes.password_confirm') }}
                                    </label>
                                    <div class="col-12">
                                        <input type="password" name="password_confirmation" class="form-control passwords"
                                               autocomplete="new-password">
                                        @error('password_confirmation')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 pl-5">
                                <label class="control-label" for="roles">{{ trans_choice('custom.roles', 2) }}</label>
                                @foreach($roles as $role)
                                    <div class="icheck-primary">
                                        <input class="roles"
                                               type="checkbox"
                                               name="roles[]"
                                               id="role_{{ $role->id }}"
                                               value="{{ $role->id }}"
                                        >
                                        <label for="role_{{ $role->id }}">{{ $role->display_name }}</label>
                                    </div>
                                @endforeach
                                @error('roles')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route('admin.users') }}" class="btn btn-primary">{{ __('custom.cancel') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
