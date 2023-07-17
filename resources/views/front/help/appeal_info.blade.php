@extends('layouts.app')

@section('content')
    <section class="content container">
        <div class="page-title mb-md-3 mb-2 px-5">
            <h3 class="b-1 text-center">{{ $item->name }}</h3>
        </div>
        <div class="card card-light mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        {!! $item->content !!}
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('help.download', ['file' => \App\Models\Page::APPEAL_INFO_FILE]) }}" class="btn btn-primary mt-3"><i class="far fa-file me-2"></i>Свали шаблонен формуляр на жалба</a>
    </section>
@endsection
