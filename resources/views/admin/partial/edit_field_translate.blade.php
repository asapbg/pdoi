@php($fieldProperties = isset($translatableFields) && sizeof($translatableFields) ? $translatableFields[$field] : [])
@if(sizeof($fieldProperties))
    @foreach(config('available_languages') as $language)
        @php($fieldName = $field.'_'.$language['code'])
        <div class="col-md-{{ $col ?? 6 }} col-12">
            <div class="form-group">
                <label class="col-sm-12 control-label" for="{{ $fieldName }}">{{ __('validation.attributes.'.$field) }} ({{ mb_strtoupper($language['code']) }})@if(isset($required) && $required)<span class="required">*</span>@endif</label>
                <div class="col-12">
                    @switch($fieldProperties['type'])
                        @case('textarea')
                            <textarea id="{{ $fieldName }}" name="{{ $fieldName }}"
                                      class="form-control form-control-sm @error($fieldName){{ 'is-invalid' }}@enderror">{{ old($fieldName, ($item->id ? $item->translate($language['code'])->{$field} : '')) }}</textarea>
{{--                            <input type="text" id="{{ $fieldName }}" name="{{ $fieldName }}"--}}
{{--                                   class="form-control form-control-sm @error($fieldName){{ 'is-invalid' }}@enderror"--}}
{{--                                   value="{{ old($fieldName, ($item->id ? $item->translate($language['code'])->{$field} : '')) }}">--}}
                        @break
                        @default
                            <input type="text" id="{{ $fieldName }}" name="{{ $fieldName }}"
                                   class="form-control form-control-sm @error($fieldName){{ 'is-invalid' }}@enderror"
                                   value="{{ old($fieldName, ($item->id ? $item->translate($language['code'])->{$field} : '')) }}">
                    @endswitch
                    @error($fieldName)
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    @endforeach
@endif
