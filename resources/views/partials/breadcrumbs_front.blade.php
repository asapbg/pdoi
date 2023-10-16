@if (isset($breadcrumbs))
    <nav style="--bs-breadcrumb-divider: '»';" aria-label="breadcrumb">
        <ol class="breadcrumb pb-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('custom.home') }}</a></li>
            @foreach($breadcrumbs['links'] as $key => $link)
                @if($key < $breadcrumbs['links_count'])
                    <li class="breadcrumb-item"><a href="{{ $link['url'] }}">{{ capitalize($link['name']) }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ capitalize($link['name']) }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@else
    <nav style="--bs-breadcrumb-divider: '»';" aria-label="breadcrumb">
        <ol class="breadcrumb pb-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('custom.home') }}</a></li>
        </ol>
    </nav>
@endif
