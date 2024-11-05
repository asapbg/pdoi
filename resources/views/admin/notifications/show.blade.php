@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">
        @php($data = json_decode($notification->data, true));
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Относно: {{ $data['subject'] }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3"><span class="fw-bold">Тип:</span>
                        @if($notification->by_email == 1)
                            Ел. поща
                        @endif
                        @if($notification->by_app == 1)
                            @if($notification->by_email == 1){{ ', ' }}@endif
                                Втрешно съобщение
                        @endif
                    </div>
                    <div class="col-12 mb-3"><span class="fw-bold">Планирано на:</span> {{ displayDateTime($notification->created_at) }}</div>
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено от:</span> {{ $notification->sender?->fullName() }}</div>
{{--                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено до:</span> {{ $notification->notifiable->fullName() }}</div>--}}
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено на:</span>
                        @if($notification->send_at)
                            <i class="fas fa-check-square text-success me-2"></i> {{ displayDateTime($notification->send_at) }}
                        @else
                            ---
                        @endif
                    </div>
                    <div class="col-12 fw-bold">Съдържание:</div>
                    <div class="col-12 mb-2">
                        {!! $data['msg'] !!}
                    </div>
                    <div class="col-12 fw-bold">Изпратено до:</div>
                    <div class="col-12 mb-2">
                        @php($users = $notification->recipients())
                        @if($users->count())
                            @foreach($users as $u)
                                {{ $u->fullname() }} ({{ $u->email }}),
                            @endforeach
                        @else
                            ---
                        @endif
                    </div>
                    @if($notification->by_email == 1 && !empty($notification->not_send_to_by_email))
                        <div class="col-12 fw-bold"> <i class="fas fa-exclamation-triangle text-danger me-2"></i> Не е изпратено по ел. поща до:</div>
                        <div class="col-12 mb-2">
                            @php($users = $notification->notReceivedByMail())
                            @if($users && $users->count())
                                @foreach($users as $u)
                                    {{ $u->fullname() }} ({{ $u->email }}),
                                @endforeach
                            @else
                                ---
                            @endif
                        </div>
                    @endif
                    @if($notification->by_app == 1 && !empty($notification->not_send_to_by_app))
                        <div class="col-12 fw-bold"><i class="fas fa-exclamation-triangle text-danger me-2"></i>  Не е изпратено като вътрешно съобщение до:</div>
                        <div class="col-12 mb-2">
                            @php($users = $notification->notReceivedByApp())
                            @if($users && $users->count())
                                @foreach($users as $u)
                                    {{ $u->fullname() }} ({{ $u->email }}),
                                @endforeach
                            @else
                                ---
                            @endif
                        </div>
                    @endif
                </div>

            </div>
            <div class="card-footer">
                <a href="{{ route('admin.notifications') }}" class="btn btn-primary">{{ __('custom.back') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection


