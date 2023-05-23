@extends('layouts.admin')

@php
    if (strstr($activity->subject_type, 'App')) {
        $subject_type = $activity->subject_type;
    } else {
        $subject_type = Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($activity->subject_type);
    }
    $title = trans_choice('custom.activity_logs', 1)." ".__('custom.by')." ".l_trans($subject_type::MODULE_NAME, 1)." ".$activity->getSubjectName();
@endphp

@section('title')
    {{ $title }}
@endsection

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{__('custom.home')}}</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.activity-logs') }}">
                                <i class="fa fa fa-clock-o"></i> {{trans_choice('custom.activity_logs', 2)}}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>


    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <tbody>
                        @php
                            $subject_name = $activity->getSubjectName();
                            $causer_name = "Няма данни за Дееце, може би е бил изтрит";
                            if($activity->causer) {
                              $causer_name = $activity->causer->fullName();
                            }
                            if (strstr($activity->subject_type, 'App')) {
                                $subject_type = $activity->subject_type;
                            } else {
                                $subject_type = Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($activity->subject_type);
                            }
                        @endphp
                        <tr>
                            <td>{{__('custom.activity_log_date')}}</td>
                            <td>{{$activity->created_at}}</td>
                        </tr>
                        <tr>
                            <td>{{__('custom.activity_log_model')}}</td>
                            <td>{{$activity->subject_type}}</td>
                        </tr>
                        <tr>
                            <td>{{__('custom.activity_log_action')}}</td>
                            <td>{{__('custom.'.$activity->description)." ".__('custom.of')." ".trans_choice('custom.'.$activity->log_name, 1)}}</td>
                        </tr>
                        <tr>
                            <td>{{__('custom.activity_log_subject')}}</td>
                            <td>{{trans_choice($subject_type::MODULE_NAME, 1).": ".$activity->getSubjectName()}}</td>
                        </tr>
                        <tr>
                            <td>{{__('custom.activity_log_causer')}}</td>
                            <td>{{$causer_name}}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="background: #c6d9f0;color:#1c3050;border: 1px solid #1c3050;">{{__('custom.activity_log_new_state')}}</td>
                            <td style="background: #c6d9f0;color:#1c3050;border: 1px solid #1c3050;">{{__('custom.activity_log_old_state')}}</td>
                        </tr>
                        @if($activity->log_name == "depends-on-project")
                            @includeIf('admin.activity-logs.depends-on-project')
                        @else
                            @includeIf('admin.activity-logs.common')
                        @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>

@endsection
