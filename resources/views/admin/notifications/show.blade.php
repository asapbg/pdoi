@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Относно: {{ $notification->data['subject'] }} до {{ $notification->notifiable->fullName() }} ({{ displayDateTime($notification->created_at) }})</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3"><span class="fw-bold">Тип:</span>
                        @if($notification->type_channel == 1)
                            Ел. поща
                        @else
                            Втрешно съобщение
                        @endif
                    </div>
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено от:</span> {{ $notification->data['sender_name'] }}</div>
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено до:</span> {{ $notification->notifiable->fullName() }}</div>
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено на:</span>
                        @if($notification->type_channel == 0)
                            {{ displayDateTime($notification->created_at) }}
                        @else
                            @if($notification->is_send)
                                {{ displayDateTime($notification->updated_at) }}
                            @else
                                <i class="fas fa-minus text-danger"></i>
                            @endif
                        @endif
                    </div>
                    <div class="col-12 mb-3"><span class="fw-bold">Прочетено:</span>
                        @if($notification->type_channel == 0)
                            @if($notification->unread())
                                <i class="fas fa-minus text-danger"></i>
                            @else
                                {{ displayDateTime($notification->read_at) }}
                            @endif
                        @else
                            NA
                        @endif
                    </div>
                    <div class="col-12 mb-3"><span class="fw-bold">Относно:</span> {{ $notification->data['subject'] }}</div>
                    <div class="col-12 fw-bold">Съдържание:</div>
                    <div class="col-12 mb-2">
                        {!! $notification->data['message'] !!}
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a href="{{ route('admin.notifications') }}" class="btn btn-primary">{{ __('custom.back') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection


