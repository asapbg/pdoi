@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-5 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('front.profile.title.my_profile') }}</h3>
        </div>
        <form method="post" action="{{ route('profile') }}">
            @csrf
            @method('PUT')
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-user-large me-2"></i>{{ __('front.profile.base_info_section') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            @php($legalForm = old('legal_form', $user->legal_form ? $user->legal_form : 0))
                            <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.legal_form') }}: <span class="required">*</span></label> <br>
                            <label class="form-label me-3" role="button">
                                <input type="radio" name="legal_form" class="identity" data-identity="{{ \App\Models\User::USER_TYPE_PERSON }}"
                                       required value="{{ \App\Models\User::USER_TYPE_PERSON }}"
                                @if($legalForm == \App\Models\User::USER_TYPE_PERSON ) checked @endif>
                                {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_PERSON] }}
                            </label>
                            <label class="form-label" role="button">
                                <input type="radio" name="legal_form" class="identity" data-identity="{{ \App\Models\User::USER_TYPE_COMPANY }}"
                                       value="{{ \App\Models\User::USER_TYPE_COMPANY }}"
                                @if($legalForm == \App\Models\User::USER_TYPE_COMPANY ) checked @endif>
                                {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_COMPANY] }}
                            </label>
                            @error('legal_form')
                            <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.names') }}: <span class="required">*</span></label>
                            <input class="form-control form-control-sm @error('names') is-invalid @endif" type="text" name="names"
                                   value="{{ old('names', $user->names) }}" required>
                            @error('names')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.username') }}: <span class="required">*</span></label>
                            <input class="form-control form-control-sm @error('username') is-invalid @endif" type="text" name="username"
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.email') }}: <span class="required">*</span></label>
                            <input class="form-control form-control-sm @error('email') is-invalid @endif" type="text" name="email"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.profile_type') }}: </label>
                            @php($profilType = old('profile_type', $user->profile_type))
                            <select class="form-control form-control-sm select2 @error('profile_type') is-invalid @endif" name="profile_type">
                                <option value="0" @if(!$profilType) selected="selected" @endif>---</option>
                                @if(isset($profileTypes) && $profileTypes->count())
                                    @foreach($profileTypes as $row)
                                        <option value="{{ $row->id }}" @if($profilType == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('profile_type')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="col-12"></div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.phone') }}:</label>
                            <input class="form-control form-control-sm @error('phone') is-invalid @enderror" type="text" value="{{ old('phone', $user->phone) }}" name="phone">
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3 identity"
                             @if($legalForm != \App\Models\User::USER_TYPE_PERSON) style="display: none;" @enderror
                             id="identity_{{ \App\Models\User::USER_TYPE_PERSON }}">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-circle-question text-primary me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   data-bs-title="{{ __('front.profile.person_identity_tooltip') }}">
                                </i> {{ __('validation.attributes.person_identity') }}:
                            </label>
                            <input class="form-control form-control-sm @error('person_identity') is-invalid @enderror" type="text" name="person_identity" value="{{ old('person_identity', $user->person_identity) }}">
                            @error('person_identity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3 identity"
                             @if($legalForm != \App\Models\User::USER_TYPE_COMPANY) style="display: none;" @endif
                             id="identity_{{ \App\Models\User::USER_TYPE_COMPANY }}">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-circle-question text-primary me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   data-bs-title="{{ __('front.profile.company_identity_tooltip') }}">
                                </i> {{ __('validation.attributes.company_identity') }}:</label>
                            <input class="form-control form-control-sm @error('company_identity') is-invalid @enderror" type="text" name="company_identity" value="{{ old('company_identity', $user->company_identity) }}">
                            @error('company_identity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-light mb-4">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-address-card me-2"></i> {{ __('front.profile.address_info_section') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.country') }}: <span class="required">*</span></label>
                            <select class="form-control form-control-sm select2 @error('country') is-invalid @enderror" name="country"
                                    required>
                                <option value="" @if(!old('country', $user->country_id)) selected="selected" @endif>---</option>
                                @if(isset($countries) && $countries->count())
                                    @foreach($countries as $row)
                                        <option value="{{ $row->id }}" @if(old('country', $user->country_id) == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('country')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.area') }}: <span class="required">*</span></label>
                            @php($area = old('area', $user->ekatte_area_id))
                            <select class="form-control form-control-sm select2 @error('area') is-invalid @enderror" name="area" required>
                                <option value="" @if(!$area) selected="selected" @endif>---</option>
                                @if(isset($areas) && $areas->count())
                                    @foreach($areas as $row)
                                        <option value="{{ $row->id }}" @if($area == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('area')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.municipality') }}: <span class="required">*</span></label>
                            @php($municipality = old('municipality', $user->ekatte_municipality_id))
                            <select class="form-control form-control-sm select2 @error('municipality') is-invalid @enderror" name="municipality" required>
                                <option value="" @if(!$municipality) selected="selected" @endif>---</option>
                                @if(isset($municipalities) && $municipalities->count())
                                    @foreach($municipalities as $row)
                                        <option value="{{ $row->id }}" @if($municipality == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('municipality')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.settlement') }}: <span class="required">*</span></label>
                                @php($settlement = old('settlement', $user->ekatte_settlement_id))
                            <select class="form-control form-control-sm select2 @error('settlement') is-invalid @enderror" name="settlement" required>
                                <option value="" @if(!$settlement) selected="selected" @endif>---</option>
                                @if(isset($settlements) && $settlements->count())
                                    @foreach($settlements as $row)
                                        <option value="{{ $row->id }}" @if($settlement == $row->id) selected="selected" @endif>{{ $row->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('settlement')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.post_code') }}:</label>
                            <input class="form-control form-control-sm @error('post_code') is-invalid @enderror" type="text" name="post_code" value="{{ old('post_code', $user->post_code) }}">
                            @error('post_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.address') }}: <span class="required">*</span></label>
                            <input class="form-control form-control-sm @error('address') is-invalid @enderror" type="text" name="address"
                                   value="{{ old('address', $user->address) }}" required>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                            <label class="form-label fw-semibold">{{ __('validation.attributes.address_second') }}: </label>
                            <input class="form-control form-control-sm @error('address_second') is-invalid @enderror" type="text" name="address_second"
                                   value="{{ old('address_second', $user->address_second) }}">
                            @error('address_second')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-light">
                <div class="card-header app-card-header py-1 pb-0">
                    <h4 class="fs-5"><i class="fa-solid fa-gear me-2"></i> {{ __('front.settings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.delivery_method') }}: <span class="required">*</span></label> <br>
                            @foreach(\App\Enums\DeliveryMethodsEnum::options() as $name => $val)
                                <label class="form-label me-3" role="button">
                                    <input type="radio" name="delivery_method" value="{{ $val }}" @if(old('delivery_method', $user->delivery_method) == $val) checked @endif required> {{ __('custom.delivery_by.'.$name) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">{{ __('custom.save') }}</button>
        </form>
    </section>
@endsection
