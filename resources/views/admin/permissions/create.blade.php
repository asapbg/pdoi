@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.permissions.store') }}" method="post" name="form" id="form">
                        @csrf

                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="permission_group">
                                {{ __('permissions.group_permission') }} <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="permission_group" name="permission_group" class="form-control">
                                    <option value="" @if(old('permission_group', '') == '') selected @endif>{{ __('custom.permissions.as_new_group') }}</option>
                                    @if(isset($groups) && sizeof($groups))
                                        @foreach($groups as $g)
                                            <option value="{{ $g }}" @if(old('permission_group', '') == $g) selected @endif>{{ __('custom.'.$g) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('permission_group')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="name">
                                {{ __('validation.attributes.alias') }} <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}">
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="display_name">
                                {{ __('validation.attributes.name') }} <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="display_name" name="display_name" class="form-control" value="{{ old('display_name') }}">
                                @error('display_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route('admin.permissions') }}"  class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
