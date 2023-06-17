@if(isset($filter) && count($filter))
    <div class="accordion app-accordion mb-4 @if(isset($filterClass)){{ $filterClass }}@endif" id="accordionExample">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button text-white py-1 pb-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fa-solid fa-search me-2"></i> Филтри
                </button>
            </h4>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <form class="row form-filter">
                        @foreach($filter as $key => $field)
                            <div class="mb-3 {{ $field['col'] ?? 'col-md-6' }} col-12 mb-3">
                                <div class="input-group">
                                    @switch($field['type'])
                                        @case('text')
                                                <input type="text" name="{{ $key }}" autocomplete="off" class="form-control form-control-sm"
                                                       value="{{ old($key, $field['value']) }}" placeholder="{{ $field['placeholder'] }}">
                                        @break('text')
                                        @case('datepicker')
                                                <input type="text" name="{{ $key }}" autocomplete="off" readonly value="{{ old($key, $field['value']) }}"
                                                       class="form-control form-control-sm datepicker" placeholder="{{ $field['placeholder'] }}">
                                                <span class="input-group-text" id="basic-addon2"><i class="fa-solid fa-calendar"></i></span>
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
                                            <select class="form-select select2 @if(isset($field['class'])){{$field['class'] }}@endif"
                                            @if(isset($field['multiple']) && $field['multiple']) multiple="multiple" @endif name="{{ $key }}" id="subjects"
                                                    data-placeholder="{{ $field['placeholder'] }}">
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option['value'] }}" @if($option['value'] == old($key, $field['value'])) selected @elseif(is_null(old($key, $field['value'])) && isset($field['default']) && $option['value'] == $field['default']) selected @endif>{{ $option['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject rounded"
                                                    data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                                                    data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=0' }}">
                                                <i class="fa-solid fa-list"></i>
                                            </button>
                                        @break('subjects')
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                        <div class="mb-3 col-md-4 col-12 mb-3">
                            <button type="submit" name="search" value="1" class="btn btn-sm btn-success d-inline w-auto">
                                <i class="fa fa-search"></i> {{ __('custom.search') }}
                            </button>
                            @if(isset($listRouteName))
                                <a href="{{ route($listRouteName) }}" class="btn btn-sm btn-default d-inline">
                                    <i class="fas fa-eraser"></i> {{ __('custom.clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
