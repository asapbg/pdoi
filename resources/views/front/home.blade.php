@extends('layouts.app')

@section('content')
    <section class="content">
        @if (session('verified'))
            <div class="alert alert-success" role="alert">
                {{ __('auth.success_verify') }}
            </div>
        @endif
        <div class="row d-flex justify-content-md-evenly">
            <div class="card card-light mb-4 mt-4 px-0 col-md-5">
                <div class="card-header app-card-header py-1 pb-0">
                    <h3 class="fs-4 pt-2">
                        <i class="fa-solid fa-file-import me-2"></i> {{ __('custom.last_applications') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($applications) && sizeof($applications))
                            @foreach($applications as $item)
                                <div class="col-12 mb-3">
                                    <a class="home-sub-items" href="{{ route('application.show', ['id' => $item['id']]) }}">{{  $item['title'] }}</a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @if(isset($applications) && sizeof($applications))
                    <div class="card-footer">
                        <a href="{{ route('application.list') }}" class="btn btn-primary btn-sm">{{ __('custom.to_all_applications') }} <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
                    </div>
                @endif
            </div>
            <div class="card card-light mb-4 mt-4 px-0 col-md-5">
                <div class="card-header app-card-header py-1 pb-0">
                    <h3 class="fs-4 pt-2">
                        <i class="fa-solid fa-clipboard-question me-2"></i> {{ __('custom.most_asked_institutions') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($mostAskedSubjects) && sizeof($mostAskedSubjects))
                                @foreach($mostAskedSubjects as $item)
                                    <div class="col-12 mb-3">
                                        <p class="home-sub-items m-0">
                                            <span>{{ $item->rzs_name }}</span>
                                            <span class="home-sub-items-count">{{ $item->applications }} {{ mb_strtolower(trans_choice('custom.queries', 2)) }}</span>
                                        </p>
                                    </div>
                                @endforeach
                        @endif
                    </div>
                </div>
                @if(isset($mostAskedSubjects) && sizeof($mostAskedSubjects))
                    <div class="card-footer">
                        <a href="{{ route('statistic.list') }}" class="btn btn-primary btn-sm rounded ">{{ __('custom.more_statistics') }} <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
