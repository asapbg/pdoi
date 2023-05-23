@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.users.update', $user->id) }}" method="post" name="form" id="form">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="username">
                                        {{ __('validation.attributes.username') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="username" name="username" class="form-control"
                                               value="{{ old('username') ?? $user->username }}">
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
                                               value="{{ old('first_name') ?? $user->first_name }}">
                                        @error('first_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="middle_name">
                                        {{ __('validation.attributes.middle_name') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="middle_name" name="middle_name" class="form-control"
                                               value="{{ old('middle_name') ?? $user->middle_name }}">
                                        @error('middle_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="last_name">
                                        {{ __('validation.attributes.last_name') }}<span class="required">*</span>
                                    </label>
                                    <div class="col-12">
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                               value="{{ old('last_name') ?? $user->last_name }}">
                                        @error('last_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="email">{{ __('validation.attributes.email') }}</label>
                                    <div class="col-12">
                                        <input type="email" id="email" name="email" class="form-control"
                                               value="{{ old('email') ?? $user->email }}">
                                        @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label"
                                           for="password">{{ __('validation.attributes.password') }}</label>
                                    <div class="col-12">
                                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                                        <i>{{ __('auth.password_format') }}</i>
                                        @error('password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label"
                                           for="password_confirmation">{{ __('validation.attributes.password_confirm') }}</label>
                                    <div class="col-12">
                                        <input type="password" name="password_confirmation" class="form-control"
                                               autocomplete="new-password">
                                        @error('password_confirmation')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="active">
                                        {{ __('custom.active_m') }}?
                                    </label>
                                    <div class="col-12">
                                        <div class="icheck-primary d-inline mr-3">
                                            <input type="radio" name="active" id="active_1" value="1"
                                                    @if(old('_token') && old('active') == 1)
                                                       checked="checked"
                                                    @elseif($user->active == 1)
                                                        checked="checked"
                                                    @endif
                                            >
                                            <label for="active_1">Да</label>
                                        </div>
                                        <div class="icheck-primary  d-inline ">
                                            <input type="radio" name="active" id="active_0" value="0"
                                                    @if(old('_token') && old('active') == 0)
                                                       checked="checked"
                                                    @elseif($user->active == 0)
                                                        checked="checked"
                                                    @endif
                                            >
                                            <label for="active_0">Не</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="col-sm-12 control-label" for="activity_status">
                                        Блокиран?
                                    </label>
                                    <div class="col-12">
                                        <div class="icheck-primary d-inline mr-3">
                                            <input type="radio" name="activity_status" id="activity_status_{{ App\Models\User::STATUS_BLOCKED }}"
                                                   value="{{ App\Models\User::STATUS_BLOCKED }}"
                                                    @if(old('_token') && old('activity_status') == App\Models\User::STATUS_BLOCKED)
                                                       checked="checked"
                                                    @elseif($user->status == App\Models\User::STATUS_BLOCKED)
                                                        checked="checked"
                                                    @endif
                                            >
                                            <label for="activity_status_{{ App\Models\User::STATUS_BLOCKED }}">Да</label>
                                        </div>
                                        <div class="icheck-primary  d-inline ">
                                            <input type="radio" name="activity_status" id="activity_status_{{ App\Models\User::STATUS_ACTIVE }}"
                                                   value="{{ App\Models\User::STATUS_ACTIVE }}"
                                                    @if(old('_token') && old('activity_status') !== App\Models\User::STATUS_BLOCKED)
                                                       checked="checked"
                                                    @elseif($user->status != App\Models\User::STATUS_BLOCKED)
                                                        checked="checked"
                                                    @endif
                                            >
                                            <label for="activity_status_{{ App\Models\User::STATUS_ACTIVE }}">Не</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 col-sm-12 pl-5">
                                <label class="control-label" for="roles">{{ trans_choice('custom.roles', 2) }}</label>
                                @php
                                    $user_roles = $user->roles()->pluck('id')->toArray();
                                @endphp
                                @foreach($roles as $role)
                                    <div class="icheck-primary">
                                        <input class="roles"
                                               type="checkbox"
                                               name="roles[]"
                                               id="role_{{ $role->id }}"
                                               value="{{ $role->id }}"
                                               @if (in_array($role->id, $user_roles)) checked @endif
                                        >
                                        <label for="role_{{ $role->id }}">{{ $role->display_name }}</label>
                                    </div>
                                @endforeach
                                @error('roles')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>


                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route('admin.users') }}"
                                   class="btn btn-primary">{{ __('custom.cancel') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
