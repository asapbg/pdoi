<form id="pdoiSubjectsTree" class="px-2">
    @php($bootstrapDataPrefix = $oldBootstrap ? 'data' : 'data-bs')
    @if(isset($subjects) && sizeof($subjects))
        @foreach($subjects as $subject)
            @if(!$subject['selectable'])
                <p class="mb-1 @if($oldBootstrap){{ 'fw-bold' }}@else{{ 'fw-semibold' }}@endif" role="button" {{ $bootstrapDataPrefix }}-toggle="collapse" {{ $bootstrapDataPrefix }}-target="#collapse{{ $subject['id'] }}" aria-expanded="false" aria-controls="collapse{{ $subject['id'] }}">
                    <i class="fa-regular fa-circle me-2" style="font-size: 7px;"></i> {{ $subject['name'] }} ({{ sizeof($subject['children']) }})
                </p>
                @if(isset($subject['children']) && sizeof($subject['children']))
                    <div class="collapse multi-collapse" id="collapse{{ $subject['id'] }}">
                        <div class="ps-4">
                            @include('partials.pdoi_tree.tree_row', ['children' => $subject['children']])
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
        @if($canSelect)
            <button type="button" class="btn btn-sm btn-primary mt-3" id="select-subject">{{ __('custom.select') }}</button>
        @endif
    @else
        <p>Не са откити записи</p>
    @endif
</form>
