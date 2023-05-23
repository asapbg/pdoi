@php
    $boolean = (isset($boolean)) ? $boolean : "active";
@endphp
<div id="{{ $boolean }}_form_{{$object->id}}">
    <input type="hidden" name="id" class="id" value="{{$object->id}}">
    <input type="hidden" name="model" class="model" value="{{ $model }}">
    <div class="status-box">
        @if ($object->$boolean)
            <span class="badge badge-success status" style="cursor: pointer"
                  data-status="0"
                  onclick="ConfirmToggleBoolean('{{ $boolean }}','{{ $object->id }}','{{ __('custom.are_you_sure_to_make_not_'.$boolean)." ".$object->getModelName() }}')">
                {{__('custom.yes')}}
            </span>
        @else
            <span class="badge badge-danger status" style="cursor: pointer"
                  data-status="1"
                  onclick="ConfirmToggleBoolean('{{ $boolean }}','{{ $object->id }}', '{{ __('custom.are_you_sure_to_make_'.$boolean)." ".$object->getModelName() }}')">
                {{__('custom.no')}}
            </span>
        @endif
    </div>
</div>
