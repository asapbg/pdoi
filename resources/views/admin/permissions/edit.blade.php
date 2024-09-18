@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.permissions.update',$permission->id) }}" method="post" permission="form" id="form"
                          class="form-horizontal form-groups-bordered">
                        @csrf

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{ __('validation.attributes.alias') }} <span
                                    class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') ?? $permission->name }}">
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="display_name">{{ __('validation.attributes.name') }} <span
                                    class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="display_name" name="display_name" class="form-control" value="{{ old('display_name') ?? $permission->display_name }}">
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
