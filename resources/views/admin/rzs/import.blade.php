@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
{{--            @include('admin.partial.filter_form')--}}
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="mb-3">
{{--                        @includeIf('partials.status', ['action' => 'App\Http\Controllers\Admin\PdoiResponseSubjectController@index'])--}}

                        <a href="{{ route('admin.rzs.import.download_example') }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fas fa-file-download mr-1"></i> {{ __('custom.rzs.download_import_example') }}
                        </a>
                    </div>
                    <form class="row" action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Изберете файл <span class="required">*</span> </label>
                            <input placeholder="Файл" class="form-control form-control-sm @error('file') is-invalid @enderror" id="file" type="file" name="file">
                            @error('file')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button id="save" type="submit" class="btn btn-success d-inline-block w-auto">{{ __('custom.import') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection


