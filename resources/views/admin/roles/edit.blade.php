@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.roles.general_info') }}</h5>
                    <form class="row" action="{{ route('admin.roles.update',$role->id) }}" method="post" name="form" id="form">
                            @csrf
                            <div class="form-group row col-12 col-md-6">
                                <label class="col-md-4 control-label" for="display_name">
                                    {{ __('validation.attributes.name') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="display_name" class="form-control form-control-sm"
                                           value="{{ old('display_name') ?? $role->display_name }}">
                                    @error('display_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row col-12 col-md-6">
                                <label class="col-md-4 control-label" for="name">
                                    {{ __('validation.attributes.alias') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="alias" class="form-control form-control-sm"
                                           value="{{ old('alias') ?? $role->name }}">
                                    @error('alias')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-12 mt-2">
{{--                                <div class="col-md-6 col-md-offset-3">--}}
                                    <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                    <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('custom.cancel') }}</a>
{{--                                </div>--}}
                            </div>
                            <br/>
                        </form>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5 class="bg-primary py-1 px-2 mt-5 mb-3">{{ __('custom.roles.users_in_group') }}</h5>
                            @if($roleUsers->count())
                                <form action="{{ route('admin.roles.users.remove', ['role' => $role->id]) }}" method="post" class="row">
                                    @csrf
                                    <div class="form-group col-12">
                                        {{--                                            @dd($roleUsers->count())--}}
                                        <select class="form-control form-control-sm @error('remove_users'){{ 'is-invalid' }}@endif" name="remove_users[]" multiple="">
                                            @foreach($roleUsers as $u)
                                                <option value="{{ $u->id }}">{{ $u->email.' ('. $u->names .')' }}</option>
                                            @endforeach
                                        </select>
                                        @error('remove_users')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12">
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('custom.remove') }}</button>
                                    </div>
                                </form>
                            @else
                                <p class="font-italic">{{ __('custom.roles.not_assigned') }}</p>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="bg-primary py-1 px-2 mt-5 mb-3">{{ __('custom.roles.add.users') }}</h5>
                            @if(isset($usersToAdd) && $usersToAdd->count())
                                <form action="{{ route('admin.roles.users.add', ['role' => $role->id]) }}" method="post" class="row">
                                    @csrf
                                    <div class="form-group col-12">
                                        <select class="form-control form-control-sm @error('add_users'){{ 'is-invalid' }}@endif" name="add_users[]" multiple="">
                                            @foreach($usersToAdd as $u)
                                                <option value="{{ $u->id }}">{{ $u->email.' ('. $u->names .')' }}</option>
                                            @endforeach
                                        </select>
                                        @error('add_users')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        </div>
                                    <div class="form-group col-12">
                                        <button type="submit" class="btn btn-sm btn-success">{{ __('custom.add') }}</button>
                                    </div>
                                </form>
                            @else
                                <p class="font-italic">{{ __('custom.roles.cant_find_available_users') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
