@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.restore_requests.reject') }}" method="post" name="form" id="form">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">

                        <div class="row mb-4">
{{--                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.additional_info') }}</h5>--}}
                            @if(!empty($item->user_request))
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label">{{ __('custom.additional_info') }}</label>
                                        <div class="col-12">
                                            {!! $item->user_request !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                                <table class="table table-light table-sm table-bordered mb-4">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('front.file_name') }}</th>
                                        <th>{{ __('front.description') }}</th>
                                        <th>{{ __('custom.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($item->files->count())
                                        @foreach($item->files as $key => $file)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $file->filename }}</td>
                                                <td>{{ $file->description }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-secondary" type="button" href="{{ route('admin.download.file', ['file' => $file->id]) }}">
                                                        <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                           data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">Отговор (при отказ ще бъде изпратено по ел. поща до заявителя)</label>
                                    <div class="col-12">
                                        @php($request = old('answer', ''))
                                        <textarea class="form-control summernote w-100 @error('answer') is-invalid @enderror" name="answer" >{{ $request }}</textarea>
                                        @error('answer')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            @if($item->status == \App\Models\PdoiApplicationRestoreRequest::STATUS_IN_PROCESS)
                                <div class="col-md-6 col-md-offset-3">
{{--                                    <button id="save" type="submit" class="btn btn-danger">{{ __('custom.restore_request.reject_btn') }}</button>--}}
                                    <input type="submit" class="btn btn-danger d-none" id="confirmRejectRenewModalSubmit" value="{{ __('custom.restore_request.reject_btn') }}" />
                                    <input type="button" class="btn btn-danger confirmRejectRenewModal" value="{{ __('custom.restore_request.reject_btn') }}" />
                                    @canany('renew', $item->application)
                                        <a href="{{ route( 'admin.application.renew' , [$item->application->id]) }}"
                                           class="btn btn-success"
                                           data-toggle="tooltip"
                                           title="{{ __('custom.renew') }}">
                                            <i class="fas fa-gavel"></i> {{ __('custom.renew') }}
                                        </a>
                                    @endcan
                                    <a href="{{ route($listRouteName) }}"
                                       class="btn btn-primary">{{ __('custom.back') }}</a>
                                </div>
                            @endif
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
