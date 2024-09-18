@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
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
                            <div class="form-group">
                                <label class="col-sm-12 control-label">{{ __('validation.attributes.files') }}</label>
                                Файла тук за сваляне
                            </div>
                        </div>
                        @if($item->status == \App\Models\PdoiApplicationRestoreRequest::STATUS_REGECTED)
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">Статус:</label>
                                    <div class="col-12">{{ __('custom.restore_request.status.'.$item->status) }}</div>
                                </div>
                            </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label">Отговор до заявителя:</label>
                                        <div class="col-12">{!! $item->reason_refuse !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($item->status == \App\Models\PdoiApplicationRestoreRequest::STATUS_APPROVED)
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">Статус:</label>
                                    <div class="col-12">{{ __('custom.restore_request.status.'.$item->status) }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <div class="form-group row">
                                <div class="col-md-6 col-md-offset-3">
                                    <a href="{{ route($listRouteName) }}"
                                       class="btn btn-primary">{{ __('custom.back') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </section>
@endsection
