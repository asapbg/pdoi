@if(isset($contacts) && sizeof($contacts))
    @foreach($contacts as $c)
        <div class="@if(!$loop->first) mt-3 @endif">
            {{ $c->names }}
            @if(!empty($c->phone))
                <br><i class="me-2 fas fa-phone text-primary app-f-small"></i><span>{{ $c->phone }}</span>
            @endif
            @if(!empty($c->email))
                <br><i class="me-2 fas fa-envelope text-primary app-f-small"></i><span>{{ $c->email }}</span>
            @endif
        </div>
    @endforeach
@else
    <p>{{ __('custom.no_contact_found') }}</p>
@endif
