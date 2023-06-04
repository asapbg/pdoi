@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.users.update', $item->id) }}" method="post" name="form" id="form">
                        @csrf
                        @if(isset($item) && $item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ isset($item) && $item->id ? $item->id : 0 }}">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                @include('admin.users.partial.user_general_form')
                            </div>

                            <div class="col-md-6 col-sm-12 pl-5">
                                @include('admin.users.partial.edit_user_roles')
                                @include('admin.users.partial.permissions_tree')
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
