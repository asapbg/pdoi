@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            @include('admin.partial.filter_form', ['filter' => $data['filter']])
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('custom.pdoi_response_subjects', 1)  }}</th>
                            <th>{{ trans_choice('custom.pdoi_applications', 2) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($data['statistic']) && $data['statistic']->count())
                            @foreach($data['statistic'] as $row)
                                <tr>
                                    <td>{{ $row->subject_name }}</td>
                                    <td>{{ $row->applications_cnt }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer mt-2">
                    @if(isset($data['statistic']) && $data['statistic']->count() > 0)
                        {{ $data['statistic']->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection


