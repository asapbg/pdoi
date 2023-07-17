@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ __('custom.help') }}</h3>
        </div>
        <div class="card card-light mb-4">
                <div class="card-body">
                    <div class="d-flex gap-md-4 flex-wrap justify-content-lg-start">
                        @if(isset($appealPage) && $appealPage)
                        <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                            <a href="{{ route('help.page', ['slug' => $appealPage->slug]) }}" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                                <span>
                                    <i class="fa-solid fa-gavel text-warning" style="font-size: 55px;"></i>
                                </span>
                                <span class="d-inline-block flex-grow-1">{{ $appealPage->name }}
                                </span>
                            </a>
                        </div>
                        @endif
                        <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                            <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                                <span>
                                    <i class="fa-regular fa-file-lines text-success" style="font-size: 55px;"></i>
                                </span>
                                <span class="d-inline-block flex-grow-1">Инструкции
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
