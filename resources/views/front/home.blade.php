@extends('layouts.app')

@section('content')
    <section class="content">
        @if (session('verified'))
            <div class="alert alert-success" role="alert">
                {{ __('auth.success_verify') }}
            </div>
        @endif
        <div class="row d-flex justify-content-md-evenly">
            <div class="card card-light mb-4 px-0 col-md-5">
                <div class="card-header app-card-header py-1 pb-0">
                    <h3 class="fs-5">
                        <i class="fa-solid fa-file me-2"></i> {{ __('custom.last_applications') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($applications) && sizeof($applications))
                            @foreach($applications as $item)
                                <div class="col-12 mb-4 mb-3">
                                    <a class="a-fs" href="{{ route('application.show', ['id' => $item['id']]) }}">{{  $item['title'] }}</a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @if(isset($applications) && sizeof($applications))
                    <div class="card-footer">
                        <a href="{{ route('application.list') }}">{{ __('custom.to_all_applications') }}</a>
                    </div>
                @endif
            </div>
            <div class="card card-light mb-4 px-0 col-md-5">
                <div class="card-header app-card-header py-1 pb-0">
                    <h3 class="fs-5">
                        <i class="fa-solid fa-file me-2"></i> {{ __('custom.most_asked_institutions') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($mostAskedSubjects) && sizeof($mostAskedSubjects))
                            <table>
                                @foreach($mostAskedSubjects as $item)
                                    <tr>
                                        <td>{{ $item->rzs_name }}</td>
                                        <td>{{ $item->applications }} {{ trans_choice('custom.applications', 2) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>
                @if(isset($mostAskedSubjects) && sizeof($mostAskedSubjects))
                    <div class="card-footer">
                        <a href="{{ route('statistic.list') }}">{{ __('custom.more_statistics') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
