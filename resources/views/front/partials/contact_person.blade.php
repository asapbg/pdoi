@if(isset($contacts) && sizeof($contacts))
    @foreach($contacts as $c)
        <div class="@if(!$loop->first) mt-3 @endif">
            <div class="mb-2">{{ $c->names }}</div>
            @if(!empty($c->phone))
            <span class=" mt-4 mr-2 text-dark"><strong>{{ __('custom.phone') }}</strong></span>
                <i class="me-2 fas fa-phone text-primary app-f-small"></i><span>{{ $c->phone }}</span>
            @endif
            @if(!empty($c->email))
                <br>
                <span class="mr-2  text-dark"><strong>{{ __('custom.email_contact') }}</strong></span>
                <i class="me-2 fas fa-envelope text-primary app-f-small"></i><span>{{ $c->email }}</span>
            @endif
        </div>
    @endforeach
@else
    <p>{{ __('custom.no_contact_found') }}</p>
@endif
