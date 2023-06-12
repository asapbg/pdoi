@if(isset($children) && sizeof($children))
    @foreach($children as $child)
        @if(isset($child['children']) && sizeof($child['children']))
            <p class="mb-1 @if($oldBootstrap){{ 'fw-bold' }}@else{{ 'fw-semibold' }}@endif" role="button" {{ $bootstrapDataPrefix }}-toggle="collapse" {{ $bootstrapDataPrefix }}-target="#collapse{{ $child['id'] }}" aria-expanded="false" aria-controls="collapse{{ $child['id'] }}">
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
                <div class="ps-4">
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
    @endforeach
@endif

