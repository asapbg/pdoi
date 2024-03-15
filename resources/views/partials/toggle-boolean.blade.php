@php
    $boolean = (isset($boolean)) ? $boolean : "active";
@endphp
<div id="{{ $boolean }}_form_{{$object->id}}">
    <input type="hidden" name="id" class="id" value="{{$object->id}}">
    <input type="hidden" name="model" class="model" value="{{ $model }}">
    <div class="status-box">
        @if ($object->$boolean)
            <span class="badge badge-success status @if(!isset($disable_btn) || !$disable_btn) toggle-boolean @endif" @if(!isset($disable_btn) || !$disable_btn) style="cursor: pointer" @endif
                  data-status="0"
                  data-btype="{{ $boolean }}"
                  data-entityid="{{ $object->id }}"
                  data-message="{{ __('custom.are_you_sure_to_make_not_'.$boolean)." ".$object->getModelName() }}"
{{--                  @if(!isset($disable_btn) || !$disable_btn)--}}
{{--                    onclick="ConfirmToggleBoolean('{{ $boolean }}','{{ $object->id }}','{{ __('custom.are_you_sure_to_make_not_'.$boolean)." ".$object->getModelName() }}')"--}}
{{--                  @endif--}}
            >
                {{__('custom.yes')}}
            </span>
        @else
            <span class="badge badge-danger status @if(!isset($disable_btn) || !$disable_btn) toggle-boolean @endif" @if(!isset($disable_btn) || !$disable_btn) style="cursor: pointer" @endif
                  data-status="1"
                  data-btype="{{ $boolean }}"
                  data-entityid="{{ $object->id }}"
                  data-message="{{ __('custom.are_you_sure_to_make_'.$boolean)." ".$object->getModelName() }}"
{{--                  @if(!isset($disable_btn) || !$disable_btn)--}}
{{--                    onclick="ConfirmToggleBoolean('{{ $boolean }}','{{ $object->id }}', '{{ __('custom.are_you_sure_to_make_'.$boolean)." ".$object->getModelName() }}')"--}}
{{--                    @endif--}}
            >
                {{__('custom.no')}}
            </span>
        @endif
    </div>
</div>
