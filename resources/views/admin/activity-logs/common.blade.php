
<tr>
    <td>
        @isset($activity->changes['attributes'])
            @foreach($activity->changes['attributes'] as $key => $value)
                @if($key != "updated_at" && $key != "password")
                    @if(is_array($value))
                        {{ $key }}:
                        @foreach($value as $val)
                            @if(is_array($val))
                                @if(is_array($val))
                                    @foreach($val as $v)
                                        {{ $v['name'] }}<br>
                                    @endforeach
                                @else
                                    {{ $val['name'] }}<br>
                                @endif
                            @else
                                {{ $val }}<br>
                            @endif
                        @endforeach
                    @else
                        <p>{{ trans_choice('custom.'.$key, 1) }}: {{ $value }}</p>
                    @endif
                @endif
            @endforeach
        @endisset
    </td>
    <td>
        @isset($activity->changes['old'])
            @foreach($activity->changes['old'] as $key => $value)
                @if($key != "updated_at" && $key != "password")
                    @if(is_array($value))
                        {{ $key }}:
                        @foreach($value as $val)
                            @if(is_array($val))
                                @if(is_array($val))
                                    @foreach($val as $v)
                                        {{ $v['name'] }}<br>
                                    @endforeach
                                @else
                                    {{ $val['name'] }}<br>
                                @endif
                            @else
                                {{ $val }}<br>
                            @endif
                        @endforeach
                    @else
                        <p>{{ trans_choice('custom.'.$key, 1) }}: {{ $value }}</p>
                    @endif
                @endif
            @endforeach
        @endisset
    </td>
</tr>
