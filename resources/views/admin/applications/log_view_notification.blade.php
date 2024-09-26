@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row mb-4">
                        @php($notification = $item['notification'])
                        @php($jsonNotifyMsgData = json_decode($notification->data, true))

                        <h5 class="bg-primary py-1 px-2">{{ __('custom.notification_types.'.$notification->type) }}</h5>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Тип събитие:</label>
                            <span>Комуникационно</span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Дата/час на настъпване:</label>
                            <span>{{ displayDateTime($notification->created_at }}</span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Статус:</label>
                            Успешно
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Канал:</label>
                            <span>
                                @if($notification->notifiable_type == 'App\Models\PdoiResponseSubject')
                                    {{ __('custom.rzs.delivery_by.'.\App\Enums\PdoiSubjectDeliveryMethodsEnum::keyByValue($notification->type_channel)) }}
                                @else
                                    {{ __('custom.delivered_by.'.\App\Enums\DeliveryMethodsEnum::keyByValue($notification->type_channel)) }}
                                @endif
                            </span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >До:</label>
                            <span>
                                @if($notification->notifiable_type == 'App\Models\PdoiResponseSubject')
                                    @if($notification->type_channel == \App\Enums\PdoiSubjectDeliveryMethodsEnum::EMAIL->value)
                                        <a href="{{ route('admin.rzs.view', $notification->notifiable_id) }}" target="_blank">{{ $jsonNotifyMsgData['to_email'] }}</a>
                                    @elseif($notification->type_channel == \App\Enums\PdoiSubjectDeliveryMethodsEnum::SDES->value)
                                        {{ $jsonNotifyMsgData['to_identity'] }} (ССЕВ ID {{ $jsonNotifyMsgData['ssev_profile_id'] }})
                                    @endif
                                @else
                                    @if($notification->type_channel == \App\Enums\DeliveryMethodsEnum::EMAIL->value)
                                        <a class="text-primary" href="{{ route('admin.users.edit', $notification->notifiable_id) }}" target="_blank">{{ $jsonNotifyMsgData['to_email'] }}</a>
                                    @elseif($notification->type_channel == \App\Enums\DeliveryMethodsEnum::SDES->value)
                                        {{ $jsonNotifyMsgData['to_identity'] }} (ССЕВ ID {{ $jsonNotifyMsgData['ssev_profile_id'] }})
                                    @endif
                                @endif
                            </span>
                        </div>
                        <div class="form-group form-group-sm col-12 mb-3">
                            <label class="form-label fw-semibold" >Съобщение:</label>
                            <pre>@php(print_r(json_decode($notification->data)))</pre>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-md-offset-3">
                            <a href="{{ route('admin.application.view', ['item' => $jsonNotifyMsgData['application_id']]) }}"
                               class="btn btn-primary">{{ __('custom.back') }}</a>
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </section>
@endsection

