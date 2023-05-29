<h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.roles', 2) }}</h5>
@php($userRoles = isset($item) ? $item->roles->pluck('id')->toArray() : [])
{{--                                <label class="control-label" for="roles">{{ trans_choice('custom.roles', 2) }}</label>--}}
@foreach($roles as $role)
    <div class="icheck-primary">
        <input class="roles"
               type="checkbox"
               name="roles[]"
               id="role_{{ $role->id }}"
               value="{{ $role->id }}"
               @if(in_array($role->id, old('roles', $userRoles))) checked @endif
        >
        <label for="role_{{ $role->id }}">{{ $role->display_name }}</label>
    </div>
@endforeach
@error('roles')
<div class="text-danger mt-1">{{ $message }}</div>
@enderror
