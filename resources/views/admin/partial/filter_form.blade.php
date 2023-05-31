@if(isset($filter) && count($filter))
    <div class="card">
        <form method="GET">
            <div class="card-header with-border">
                <div class="card-tools pull-right">
                    <label>{{ trans_choice('custom.results', 2) }}: </label>
                    <select name="paginate" class="form-control d-inline w-auto">
                        @foreach(range(1,3) as $multiplier)
                            @php
                                $paginate = $multiplier * App\Models\User::PAGINATE;
                            @endphp
                            <option value="{{ $paginate }}"
                                    @if (request()->get('paginate') == $paginate) selected="selected" @endif
                            >{{ $paginate }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-box-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <h3 class="card-title">{{ __('custom.search') }}</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    @foreach($filter as $key => $field)
                        <div class="col-xs-12 col-md-3 mb-2">
                            @switch($field['type'])
                                @case('text')
                                <input type="text" name="{{ $key }}" autocomplete="off"
                                       class="form-control form-control-sm"
                                       placeholder="{{ $field['placeholder'] }}"
                                       value="{{ old($key, $field['value']) }}" >
                                @break('text')
                                @case('datepicker')
                                <input type="text" name="{{ $key }}" autocomplete="off" readonly
                                       class="form-control form-control-sm datepicker"
                                       placeholder="{{ $field['placeholder'] }}"
                                       value="{{ old($key, $field['value']) }}" >
                                @break('datepicker')
                                @case('checkbox')
                                <input type="checkbox" name="{{ $key }}" @if($field['checked']) checked="checked" @endif
                                value="{{ old($key, $field['value']) }}" >
                                @break('checkbox')
                                @case('select')
                                    <select class="form-control select2 @if(isset($field['class'])){{$field['class'] }}@endif" name="{{ $key }}" >
                                        {{-- select with groups--}}
                                        @if(isset($field['group']) && $field['group'])
                                            @foreach($field['options'] as $group_name => $group)
                                                @if(isset($group['any']))
                                                    <option value="{{ $group['value'] }}" @if($group['value'] == old($key, $field['value'])) selected @elseif(is_null(old($key, $field['value'])) && isset($field['default']) && $group['value'] == $field['default']) selected @endif>{{ $group['name'] }}</option>
                                                @else
                                                    <optgroup label="{{ $group_name }}">
                                                        @if(sizeof($group) > 0)
                                                            @foreach($group as $option)
                                                                <option value="{{ $option['value'] }}" @if($option['value'] == old($key, $field['value'])) selected @elseif(is_null(old($key, $field['value'])) && isset($field['default']) && $option['value'] == $field['default']) selected @endif>{{ $option['name'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                        @else
                                            {{-- regular select --}}
                                            @foreach($field['options'] as $option)
                                                <option value="{{ $option['value'] }}" @if($option['value'] == old($key, $field['value'])) selected @elseif(is_null(old($key, $field['value'])) && isset($field['default']) && $option['value'] == $field['default']) selected @endif>{{ $option['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @break('select')
                            @endswitch
                        </div>
                    @endforeach
                    <div class="col-xs-12 col-md-3 col-sm-4 mb-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-search"></i> {{__('custom.search')}}
                        </button>
                        <a href="{{route('admin.users')}}" class="btn btn-default">
                            <i class="fas fa-eraser"></i> {{__('custom.clear')}}
                        </a>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endif
