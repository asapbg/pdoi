<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th colspan="2"><strong>{{ $data['title'] }}</strong></th>
    </tr>
    </thead>
</table>

@if(isset($data) && isset($data['user_type']) && isset($data['user_type']['title']))
    <table class="table table-hover table-bordered" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2"><strong>{{ $data['user_type']['title'] }}</strong></th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['user_type']['statistic']) && sizeof($data['user_type']['statistic']))
                <tr>
                    <td>{{ trans_choice('custom.internal_users', 2) }}</td>
                    <td>{{ $data['user_type']['statistic'][0]->internal_users }}</td>
                </tr>
                <tr>
                    <td>{{ trans_choice('custom.external_users', 2) }}</td>
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
            <th colspan="2"><strong>{{ $data['subjects_with_admin']['title'] }}</strong></th>
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

