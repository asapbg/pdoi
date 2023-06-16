@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('front.application.title.apply') }}</h3>
        </div>
        <nav class="nav nav-pills flex-column flex-sm-row step-tabs mb-4">
            <a class="flex-sm-fill text-sm-center nav-link application-navigation-tab active first" aria-current="page" href="#info" id="tab-info">{{ __('front.application.step.info') }}</a>
            <a class="flex-sm-fill text-sm-center nav-link application-navigation-tab disabled mid" href="#rzs" id="tab-rzs">{{ __('front.application.step.rzs') }}</a>
            <a class="flex-sm-fill text-sm-center nav-link application-navigation-tab disabled last" href="" id="tab-apply">{{ __('front.application.step.send') }}</a>
        </nav>
        <div class="form-legend">
            <p class="d-inline-block">{!! __('front.application.legend.field_update_profile') !!}</p>
            <p class="d-inline-block ms-4">{!! __('front.application.legend.required_fields') !!}</p>
        </div>
        <form id="info" enctype="multipart/form-data">
            @csrf
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-user-large me-2"></i> {{ __('front.application') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            @php($legalForm = old('legal_form', $user->legal_form ? $user->legal_form : \App\Models\User::USER_TYPE_PERSON))
                            <label class="form-label me-3 fw-semibold">
                                {{ __('validation.attributes.legal_form') }}: <span class="required">*</span> @if(!$user->legal_form) <i class="fa-solid fa-user text-warning"></i> @endif
                            </label> <br>
                            <label class="form-label me-3" role="button">
                                <input type="radio" name="legal_form" class="identity @if($user->legal_form) disabled-item @endif" data-identity="{{ \App\Models\User::USER_TYPE_PERSON }}"
                                       required value="{{ \App\Models\User::USER_TYPE_PERSON }}"
                                       @if($legalForm == \App\Models\User::USER_TYPE_PERSON ) checked @endif
                                       @if($user->legal_form) disabled @endif>
                                {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_PERSON] }}
                            </label>
                            <label class="form-label" role="button">
                                <input type="radio" name="legal_form" class="identity @if($user->legal_form) disabled-item @endif" data-identity="{{ \App\Models\User::USER_TYPE_COMPANY }}"
                                       required value="{{ \App\Models\User::USER_TYPE_COMPANY }}"
                                       @if($legalForm == \App\Models\User::USER_TYPE_COMPANY ) checked @endif
                                       @if($user->legal_form) disabled @endif>
                                {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_COMPANY] }}
                            </label>
                            <span id="error-legal_form" class="text-danger d-inline-block w-100">@error('legal_form'){{ $message }}@endif</span>
                        </div>
                        <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                            <label class="form-label fw-semibold w-100" for="names">
                                {{ __('validation.attributes.names') }}: <span class="required">*</span>
                                @if(empty($user->names)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                                <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="names_publication" value="1"> {{ __('front.public') }}</span>
                            </label>
                            <input class="form-control form-control-sm @error('names') is-invalid @endif @if(!empty($user->names)) disabled-item @endif" type="text" id="names" name="names"
                                   value="{{ old('names', $user->names) }}" required @if(!empty($user->names)) disabled @endif>
                            <span id="error-names" class="text-danger">@error('names'){{ $message }}@endif</span>
                        </div>
                        <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                            <label class="form-label fw-semibold w-100">
                                {{ __('validation.attributes.email') }}: <span class="required">*</span>
                                @if(empty($user->email)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                                <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="email_publication" value="1"> {{ __('front.public') }}</span>
                            </label>
                            <input class="form-control form-control-sm @error('email') is-invalid @endif @if(!empty($user->email)) disabled-item @endif" type="text" name="email"
                                   value="{{ old('email', $user->email) }}" required @if(!empty($user->email)) disabled @endif>
                            <span id="error-email" class="text-danger">@error('email'){{ $message }}@endif</span>
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold w-100" for="phone">
                                {{ __('validation.attributes.phone') }}:

                                @if(empty($user->phone)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                                <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="phone_publication" value="1"> {{ __('front.public') }}</span>
                            </label>
                            <input class="form-control form-control-sm @error('phone') is-invalid @enderror @if(!empty($user->phone)) disabled-item @endif" type="text"
                                   value="{{ old('phone', $user->phone) }}" id="phone" name="phone" @if(!empty($user->phone)) disabled @endif>
                            <span id="error-phone" class="text-danger">@error('phone'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3 identity"
                             @if($legalForm != \App\Models\User::USER_TYPE_PERSON) style="display: none;" @enderror
                             id="identity_{{ \App\Models\User::USER_TYPE_PERSON }}">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-circle-question text-primary me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   data-bs-title="{{ __('front.profile.person_identity_tooltip') }}">
                                </i> {{ __('validation.attributes.person_identity') }}:
                                @if(empty($user->person_identity)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <input class="form-control form-control-sm @error('person_identity') is-invalid @enderror @if($user->person_identity) disabled-item @endif"
                                   type="text" name="person_identity" value="{{ old('person_identity', $user->person_identity) }}"
                                   @if($user->person_identity) disabled @endif>
                            <span id="error-person_identity" class="text-danger">@error('person_identity'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3 identity"
                             @if($legalForm != \App\Models\User::USER_TYPE_COMPANY) style="display: none;" @endif
                             id="identity_{{ \App\Models\User::USER_TYPE_COMPANY }}">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-circle-question text-primary me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   data-bs-title="{{ __('front.profile.company_identity_tooltip') }}">
                                </i> {{ __('validation.attributes.company_identity') }}:
                                @if(empty($user->company_identity)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <input class="form-control form-control-sm @error('company_identity') is-invalid @enderror"
                                   type="text" name="company_identity" value="{{ old('company_identity', $user->company_identity) }}"
                                   @if($user->person_identity) disabled @endif>
                            <span id="error-company_identity" class="text-danger">@error('company_identity'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-address-card me-2"></i> {{ __('front.application.address_info_section') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3 text-right">
                            <span class="text-primary ms-3 float-end fw-normal"><input type="checkbox" class="form-check-input" name="address_publication" value="1"> {{ __('front.public') }}</span>
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold" for="country">
                                {{ __('validation.attributes.country') }}: <span class="required">*</span>
                                @if(!$user->country) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <select class="form-control form-control-sm select2 @error('country') is-invalid @enderror @if($user->country) disabled-item @endif" name="country" id="country"
                                    required @if($user->country) disabled @endif>
                                @if($user->country)
                                    <option value="{{ $user->country->id }}" selected="selected" >{{ $user->country->name }}</option>
                                @else
                                    <option value="" @if(!old('country', $user->country_id)) selected="selected" @endif>---</option>
                                    @if(isset($data['countries']) && $data['countries']->count())
                                        @foreach($data['countries'] as $row)
                                            <option value="{{ $row->id }}" @if(old('country', $user->country_id) == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            <span id="error-country" class="text-danger">@error('country'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold" for="area">
                                {{ __('validation.attributes.area') }}: <span class="required">*</span>
                                @if(!$user->area) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            @php($area = old('area', $user->ekatte_area_id))
                            <select class="form-control form-control-sm select2 @error('area') is-invalid @enderror @if($user->area) disabled-item @endif" name="area" id="area-select"
                                    required @if($user->area) disabled @endif>
                                @if($user->area)
                                    <option value="{{ $user->area->id }}" selected="selected">{{ $user->area->ime }}</option>
                                @else
                                    <option value="" @if(!$area) selected="selected" @endif>---</option>
                                    @if(isset($data['areas']) && $data['areas']->count())
                                        @foreach($data['areas'] as $row)
                                            <option value="{{ $row->id }}" @if($area == $row->id) selected="selected" @endif
                                            data-code="{{ $row->code }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            <span id="error-area" class="text-danger">@error('area'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold" for="municipality">
                                {{ __('validation.attributes.municipality') }}: <span class="required">*</span>
                                @if(!$user->municipality) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            @php($municipality = old('municipality', $user->ekatte_municipality_id))
                            <select class="form-control form-control-sm select2 @error('municipality') is-invalid @enderror @if($user->municipality) disabled-item @endif" name="municipality" id="municipality-select"
                                    required @if($user->municipality) disabled @endif>
                                @if($user->municipality)
                                    <option value="{{ $user->municipality->id }}" selected="selected">{{ $user->municipality->ime }}</option>
                                @else
                                    <option value="" @if(!$municipality) selected="selected" @endif>---</option>
                                    @if(isset($data['municipality']) && $data['municipality']->count())
                                        @foreach($data['municipality'] as $row)
                                            <option value="{{ $row->id }}" @if($municipality == $row->id) selected="selected" @endif
                                            data-area="{{ substr($row->code, 0, 3) }}" data-code="{{ substr($row->code, -2) }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            <span id="error-municipality" class="text-danger">@error('municipality'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold" for="settlement">
                                {{ __('validation.attributes.settlement') }}: <span class="required">*</span>
                                @if(!$user->settlement) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            @php($settlement = old('settlement', $user->ekatte_settlement_id))
                                <select class="form-control form-control-sm select2 @error('settlement') is-invalid @enderror @if($user->settlement) disabled-item @endif" name="settlement" id="settlement-select"
                                        required @if($user->settlement) disabled @endif>
                                    @if($user->settlement)
                                        <option value="{{ $user->settlement->id }}" selected="selected">{{ $user->settlement->ime }}</option>
                                    @else
                                        <option value="" @if(!$settlement) selected="selected" @endif>---</option>
                                        @if(isset($data['settlements']) && $data['settlements']->count())
                                            @foreach($data['settlements'] as $row)
                                                <option value="{{ $row->id }}" @if($settlement == $row->id) selected="selected" @endif
                                                data-area="{{ $row->area }}" data-municipality="{{ substr($row->municipality, -2) }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                                <span id="error-settlement" class="text-danger">@error('settlement'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                            <label class="form-label fw-semibold" for="post_code">
                                {{ __('validation.attributes.post_code') }}:
                                @if(empty($user->post_code)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <input class="form-control form-control-sm @error('post_code') is-invalid @enderror @if(empty(!$user->post_code)) disabled-item @endif" type="text" name="post_code" id="post_code"
                                   value="{{ old('post_code', $user->post_code) }}" @if(empty(!$user->post_code)) disabled @endif>
                            <span id="error-post_code" class="text-danger">@error('post_code'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                            <label class="form-label fw-semibold" for="address">
                                {{ __('validation.attributes.address') }}: <span class="required">*</span>
                                @if(empty($user->address)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <input class="form-control form-control-sm @error('address') is-invalid @enderror @if(empty(!$user->address)) disabled-item @endif" type="text" name="address" id="address"
                                   value="{{ old('address', $user->address) }}" required @if(empty(!$user->address)) disabled @endif>
                                <span id="error-address" class="text-danger">@error('address'){{ $message }}@enderror</span>
                        </div>
                        <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                            <label class="form-label fw-semibold" for="address_second">
                                {{ __('validation.attributes.address_second') }}:
                                @if(empty($user->address_second)) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label>
                            <input class="form-control form-control-sm @error('address_second') is-invalid @enderror @if(empty(!$user->address_second)) disabled-item @endif" type="text" name="address_second" id="address_second"
                                   value="{{ old('address_second', $user->address_second) }}" @if(empty(!$user->address_second)) disabled @endif>
                            <span id="error-address_second" class="text-danger">@error('address_second'){{ $message }}@enderror</span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-question me-2"></i> {{ __('front.application.request_field.description') }}: <span class="required">*</span></h4>
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
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-file me-2"></i> {{ __('front.application.files_section') }}</h4>
                </div>
                <div class="card-body">
                    <p>{{ __('front.application.files_description') }}</p>
                    <span id="error-tmpFile" class="text-danger d-inline-block w-100"></span>
                    <table class="table table-light table-sm table-bordered table-responsive" id="attachFiles">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('front.file_name') }}</th>
                            <th>{{ __('front.description') }}</th>
                            <th>
                                <div>
                                    <label for="tmpFile" class="form-label p-0 m-0">
                                        <i class="fa-solid fa-upload text-primary p-1" role="button" data-bs-toggle="tooltip" data-bs-title="{{ __('front.upload_btn') }}"></i>
                                    </label>
                                    <input class="form-control d-none" type="file" name="tmpFile" id="tmpFile" data-container="attachFiles">
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card card-light">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-envelope me-2"></i> {{ __('front.application.answer_section') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label me-3 fw-semibold">
                                {{ __('validation.attributes.delivery_method') }}: <span class="required">*</span>
                                @if(!$user->delivery_method) <i class="fa-solid fa-user fs text-warning"></i> @endif
                            </label> <br>
                            @foreach(\App\Enums\DeliveryMethodsEnum::options() as $name => $val)
                                <label class="form-label me-3" role="button" for="delivery_method">
                                    <input type="radio" class="@if($user->delivery_method)  @error('delivery_method') is-invalid @enderror disabled-item @endif" name="delivery_method" value="{{ $val }}" @if(old('delivery_method', $user->delivery_method) == $val) checked @endif
                                    required @if($user->delivery_method) disabled @endif> {{ __('custom.delivery_by.'.$name) }}
                                </label>
                            @endforeach
                            <span id="error-delivery_method" class="text-danger d-inline-block w-100">@error('delivery_method'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary mt-3 nav-application apply-application" data-validate="info" data-next="rzs">{{ __('front.next_btn') }}</button>
        </form>
        <form id="rzs" class="d-none">
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-building me-2"></i> {{ __('front.application.pdoi_subject_section') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label me-3 fw-semibold" for="subjects">
                                {{ __('front.search_by_name_or_pick_from_list') }}
                                <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject"
                                        data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                        data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=1' }}">
                                    <i class="fa-solid fa-list"></i>
                                </button> :
                                <span class="required">*</span>
                            </label>
                            <select name="subjects[]" id="subjects" multiple="multiple" placeholder="{{ __('front.search_by_name') }}" style="width:100%;" class="form-control form-control-sm select2 @error('subjects') is-invalid @endif">
                                @if(isset($rzs) && $rzs->count())
                                    @foreach($rzs as $row)
                                        <option value="{{ $row->id }}" @if(in_array($row->id, old('subjects', []))) selected @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span id="error-subjects" class="text-danger">@error('subjects'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary mt-3 nav-application " data-prev="info">{{ __('front.back_btn') }}</button>
            <button type="button" class="btn btn-sm btn-primary mt-3 nav-application apply-application" data-validate="rzs" data-next="send">{{ __('front.next_btn') }}</button>
        </form>
        <div id="apply"></div>
        <input type="hidden" id="applicationUrl" value="{{ route('application.store') }}">
    </section>
@endsection
<link href="{{ asset('summernote/summernote-lite.min.css') }}" rel="stylesheet">
@push('scripts')
    <script src="{{ asset('summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('jquery-validation/localization/messages_' . app()->getLocale() . '.js') }}"></script>
@endpush
