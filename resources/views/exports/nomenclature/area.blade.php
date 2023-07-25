<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th colspan="6"><strong>{{ $title }}</strong></th>
    </tr>
    <tr>
        <th>ID</th>
        <th>{{__('validation.attributes.name')}}</th>
        <th>Област (код)</th>
        <th>ЕКАТТЕ</th>
        <th>Регион</th>
        <th>{{__('custom.active_m')}}</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($items) && $items->count() > 0)
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->ime }}</td>
                <td>{{ $item->oblast }}</td>
                <td>{{ $item->ekatte }}</td>
                <td>{{ $item->region }}</td>
                <td>@if($item->active){{ 'Да' }}@else{{ 'Не' }}@endif</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

