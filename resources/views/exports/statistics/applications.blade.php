<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th colspan="2"><strong>{{ $data['title'] }}</strong></th>
    </tr>
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

