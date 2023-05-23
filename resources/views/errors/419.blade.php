@extends('layouts.admin')

@section('title')
    {{ __('custom.page_419_text') }}
@endsection

@section('header')
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}"><i class="fa fa-dashboard"></i> {{__('custom.home')}}</a></li>
        <li class="active">{{ __('custom.page_419_text') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container">
        <div class="col-xs-12 pt-4">
            <div class="alert alert-warning">
                {{ __('custom.page_419_text') }}
                <a href="{{ url('/') }}" class="btn btn-primary">{{ __('custom.here') }}</a>
            </div>
        </div>
    </div>
@endsection
