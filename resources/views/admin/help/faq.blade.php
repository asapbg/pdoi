@extends('layouts.admin')

@section('title')
    {{__('custom.home')}}
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="row mt-3">
            <embed src="{{ asset(DIRECTORY_SEPARATOR.'help'.DIRECTORY_SEPARATOR.\App\Models\File::ADMIN_GUIDE_FILE) }}" width="800px" height="2100px" />
        </div>
    </div>
@endsection
