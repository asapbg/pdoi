@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @php($storeRoute = route($storeRouteName, ['id' => ($item->id ?? 0)]))
                    <form action="{{ $storeRoute }}" method="{{ $item->id ? 'put' : 'post' }}" name="form" id="form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">
                            @include('admin.pdoi_subjects.edit_general')
                            @include('admin.pdoi_subjects.edit_addres_section')
                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route($listRouteName) }}"
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
