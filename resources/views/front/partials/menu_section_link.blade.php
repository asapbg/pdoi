@if(isset($isChild) && $isChild && !(isset($section['children']) && sizeof($section['children'])))
    <li><a class="dropdown-item px-2 @if(\Illuminate\Support\Str::contains(url()->current(), [$section['slug']])) active @endif" href="{{ route('section', ['slug' => $section['slug']]) }}">{{ $section['name'] }}</a></li>
@else
    <li class="nav-item @if(isset($section['children']) && sizeof($section['children'])) dropdown @endif">
{{--        <a role="button" data-bs-toggle="dropdown" aria-expanded="false" class="nav-link dropdown-toggle @if(\Illuminate\Support\Str::contains(url()->current(), [$section['slug']])) active @endif" href="{{ route('section', ['slug' => $section['slug']]) }}">--}}
        <a class="nav-link @if(isset($section['children']) && sizeof($section['children'])) dropdown-toggle @endif px-2 @if(\Illuminate\Support\Str::contains(url()->current(), ['/'.$section['slug']])) active @endif"
           href="{{ route('section', ['slug' => $section['slug']]) }}"
           @if(isset($section['children']) && sizeof($section['children'])) role="button" aria-expanded="false" @endif>
            {{ $section['name'] }}
            @if(isset($section['children']) && sizeof($section['children']))<span class="dropdown-toggle-arrow"></span>@endif
        </a>
        @if(isset($section['children']) && sizeof($section['children']))
            <ul class="dropdown-menu ms-{{ ($section['level'] * 4) - 4 }}">
                @foreach($section['children'] as $child)
                    @include('front.partials.menu_section_link', ['section' => $child, 'isChild' => true])
                @endforeach
            </ul>
        @endif
    </li>
@endif
