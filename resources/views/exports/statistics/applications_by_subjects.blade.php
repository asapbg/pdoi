@php($total = [])
@php($colorBkg = '#b6def3')
<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th style="background-color: {{ $colorBkg }}" colspan="{{ 1 + sizeof(\App\Enums\PdoiApplicationStatusesEnum::names()) }}"><strong>{{ $data['data']['title'] }}</strong></th>
    </tr>
    <tr>
        <th style="background-color: {{ $colorBkg }}" colspan="{{ 1 + sizeof(\App\Enums\PdoiApplicationStatusesEnum::names()) }}"><strong>{{ $data['data']['period'] }}</strong></th>
    </tr>
    <tr>
        <th style="background-color: {{ $colorBkg }}">{{ trans_choice('custom.institutions', 1) }}</th>
        @foreach(\App\Enums\PdoiApplicationStatusesEnum::options() as $name => $key)
            @php($total[$key] = 0)
            <th style="background-color: {{ $colorBkg }}">{{ __('custom.application.status.'.$name) }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @if(isset($data['data']['statistic']) && sizeof($data['data']['statistic']))
        @foreach($data['data']['statistic'] as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                @foreach(\App\Enums\PdoiApplicationStatusesEnum::values() as $val)
                    @php($total[$val] += $row['cnt_'.$val] ?? 0)
                    <td>{{ $row['cnt_'.$val] ?? 0 }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <th style="background-color: {{ $colorBkg }}">{{ __('custom.total') }}</th>
            @foreach(\App\Enums\PdoiApplicationStatusesEnum::values() as $val)
                <th style="background-color: {{ $colorBkg }}">{{ $total[$val] }}</th>
            @endforeach
        </tr>
    @endif
    </tbody>
</table>
