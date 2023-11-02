@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    {{ __('custom.application.create') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.application.create') }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2">{{ __('custom.general_info') }}</h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label me-3 ">{{ __('validation.attributes.legal_form') }}: <span class="required">*</span></label> <br>
                                <label class="form-label me-3" role="button">
                                    <input value="{{ \App\Models\User::USER_TYPE_PERSON }}" type="radio" name="applicant_type" @if(old('applicant_type', '') == \App\Models\User::USER_TYPE_PERSON) checked @endif>
                                    {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_PERSON] }}
                                </label>
                                <label class="form-label" role="button">
                                    <input value="{{ \App\Models\User::USER_TYPE_COMPANY }}" type="radio" name="applicant_type" @if(old('applicant_type', '') == \App\Models\User::USER_TYPE_COMPANY) checked @endif>
                                    {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_COMPANY] }}
                                </label>
                                @error('applicant_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                <label class="form-label w-100">
                                    {{ __('validation.attributes.names') }}: <span class="required">*</span>
                                    <span class="text-primary ml-3 float-right fw-normal"><input type="checkbox" class="form-check-input" name="names_publication" value="1"> {{ __('front.public') }}</span>
                                </label>
                                <input class="form-control form-control-sm @error('full_names') is-invalid @enderror" type="text" name="full_names"
                                       value="{{ old('full_names', '') }}" >
                                @error('full_names')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                <label class="form-label w-100">
                                    {{ __('validation.attributes.email') }}:
                                    <span class="text-primary ml-3 float-right fw-normal"><input type="checkbox" class="form-check-input" name="email_publication" value="1"> {{ __('front.public') }}</span>
                                </label>
                                <input class="form-control form-control-sm @error('email') is-invalid @enderror" type="text" name="email"
                                       value="{{ old('email', '') }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label w-100" for="phone">
                                    {{ __('validation.attributes.phone') }}:
                                    <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="phone_publication" value="1"> {{ __('front.public') }}</span>
                                </label>
                                <input class="form-control form-control-sm @error('phone') is-invalid @enderror" type="text"
                                       value="{{ old('phone', '') }}" id="phone" name="phone" >
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label ">{{ trans_choice('custom.profile_type', 1)  }}: </label>
                                <select class="form-control form-control-sm @error('profile_type') is-invalid @enderror" name="profile_type">
                                    <option value="" @if(old('profile_type', '') == '') selected="selected" @endif>---</option>
                                    @if(isset($profileTypes) && $profileTypes->count())
                                        @foreach($profileTypes as $row)
                                            <option class="@if(old('applicant_type', '') == '') d-none @endif" value="{{ $row->id }}" data-legalform="{{ $row->legal_form }}" @if(old('profile_type', '') == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('profile_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <h5 class="bg-primary py-1 px-2">{{ __('front.application.address_info_section') }}</h5>
                            <div class="form-group form-group-sm col-12 mb-3 text-right">
                                <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="address_publication" value="1"> {{ __('front.public') }}</span>
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label" for="country_id">
                                    {{ __('validation.attributes.country') }}: <span class="required">*</span>
                                </label>
                                <select class="form-control form-control-sm select2 @error('country_id') is-invalid @enderror" name="country_id" id="country">
                                    <option value="" @if(old('country_id', '') == '') selected="selected" @endif>---</option>
                                    @if(isset($countries) && $countries->count())
                                        @foreach($countries as $row)
                                            <option value="{{ $row->id }}" @if(old('country_id', '') == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('country_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label" for="area_id">
                                    {{ __('validation.attributes.area') }}:
                                </label>
                                <select class="form-control form-control-sm select2 @error('area_id') is-invalid @enderror " name="area_id" id="area-select" >
                                    <option value="" @if(old('area_id', '') == '') selected="selected" @endif>---</option>
                                    @if(isset($areas) && $areas->count())
                                        @foreach($areas as $row)
                                            <option value="{{ $row->id }}" @if(old('area_id', '') == $row->id) selected="selected" @endif
                                            data-code="{{ $row->code }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('area_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label" for="municipality_id">
                                    {{ __('validation.attributes.municipality') }}:
                                </label>
                                <select class="form-control form-control-sm select2 @error('municipality_id') is-invalid @enderror " name="municipality_id" id="municipality-select">
                                        <option value="" @if(old('municipality_id', '') == '') selected="selected" @endif>---</option>
                                        @if(isset($municipality) && $municipality->count())
                                            @foreach($municipality as $row)
                                                <option value="{{ $row->id }}" @if(old('municipality_id', '') == $row->id) selected="selected" @endif
                                                data-area="{{ substr($row->code, 0, 3) }}" data-code="{{ substr($row->code, -2) }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                </select>
                                @error('municipality_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                <label class="form-label" for="settlement_id">
                                    {{ __('validation.attributes.settlement') }}:
                                </label>
                                <select class="form-control form-control-sm select2 @error('settlement') is-invalid @enderror" name="settlement_id" id="settlement-select">
                                    <option value="" @if(old('settlement_id', '') == '') selected="selected" @endif>---</option>
                                    @if(isset($settlements) && $settlements->count())
                                        @foreach($settlements as $row)
                                            <option value="{{ $row->id }}" @if(old('settlement_id', '') == $row->id) selected="selected" @endif
                                            data-area="{{ $row->area }}" data-municipality="{{ substr($row->municipality, -2) }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('settlement_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                <label class="form-label" for="post_code">
                                    {{ __('validation.attributes.post_code') }}:
                                </label>
                                <input class="form-control form-control-sm @error('post_code') is-invalid @enderror" type="text" name="post_code" id="post_code"
                                       value="{{ old('post_code', '') }}">
                                @error('post_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                <label class="form-label" for="address">
                                    {{ __('validation.attributes.address') }}:
                                </label>
                                <input class="form-control form-control-sm @error('address') is-invalid @enderror" type="text" name="address" id="address"
                                       value="{{ old('address', '') }}">
                                @error('address')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('front.application.request_field.description') }} <span class="required text-white">*</span></h5>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label" ></label>
                                @php($request = old('request', ''))
                                <textarea class="form-control summernote w-100 @error('request') is-invalid @enderror" name="request">{{ $request }}</textarea>
                                @error('request')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-9 mb-3">
                                <label class="form-label" >{{ trans_choice('custom.pdoi_response_subjects', 1) }}: <span class="required text-white">*</span></label>
                                <div class="col-12 d-flex flex-row">
                                    <div class="input-group">
                                        <select class="form-control form-control-sm select2"name="response_subject_id" id="subjects">
                                            <option value="" @if('' == old('response_subject_id', '')) selected @endif>--</option>
                                            @if(isset($subjects) && sizeof($subjects))
                                                @foreach($subjects as $option)
                                                    <option value="{{ $option['value'] }}" @if($option['value'] == old('response_subject_id', '')) selected @endif>{{ $option['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('response_subject_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject rounded"
                                            data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                            data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&&admin=1' }}">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.decision') }} <span class="required text-white">*</span></h5>
                            <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                <label class="form-label fw-semibold" >{{ __('custom.event.FINAL_DECISION') }}: <span class="required">*</span></label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value=""></option>
                                    @foreach(\App\Enums\PdoiApplicationStatusesEnum::finalStatuses() as $status)
                                        @if(!in_array($status->value, [\App\Enums\PdoiApplicationStatusesEnum::FORWARDED->value, \App\Enums\PdoiApplicationStatusesEnum::INFO_NOT_EXIST->value]))
                                            <option @if(old('status', '') == $status->value) selected @endif value="{{ $status->value }}">{{ __('custom.application.status.'.$status->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label" ></label>
                                @php($response = old('response', ''))
                                <textarea class="form-control summernote w-100 @error('response') is-invalid @enderror" name="response">{{ $response }}</textarea>
                                @error('response')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                            <table class="table table-light table-sm table-bordered mb-4" id="attachFiles">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('front.file_name') }}</th>
                                    <th>{{ __('front.description') }}</th>
                                    <th>
                                        <div>
                                            <label for="tmpFile" class="form-label p-0 m-0">
                                                <i class="fas fa-upload text-primary p-1" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __('front.upload_btn') }}"></i>
                                            </label>
                                            <input class="form-control d-none" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles" data-admin="1" data-visibleoption="1">
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <br/>
                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ url()->previous() }}"
                                   class="btn btn-primary">{{ __('custom.cancel') }}</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function (){
            $('input[name="applicant_type"]').on('change', function (){
                let legalForm = parseInt($('input[name=applicant_type]:checked').val());
                $('select[name="profile_type"] option').each(function (index, el){
                    if(parseInt($(el).data('legalform')) === legalForm) {
                        $(el).removeClass('d-none');
                    } else{
                        $(el).addClass('d-none');
                    }
                });
                $('select[name="profile_type"]').val('');
            });
        });
    </script>
@endpush
