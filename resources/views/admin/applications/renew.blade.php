@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    {{ __('custom.application.renew') }} {{ __('custom.reg_number') }}: {{ $application->application_uri }} ({{ __('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($application->status)) }})
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.application.renew.submit') }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="application" value="{{ $application->id }}">
                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2">{{ __('custom.application.renew.procedure_info') }}</h5>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label fw-semibold" disabled>{{ __('validation.attributes.decision') }}: <span class="required">*</span></label>
                                <select class="form-control form-control-sm @error('decision') is-invalid @enderror" name="decision" id="decision">
                                    <option></option>
                                    @foreach(\App\Enums\CourtDecisionsEnum::options() as $name => $val)
                                        <option data-reopen="{{ (int)\App\Enums\CourtDecisionsEnum::isReopenAvailable($val) }}" value="{{ $val }}" @if(old('decision', '') == $val) selected @endif>{{ __('custom.court_decision.'.$name) }}</option>
                                    @endforeach
                                </select>
                                @error('decision')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12"></div>
                            <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                <label class="form-label fw-semibold">{{ __('validation.attributes.file_decision') }}: <span class="required">*</span> </label>
                                <input name="file_decision" class="form-control form-control-sm @error('file_decision') is-invalid @enderror" type="file">
                                @error('file_decision')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.additional_info') }}</h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" ></label>
                                <textarea class="form-control summernote w-100 @error('add_text') is-invalid @enderror" name="add_text">{{ old('add_text', '') }}</textarea>
                                @error('add_text')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <br/>
                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" name="reopen" value="0" class="action-btn btn btn-success d-none" disabled="">{{ __('custom.apply') }}</button>
                                <button id="reopen" type="submit" name="reopen" value="1" class="action-btn btn btn-success d-none" disabled="">{{ __('custom.application.renew') }}</button>
                                <a href="{{ url()->previous() }}"
                                   class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
    <script type="text/javascript"  nonce="2726c7f26c">
        $(document).ready(function(){
            $('#decision').on('change', function(){
                let reopen = $('#decision option:selected').data('reopen');
                $('.action-btn').addClass('d-none').prop('disabled', true);
                if(typeof reopen != 'undefined') {
                    if(parseInt(reopen) == 1) {
                        $('#reopen').removeClass('d-none').prop('disabled', false);
                    } else {
                        $('#save').removeClass('d-none').prop('disabled', false);
                    }
                }
            });
        });
    </script>
@endpush
