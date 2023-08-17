@extends('layouts.app')

@section('content')
    <section class="content">
        @if (session('verified'))
            <div class="alert alert-success" role="alert">
                {{ __('auth.success_verify') }}
            </div>
        @endif
        <div class="d-flex gap-md-4 flex-wrap justify-content-center">
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="{{ route('application.create') }}" title="{{ __('front.apply_new_application') }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-regular fa-file-lines text-warning" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        {{ __('front.application') }}
                        <span class="d-inline-block">{{ __('front.apply_new_application') }}</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="{{ route('application.list') }}" title="{{ __('front.search_in_applications') }}" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-solid fa-magnifying-glass text-success" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        {{ __('custom.searching') }}
                        <span class="d-inline-block">{{ __('front.search_in_applications') }}</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-regular fa-file-video text-primary-emphasis" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Видео инструкции
                        <span class="d-inline-block">Инструкции за работа с платформата за достъп на обществена информация.</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-solid fa-layer-group text-info-emphasis" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Документи
                        <span class="d-inline-block">Нормативни документи: закони, инструции, наредби.</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-solid fa-chart-line text-danger" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Статистика
                        <span class="d-inline-block">&nbsp;</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="{{ route('page', ['section_slug' => 'info', 'slug' => \App\Models\Page::CONTACT_SYSTEM_PAGE]) }}" title=""  class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-regular fa-address-book text-primary" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        {{ trans_choice('custom.contacts', 2) }}
                        <span class="d-inline-block">&nbsp;</span>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endsection
