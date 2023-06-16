@if(isset($filter) && count($filter))
    <div class="card @if(isset($filterClass)){{ $filterClass }}@endif">
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
                        <div class="{{ $field['col'] ?? 'col-md-6' }} col-12 mb-2  @if($field['type'] == 'subjects') d-flex @endif">
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
                                    <label>
                                        <input type="checkbox" name="{{ $key }}" @if($field['checked']) checked @endif
                                        value="{{ $field['value'] }}" >
                                        {{ $field['label'] }}
                                    </label>

                                @break('checkbox')
                                @case('select')
                                    <select class="form-control form-control-sm select2 @if(isset($field['class'])){{$field['class'] }}@endif" name="{{ $key }}" >
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
                                @case('subjects')
{{--                                <div class="input-group input-group-sm d-flex">--}}
                                    <select class="custom-select form-control form-control-sm select2 @if(isset($field['class'])){{$field['class'] }}@endif"
                                            @if(isset($field['multiple']) && $field['multiple']) multiple="multiple" @endif name="{{ $key }}" id="subjects"
                                            data-placeholder="{{ $field['placeholder'] }}">
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option['value'] }}" @if($option['value'] == old($key, $field['value'])) selected @elseif(is_null(old($key, $field['value'])) && isset($field['default']) && $option['value'] == $field['default']) selected @endif>{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject"
                                            data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                            data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=0&admin=1' }}">
                                        <i class="fa fa-list"></i>
                                    </button>
{{--                                </div>--}}
                                @break('subjects')
                            @endswitch
                        </div>
                    @endforeach
                    <div class="col-xs-12 col-md-3 col-sm-4 mb-2">
                        <button type="submit" name="search" value="1" class="btn btn-sm btn-success">
                            <i class="fa fa-search"></i> {{ __('custom.search') }}
                        </button>
                        @if(isset($listRouteName))
                            <a href="{{ route($listRouteName) }}" class="btn btn-sm btn-default">
                                <i class="fas fa-eraser"></i> {{ __('custom.clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </form>
    </div>
@endif
