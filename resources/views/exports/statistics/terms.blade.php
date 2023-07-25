<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th colspan="4"><strong>{{ $data['title'] }}</strong></th>
    </tr>
    <tr>
        <th>{{ trans_choice('custom.pdoi_response_subjects', 1)  }}</th>
        <th>{{ __('custom.statistic.terms.total_applications') }}</th>
        <th>{{ __('custom.statistic.terms.in_time') }}</th>
        <th>{{ __('custom.statistic.terms.expired') }}</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($data['statistic']) && $data['statistic']->count())
        @foreach($data['statistic'] as $row)
            <tr>
                <td>{{ $row->subject_name }}</td>
                <td>{{ $row->total_applications }}</td>
                <td>{{ $row->in_time_applications }}</td>
                <td>{{ $row->expired_applications }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>


