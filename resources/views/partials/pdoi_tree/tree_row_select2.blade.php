@if($subject['selectable'])
    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
@endif
@if(isset($subject['children']) && sizeof($subject['children']))
    @foreach($subject['children'] as $child)
        @include('partials.pdoi_tree.tree_row_select2', ['subject' => $child])
    @endforeach
@endif
