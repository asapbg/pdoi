@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row mb-4">
                        @php($activity = $item['activity'])
                        @php($notification = $item['notification'])
                        @php($jsonActivityPropertiesData = $activity->properties)
                        <h5 class="bg-primary py-1 px-2">{{ __('custom.'.$activity->event) }}</h5>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Тип събитие:</label>
                            @if(in_array($activity->event, ['send_to_seos', 'notify_moderators_for_new_app', 'success_check_status_in_seos', 'error_send_to_seos', 'error_check_status_in_seos', 'success_send_to_seos']))
                                <span>Комуникационно</span>
                            @endif
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Дата/час на настъпване:</label>
                            <span>{{ displayDateTime($activity->created_at) }}</span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Статус:</label>
                            <span>
                                @if(in_array($activity->event, ['send_to_seos', 'notify_moderators_for_new_app', 'success_check_status_in_seos', 'success_send_to_seos']))
                                        Успешно
                                    @elseif(in_array($activity->event, ['error_check_status_in_seos', 'error_send_to_seos']))
                                        Неуспешно
                                @endif
                            </span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Канал:</label>
                            <span>
                                @if(in_array($activity->event, ['error_check_status_in_seos', 'success_check_status_in_seos', 'send_to_seos', 'error_send_to_seos', 'success_send_to_seos']))
                                        {{  __('custom.rzs.delivery_by.'.\App\Enums\PdoiSubjectDeliveryMethodsEnum::SEOS->name) }}
                                @endif
                            </span>
                        </div>
                        @if(isset($item['egov_message']))
                            @php($egovM = $item['egov_message'])
                            @php($zrsLabel = in_array($activity->event, ['error_check_status_in_seos', 'success_check_status_in_seos', 'error_send_to_seos', 'success_send_to_seos']) ? 'Деловодна система' : 'Получател')
                            @if(isset($notification) && in_array($activity->event, ['error_check_status_in_seos', 'success_check_status_in_seos', 'error_send_to_seos', 'success_send_to_seos']))
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label fw-semibold" >{{ $zrsLabel }}:</label>
                                    <a href="{{ route('admin.rzs.view', $notification->notifiable->id) }}">{{ $notification->notifiable->subject_name }}</a>
                                </div>
                            @endif
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >{{ $zrsLabel }} (ЕИК):</label>
                                <span>{{ $egovM->recipient_eik }}</span>
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >{{ $zrsLabel }} (GUID):</label>
                                <span>{{ $egovM->recipient_guid }}</span>
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >{{ $zrsLabel }} (URL):</label>
                                @if(!isset($jsonActivityPropertiesData['recipient_endpoint']))
                                    @php($service= $egovM->recipient?->services->first())
                                    @if($service)
                                        @php($endpoint = '* '.$service->uri)
                                    @endif
                                @else
                                    @php($endpoint = $jsonActivityPropertiesData['recipient_endpoint'])
                                @endif
                                <span>{{ $endpoint }}</span>
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >MSG ID:</label>
                                <span>{{ $egovM->id }}</span>
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >Заявка:</label>
                                <span>{{ $jsonActivityPropertiesData['send_xml'] ?? '' }}</span>
                            </div>
                            <div class="form-group form-group-sm col-12 mb-3">
                                <label class="form-label fw-semibold" >Отговор:</label>
                                @if(is_array($jsonActivityPropertiesData['response']))
                                    <pre>@php(print_r($jsonActivityPropertiesData['response']))</pre>
                                @else
                                    <span>{{ $jsonActivityPropertiesData['response'] ?? '' }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-md-offset-3">
                            <a href="{{ route('admin.application.view', ['item' => $activity->subject_id]) }}"
                               class="btn btn-primary">{{ __('custom.back') }}</a>
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </section>
@endsection

