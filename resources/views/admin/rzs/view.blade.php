@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-4">
                        <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
                        <div class="col-12 mt-1">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">{{ __('validation.attributes.eik') }}<span class="required">*</span></label>
                                <div class="col-12">
                                    {{ $item->eik ?? '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="form-group">
                                <label class="col-sm-12 control-label"">{{ __('validation.attributes.adm_level') }}<span class="required">*</span></label>
                                <div class="col-12">{{ $item->section ? $item->section->name : '---'}}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">{{ __('validation.attributes.status') }}</label>
                               <div class="col-12">{{ $item->active ? 'Активен' : 'Неактивен' }}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">{{ __('validation.attributes.name') }}</label>
                                <div class="col-12">{{ $item->subject_name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.rzs.address_section') }}</h5>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="adm_level">{{ __('validation.attributes.area') }}<span class="required">*</span></label>
                                <div class="col-12">{{ $item->regionObj ? $item->regionObj->ime : '---'}}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="municipality">{{ __('validation.attributes.municipality') }}<span class="required">*</span></label>
                                <div class="col-12">{{ $item->municipalityObj? $item->municipalityObj->ime : '---' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="municipality">{{ __('validation.attributes.settlement') }}<span class="required">*</span></label>
                                <div class="col-12">{{ $item->townObj ? $item->townObj->ime : '---'}}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="municipality">{{ __('validation.attributes.address') }}<span class="required">*</span></label>
                                <div class="col-12">{{ $item->address }}</div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="zip_code">{{ __('validation.attributes.zip_code') }}</label>
                                <div class="col-12">{{ $item->zip_code ?? '' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="phone">{{ __('validation.attributes.phone') }}</label>
                                <div class="col-12">{{ $item->phone }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="fax">{{ __('validation.attributes.fax') }}</label>
                                <div class="col-12">{{ $item->fax }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="email">{{ __('validation.attributes.email') }}</label>
                                <div class="col-12">{{ $item->email }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label" for="email">{{ __('validation.attributes.add_info') }}</label>
                                <div class="col-12">{{ $item->add_info }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.rzs.settings_section') }}</h5>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="col-12">
                                    <div class="form-group form-group-sm col-12 mb-3">
                                        <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.rzs_delivery_method') }}: <span class="required">*</span></label> <br>
                                        {{ (int)$item->delivery_method > 0 ? __('custom.rzs.delivery_by.'.\App\Enums\PdoiSubjectDeliveryMethodsEnum::keyByValue($item->delivery_method)) : '---'}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin.rzs.users', ['users' => $item->users])

                </div>
            </div>
        </div>
    </section>
@endsection
