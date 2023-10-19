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
            </section>
        @endsection

    </section>
@endsection
