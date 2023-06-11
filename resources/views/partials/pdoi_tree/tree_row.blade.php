@if(isset($children) && sizeof($children))
    @foreach($children as $child)
        @if(isset($child['children']) && sizeof($child['children']))
            <p class="mb-1" role="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $child['id'] }}" aria-expanded="false" aria-controls="collapse{{ $child['id'] }}">
                <label>
                    @if(isset($canSelect) && $canSelect)
                        @if(isset($multipleSelect) && $multipleSelect)
                            <input class="" type="checkbox" name="subjects-item" value="{{ $child['id'] }}">
                        @else
                            <input class="" type="radio" name="subjects-item" value="{{ $child['id'] }}">
                        @endif
                    @else
                        {{ '-' }}
                    @endif
                    {{ $child['name'] }} ({{ sizeof($subject['children']) }})
                </label>
            </p>
            <div class="collapse multi-collapse" id="collapse{{ $child['id'] }}">
                <div class="card card-body ps-4  border-0">
                    @include('partials.pdoi_tree.tree_row', ['children' => $subject['children']])
                </div>
            </div>
        @else
            <p class="mb-1" >
                <label>
                    @if(isset($canSelect) && $canSelect)
                        @if(isset($multipleSelect) && $multipleSelect)
                            <input class="" type="checkbox" name="subjects-item" value="{{ $child['id'] }}">
                        @else
                            <input class="" type="radio" name="subjects-item" value="{{ $child['id'] }}">
                        @endif
                    @else
                        {{ '-' }}
                    @endif
                    {{ $child['name'] }}
                </label>
            </p>
        @endif
{{--        <p>{{ isset($children['children']) && sizeof($children['children']) ? '- ' : ''}}{{ $child['name'] }} ({{ $child['id'] }})</p>--}}
{{--        @if(isset($child['children']) && sizeof($child['children']))--}}
{{--            @include('partials.pdoi_tree.tree_row', ['children' => $child['children']])--}}
{{--        @endif--}}
    @endforeach
@endif

