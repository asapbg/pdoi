@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @if(isset($data) && isset($data['user_type']) && isset($data['user_type']['title']))
                        <table class="table table-sm table-bordered mb-4" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th colspan="2" class="fw-bold">{{ $data['user_type']['title'] }}</th>
                                </tr>
                                <tr>
                                    <th>{{ trans_choice('custom.internal_users',2) }}</th>
                                    <th>{{ trans_choice('custom.external_users', 2) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['user_type']['statistic']) && sizeof($data['user_type']['statistic']))
                                    <tr>
                                        <td>{{ $data['user_type']['statistic'][0]->internal_users }}</td>
                                        <td>{{ $data['user_type']['statistic'][0]->external_users }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @endif
                        @if(isset($data) && isset($data['subjects_with_admin']) && isset($data['subjects_with_admin']['title']))
                            <table class="table table-sm table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="fw-bold">{{ $data['subjects_with_admin']['title'] }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($data['subjects_with_admin']['statistic']) && sizeof($data['subjects_with_admin']['statistic']))
                                    @foreach($data['subjects_with_admin']['statistic'] as $row)
                                        <tr>
                                            <td>{{ $row->rzs_name }}</td>
                                            <td>{{ $row->rzs_administrators }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        @endif
                </div>
            </div>
        </div>
    </section>
@endsection
