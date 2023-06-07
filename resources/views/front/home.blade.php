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
                <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-regular fa-file-lines text-warning" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Заявление
                        <span class="d-inline-block">Подаване на заявление за достъп до обществена информация.</span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-3 p-3 mb-md-3 mb-2 shadow-sm rounded bg-body-tertiary">
                <a href="" title="" class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-solid fa-magnifying-glass text-success" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Търсене
                        <span class="d-inline-block">Обществена информация, публикувана на платформата.</span>
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
                <a href="" title=""  class="text-decoration-none w-100 h-100 d-flex flex-row gap-3 justify-content-sm-between align-items-center">
                    <span>
                        <i class="fa-regular fa-address-book text-primary" style="font-size: 55px;"></i>
                    </span>
                    <span class="d-inline-block flex-grow-1">
                        Контакти
                        <span class="d-inline-block">&nbsp;</span>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endsection
