@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form', ['filter' => $data['filter'], 'canExport' => $data['canExport'] ?? 0])

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>{{ $data['name_title'] ?? 'NA' }}</th>
                            <th>{{ trans_choice('custom.applications', 2) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($data['statistic']) && $data['statistic']->count())
                            @foreach($data['statistic'] as $row)
                                <tr>
                                    <td>{{ isset($data['groupedBy']) && $data['groupedBy'] != 'status' ? $row->name : __('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($row->name))}}</td>
                                    <td>{{ $row->value_cnt }}</td>
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


