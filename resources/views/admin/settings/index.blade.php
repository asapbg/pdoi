@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        @if(isset($sections) && sizeof($sections))
                            @foreach($sections as $s)
                                <li class="nav-item">
                                    <a class="nav-link @if($section == $s) active @endif" id="{{ $s }}-tab" data-toggle="pill" href="{{ route('admin.settings', ['section' => $s]) }}">{{ __('custom.settings.sections.'.$s) }}</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    @if(isset($settings) && $settings->count())
                        <form action="{{ route('admin.settings.store') }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="{{ $section }}">
                            @foreach($settings as $row)
                                <div class="form-group">
                                    <label class="control-label" for="active">{{ __('custom.settings.'.$row->name) }}</label>
                                    <div>
                                        <input name="{{ $row->name }}" value="{{ old($row->name, ($row->value)) }}" class="form-control form-control-sm @error($row->name){{ 'is-invalid' }}@enderror" type="{{ $row->type }}">
                                    @error($row->name)
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group row">
                                <div class="col-md-6 col-md-offset-3">
                                    <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <p>Не са открити записи</p>
                    @endif

                </div>
            </div>
        </div>
    </section>

@endsection


