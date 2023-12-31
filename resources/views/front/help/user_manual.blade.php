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
                        <div class="row mt-3">
                            <embed src="{{ asset(DIRECTORY_SEPARATOR.'help'.DIRECTORY_SEPARATOR.\App\Models\File::USER_GUIDE_FILE) }}" width="800px" height="2100px" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
