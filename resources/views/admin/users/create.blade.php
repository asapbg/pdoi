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
                                @include('admin.users.partial.user_general_form')
                            </div>

                            <div class="col-md-6 col-sm-12 pl-5">
                                @include('admin.users.partial.edit_user_roles')
                                @include('admin.users.partial.permissions_tree')
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route('admin.users') }}" class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
