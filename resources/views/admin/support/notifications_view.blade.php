@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <h5 class="bg-primary py-1 px-2 mb-4">Message data</h5>
                        <div class="col-12 mt-1">
                            <pre>@php(print_r(json_decode($item->data)))</pre>
                        </div>
                        <h5 class="bg-primary py-1 px-2 my-4">Notification Error</h5>
                        @if($msgErrors && $msgErrors->count())
                            <div class="accordion" id="accordionExample">
                                @foreach($msgErrors as $key => $er)
                                    <div class="card">
                                        <div class="card-header" id="heading{{ $key }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left @if(!$loop->first) collapsed @endif" type="button" data-toggle="collapse" data-target="#collapse{{ $key }}" aria-expanded="@if($loop->first){{ 'true' }}@else{{ 'false' }}@endif" aria-controls="collapse{{ $key }}">
                                                    #{{ ($key + 1) }} | {{ displayDateTime($er->created_at) }}
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapse{{ $key }}" class="collapse @if($loop->first) show @endif" aria-labelledby="heading{{ $key }}" data-parent="#accordionExample">
                                            <div class="card-body">
                                                {{ $er->content }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            No error
                        @endif

                        @if(isset($egovMessage) && $egovMessage)
                            <h5 class="bg-primary py-1 px-2 my-4">Egov message</h5>
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
                                <strong>msg_reg_dat</strong>: {{ $egovMessage->msg_reg_dat }}<br>
                                <strong>doc_guid</strong>: {{ $egovMessage->doc_guid }}<br>
                                <strong>doc_date</strong>: {{ $egovMessage->doc_date }}<br>
                                <strong>doc_rn</strong>: {{ $egovMessage->doc_rn }}<br>
                                <strong>comm_status</strong>: {{ $egovMessage->comm_status }}<br>
{{--                                <strong>comm_error</strong>: {{ $egovMessage->comm_error }}<br>--}}
                                <strong>created_at</strong>: {{ $egovMessage->created_at }}<br>
                                <strong>msg_xml</strong>: <br>
{{--                                <pre>{{ $egovMessage->msg_xml }}</pre><br>--}}
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
