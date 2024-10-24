@extends('layouts.app')

@section('content')
    <section class="content">
        @if (session('verified'))
            <div class="alert alert-success" role="alert">
                {{ __('auth.success_verify') }}
            </div>
        @endif
        <div class="row d-flex justify-content-md-evenly">
            <section class="py-5" >
                <div class="row">
                    @if(!auth()->user())
                        <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                            <a href="{{ route('login') }}" class="home-section-button light-yellow">
                                <span class="home-icon">
                                    <i class="fa-solid fa-right-from-bracket text-warning"></i>
                                </span>
                                <span class="home-section-button-txt">
                                    {{ __('custom.login') }}
                                </span>
                            </a>
                        </div>

                        <div class="col-lg-4 col-md-12 mb-lg-0 mb-4 ">
                            <a href="{{ route('register') }}" class="home-section-button light-red">
                                <span class="home-icon">
                                    <i class="fa-solid fa-circle-user text-danger"></i>
                                </span>
                                <span class="home-section-button-txt">
                                    {{ __('custom.register') }}
                                </span>
                            </a>
                        </div>
                    @elseif(auth()->user()->user_type == \App\Models\User::USER_TYPE_INTERNAL)
                        <div class="col-lg-8 col-md-12 mb-lg-0 mb-4">
                            <a href="{{ route('admin.home') }}" class="home-section-button light-yellow">
                                <span class="home-icon">
                                    <i class="fa-solid fa-right-from-bracket text-warning"></i>
                                </span>
                                <span class="home-section-button-txt">
                                    {{ __('front.to_admin_panel') }}
                                </span>
                            </a>
                        </div>
                    @endif
                    <div class="col-lg-4 col-md-12">
                        <a href="{{ route('help.page', ['slug' => $videoInstructionPage->slug]) }}" title="{{ $videoInstructionPage->name }}" class="home-section-button light-green">
                            <span class="home-icon">
                                <i class="fa-solid fa-display text-success"></i>
                            </span>
                            <span class="home-section-button-txt">
                                {{ $videoInstructionPage->name }}
                            </span>
                        </a>
                    </div>
                </div>
            </section>
            <hr class="mb-0 custom-hr" >
            <section class="py-5">
                <h2 class="pb-2 mb-3 fw-bold">{{ __('custom.most_asked_institutions') }}</h2>
{{--                    <div class="row">--}}
                    @if(isset($mostAskedSubjects) && sizeof($mostAskedSubjects))
                        @php($cnt = 1)
                            @foreach($mostAskedSubjects as $item)
                                @if($cnt == 1)
                                    <div class="row mb-4">
                                @endif
                                    <div class="col-lg-4 col-md-12 d-flex align-items-stretch mt-4 mt-lg-0 request">
                                        <div class="icon-box w-100">
                                            <div class="icon">
                                                <span>{{ $item->applications }}</span>
                                            </div>

                                            <p>{{ trans_choice('custom.queries', 2) }}</p>
                                            <h4>{{ $item->rzs_name }}</h4>
                                        </div>
                                    </div>
                                @if($cnt == 3)
                                    </div>
                                @endif
                                @if($cnt == 3)
                                    @php($cnt = 1)
                                @else
                                    @php($cnt += 1)
                                @endif
                            @endforeach
                    @endif

                <div class="row">
                    <a href="{{ route('statistic.list') }}" class="btn btn-primary rounded w-auto">{{ __('custom.more_statistics') }} <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
                </div>
            </section>
            <hr class="mb-0 custom-hr" >
            <section class="py-5">
                <div class="row mb-4">
                    <div class="col-lg-4 col-md-12 d-flex align-items-stretch mt-4 mt-lg-0 request">
                        <div class="icon-box w-100">
                            <div class="icon-no-width">
                                <span>{{ $allApplicationsCnt }}</span>
                            </div>

                            <p></p>
                            <h4>Подадени заявления</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 d-flex align-items-stretch mt-4 mt-lg-0 request">
                        <div class="icon-box w-100">
                            <div class="icon-no-width">
                                <span>{{ $registerdUsersCnt }}</span>
                            </div>

                            <p></p>
                            <h4>Регистрирани потребители</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 d-flex align-items-stretch mt-4 mt-lg-0 request">
                        <div class="icon-box w-100">
                            <div class="icon-no-width">
                                <span>{{ $pdoiSubjectsCnt }}</span>
                            </div>

                            <p></p>
                            <h4>Задължени субекти</h4>
                        </div>
                    </div>
                </div> 
                <div class="row mb-4 text justify-content-center">
                    <div class="col-lg-4 col-md-12 d-flex align-items-stretch mt-4 mt-lg-0 request">
                        <div class="icon-box w-100">
                            <div class="icon-no-width">
                                <span>{{ $applicationsLateAnswerCnt }}</span>
                            </div>

                            <p></p>
                            <h4>Заявления с просрочени отговори</h4>
                        </div>
                    </div>
                </div>
            </section>
            <hr class="custom-hr mb-0">
            <section class="py-5">
                <h2 class="pb-2 mb-3 fw-bold">{{ __('custom.last_applications') }}</h2>
                <div class="row mb-4">
                    @if(isset($applications) && sizeof($applications))
                        @foreach($applications as $item)
                            <div class="col-12">
                                <a class="home-sub-items" href="{{ route('application.show', ['id' => $item['id']]) }}"><i class="fa-regular fa-file-lines me-2"></i> {{  $item['title'] }}</a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="row">
                    <a href="{{ route('application.list') }}" class="btn btn-primary w-auto">{{ __('custom.to_all_applications') }} <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
                </div>
            </section>
        </div>
    </section>
@endsection
