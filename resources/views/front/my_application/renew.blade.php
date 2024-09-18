@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $application['renew_title'] }}</h3>
        </div>

        <form enctype="multipart/form-data" id="renew_form">
            <input type="hidden" value="{{ $application['id'] }}" name="id">
            @csrf
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-2">
                    <h4 class="fs-5 m-0"><i class="fa-solid fa-file me-2"></i> {{ __('front.application.renew.files_section') }}</h4>
                </div>
                <div class="card-body">
                    <p>{{ __('front.application.files_description') }}</p>
                    <span id="error-tmpFile" class="text-danger d-inline-block w-100"></span>
                    <table class="table table-light table-sm table-bordered table-responsive align-middle" id="attachFiles">
                        <thead>
                        <tr>
                            <th>{{ __('front.file_name') }}</th>
                            <th>{{ __('front.description') }}</th>
                            <th>
                                <div>
                                    <label for="tmpFile" class="form-label p-0 m-0">
                                        <span class="btn btn-sm btn-primary" role="button"><i class="fas fa-plus"></i> {{ __('custom.add') }}</span>
                                        {{--                                        <i class="fa-solid fa-upload text-primary p-1" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __('front.upload_btn') }}"></i>--}}
                                    </label>
                                    <input class="form-control d-none do-not-ignore" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles">
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-2">
                    <h4 class="fs-5 m-0"><i class="fa-solid fa-info me-2"></i> {{ __('custom.additional_info') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" ></label>
                            @php($request = old('request', ''))
                            {{--                            {{ $request }}--}}
                            <textarea class="summernote w-100" name="request_summernote" id="request_summernote">{{ $request }}</textarea>
                            <input type="hidden" class="do-not-ignore" name="request" value="{{ $request }}" id="request">
                            <span id="error-request" class="text-danger">@error('request'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row mt-2">
                <div class="text-danger mb-2" id="error-apply"></div>
                <div class="col-md-6 col-md-offset-3">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">{{ __('custom.back') }}</a>
                    <button type="button" class="btn btn-sm btn-primary renew-application" data-validate="renew_form">{{ __('front.renew_request.btn') }}</button>
                </div>
            </div>
        </form>
        <input type="hidden" id="applicationUrl" value="{{ route('application.my.renew.store') }}">
    </section>
@endsection
<link href="{{ asset('summernote/summernote-lite.min.css') }}" rel="stylesheet">
@push('scripts')
    <script src="{{ asset('summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
@endpush
