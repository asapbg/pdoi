@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    @php($storeRoute = route($storeRouteName, [$item]))
                    <form action="{{ $storeRoute }}" method="post" name="form" id="form">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">
                        <input type="hidden" name="full_edit" value="{{ (int)(isset($editOptions) && isset($editOptions['full']) && $editOptions['full']) }}">
                        @if(!$item->id || (isset($editOptions) && isset($editOptions['full']) && $editOptions['full']))
                            @include('admin.rzs.edit_general')
                            @include('admin.rzs.edit_addres_section')
                        @endif
                        @if(!$item->id || (isset($editOptions) && isset($editOptions['settings']) && $editOptions['settings']))
                            @include('admin.rzs.edit_settings_section')
                        @endif
                        @include('admin.rzs.users', ['users' => $item->users])
                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route($listRouteName) }}"
                                   class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
