@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <h5 class="bg-primary py-1 px-2 mb-4">Message data</h5>
                        <div class="col-12 mt-1">
                            {{ $item->data }}
                        </div>
                        <h5 class="bg-primary py-1 px-2 my-4">Notification Error</h5>
                        @if($msgErrors && $msgErrors->count())
                            @foreach($msgErrors as $key => $er)
                                <div class="col-12 mt-1">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label">#{{ ($key + 1) }} | {{ displayDateTime($er->created_at) }}</label>
                                        <div class="col-12 mt-1">
                                            {{ $er->content }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            No error
                        @endif

                        @if(isset($egovMessage) && $egovMessage)
                            <div class="col-12 mt-1">
                                <strong>sender_guid</strong>: {{ $egovMessage->sender_guid }}<br>
                                <strong>sender_name</strong>: {{ $egovMessage->sender_name }}<br>
                                <strong>sender_eik</strong>: {{ $egovMessage->sender_eik }}<br>
                                <strong>recipient_guid</strong>: {{ $egovMessage->recipient_guid }}<br>
                                <strong>recipient_name</strong>: {{ $egovMessage->recipient_name }}<br>
                                <strong>recipient_eik</strong>: {{ $egovMessage->recipient_eik }}<br>
                                <strong>msg_type</strong>: {{ $egovMessage->msg_type }}<br>
                                <strong>msg_status</strong>: {{ $egovMessage->msg_status }}<br>
                                <strong>msg_status_dat</strong>: {{ $egovMessage->msg_status_dat }}<br>
                                <strong>comm_status</strong>: {{ $egovMessage->comm_status }}<br>
                                <strong>comm_error</strong>: {{ $egovMessage->comm_error }}<br>
                                <strong>created_at</strong>: {{ $egovMessage->created_at }}<br>
                                <strong>msg_xml</strong>: {{ $egovMessage->msg_xml }}<br>
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">Service Urls</h5>
                            <div class="col-12 mt-1">
                                @if($egovMessage->recipient && $egovMessage->recipient->services->count())
                                    @foreach($egovMessage->recipient->services as $s)
                                        {{ $s->service_name }}: {{ $s->uri }} ({{ $s->status }})
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
