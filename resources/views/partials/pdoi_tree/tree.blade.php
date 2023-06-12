<form id="pdoiSubjectsTree" class="px-2">
    @if(isset($subjects) && sizeof($subjects))
        @foreach($subjects as $subject)
            @if(!$subject['selectable'])
                @if(isset($subject['children']) && sizeof($subject['children']))
                    <p class="mb-1 fw-semibold" role="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $subject['id'] }}" aria-expanded="false" aria-controls="collapse{{ $subject['id'] }}">
                        <i class="fa-regular fa-circle me-2" style="font-size: 7px;"></i> {{ $subject['name'] }} ({{ sizeof($subject['children']) }})
                    </p>
                    <div class="collapse multi-collapse" id="collapse{{ $subject['id'] }}">
                        <div class="card card-body ps-4 border-0">
                            @include('partials.pdoi_tree.tree_row', ['children' => $subject['children']])
                        </div>
                    </div>
                @else
                    <p>{{ $subject['name'] }}</p>
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
