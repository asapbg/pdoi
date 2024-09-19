@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Относно: {{ $notification->data['subject'] }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено от:</span> {{ $notification->data['sender_name'] }}</div>
{{--                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено до:</span> {{ $notification->notifiable->fullName() }}</div>--}}
                    <div class="col-12 mb-3"><span class="fw-bold">Изпратено на:</span> {{ displayDateTime($notification->created_at) }}</div>
                    <div class="col-12 mb-3"><span class="fw-bold">Прочетено:</span> @if(!$notification->unread()) {{ displayDateTime($notification->read_at) }} @else {{ '---' }}@endif</div>
                    <div class="col-12 mb-3"><span class="fw-bold">Относно:</span> {{ $notification->data['subject'] }}</div>
                    <div class="col-12 fw-bold">Съдържание:</div>
                    <div class="col-12">
                        {!! $notification->data['message'] !!}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.profile.notifications') }}" class="btn btn-primary">{{ __('custom.back') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection


