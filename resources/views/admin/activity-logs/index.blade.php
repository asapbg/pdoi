@extends('layouts.admin')

@php
    $singular = trans_choice('custom.activity_logs', 1);
    $title = trans_choice('custom.activity_logs', 2);
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
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <form method="GET">

                    <div class="card-header with-border">

                        <div class="card-tools pull-right">
                            <label>{{ trans_choice('custom.results', 2) }}: </label>
                            <select name="paginate" class="form-control d-inline w-auto">
                                @foreach(range(1,3) as $multiplier)
                                    @php
                                        $paginate = $multiplier * App\Models\User::PAGINATE;
                                    @endphp
                                    <option value="{{ $paginate }}"
                                            @if (request()->get('paginate') == $paginate) selected="selected" @endif
                                    >{{ $paginate }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-box-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <h3 class="card-title">{{ __('custom.search') }}</h3>

                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-xs-12 col-md-2">
                                <label>{{__('custom.date')}}</label>
                                <input type="text" name="activity_log_date" class="form-control datepicker" autocomplete="off"
                                       value="{{ request()->get('activity_log_date') }}">
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <label for="subject_type">{{__('custom.activity_log_model')}}</label>
                                <select name="subject_type" id="subject_type" class="form-control select2">
                                    <option value=""></option>
                                    @if ($causers->count() > 0)
                                        @foreach ($causers as $causer)
                                            <option value="{{ $causer->id }}" @if (request()->get('causer_id') == $causer->id) selected="selected" @endif>
                                                {{ $causer->fullName() }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label for="causer_id">{{ __('custom.activity_log_causer') }}</label>
                                <select name="causer_id" id="causer_id" class="form-control select2">
                                    <option value=""></option>
                                    @if ($causers->count() > 0)
                                        @foreach ($causers as $causer)
                                            <option value="{{ $causer->id }}" @if (request()->get('causer_id') == $causer->id) selected="selected" @endif>
                                                {{ $causer->fullName() }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3 col-sm-4 mb-2">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-search"></i> {{__('custom.search')}}
                                </button>
                                <a href="{{ route('admin.activity-logs') }}" class="btn btn-default">
                                    <i class="fas fa-eraser"></i> {{__('custom.clear')}}
                                </a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{__('custom.activity_log_date')}}</th>
                            <th>{{__('custom.activity_log_model')}}</th>
                            <th>{{__('custom.activity_log_action')}}</th>
                            <th>{{__('custom.activity_log_subject')}}</th>
                            <th>{{__('custom.activity_log_subject_id')}}</th>
                            <th>{{__('custom.activity_log_causer')}}</th>
                            <th>{{__('custom.activity_log_causer_id')}}</th>
                            <th>{{__('custom.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($activities) && count($activities))
                            @foreach($activities as $activity)
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
                                    <td>{{ $activity->id }}</td>
                                    <td>{{ displayDateTime($activity->created_at) }}</td>
                                    <td>{{ trans_choice($subject_type::MODULE_NAME, 1) }}</td>
                                    <td>{{ $activity->getActivityDescription() }}</td>
                                    <td>{{ $activity->getSubjectName() }}</td>
                                    <td>{{ $activity->subject_id }}</td>
                                    <td>{{ $causer_name }}</td>
                                    <td>{{ $activity->causer_id }}</td>
                                    <td>
                                        <a href="{{route('admin.activity-logs.show',$activity->id)}}"
                                           class="btn btn-sm btn-info"
                                           data-toggle="tooltip"
                                           title="{{__('custom.view')}}">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                </div>

                <div class="card-footer mt-2">
                    @if(isset($activities) && $activities instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $activities->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>

        </div>
    </section>

@endsection
