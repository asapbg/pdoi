<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th colspan="2"><strong>{{ $data['title'] }}</strong></th>
    </tr>
    <tr>
        <th>{{ trans_choice('custom.pdoi_response_subjects', 1) }}</th>
        <th>{{ trans_choice('custom.applications', 2) }}</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($data['statistic']) && $data['statistic']->count())
        @foreach($data['statistic'] as $row)
            <tr>
                <td>{{ $row->name }}</td>
                <td>{{ $row->value_cnt }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

