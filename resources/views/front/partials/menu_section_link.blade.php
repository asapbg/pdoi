@if(isset($isChild) && $isChild)
    <li><a class="dropdown-item px-2 @if(\Illuminate\Support\Str::contains(url()->current(), [$section['slug']])) active @endif" href="{{ route('section', ['slug' => $section['slug']]) }}">{{ $section['name'] }}</a></li>
@else
    <li class="nav-item dropdown">
{{--        <a role="button" data-bs-toggle="dropdown" aria-expanded="false" class="nav-link dropdown-toggle @if(\Illuminate\Support\Str::contains(url()->current(), [$section['slug']])) active @endif" href="{{ route('section', ['slug' => $section['slug']]) }}">--}}
        <a class="nav-link dropdown-toggle px-2 @if(\Illuminate\Support\Str::contains(url()->current(), [$section['slug']])) active @endif"
           href="{{ route('section', ['slug' => $section['slug']]) }}"
           role="button" aria-expanded="false">
            {{ $section['name'] }}
        </a>
        @if(isset($section['children']) && sizeof($section['children']))
            <ul class="dropdown-menu">
                @foreach($section['children'] as $child)
                    @include('front.partials.menu_section_link', ['section' => $child, 'isChild' => true])
                @endforeach
            </ul>
        @endif
    </li>
@endif
