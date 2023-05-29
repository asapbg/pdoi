<h5 class="bg-primary py-1 px-2 my-4">{{ __('custom.users.individual_permissions') }}</h5>
<table>
    @php($userPerms = isset($item) ? $item->permissions->pluck('id')->toArray() : [])
    @if(isset($perms) && sizeof($perms))
        @foreach($perms as $group => $permissions)
            @php($groupTitle = __('custom.'.$group))
            @if(!empty($groupTitle))
                <tr><th class="pt-3">{{ $groupTitle }}</th></tr>
            @endif
            @if(sizeof($permissions))
                @foreach($permissions as $perm)
                    <tr>
                        <td class="@if(!empty($groupTitle)){{ 'pl-4' }}@endif">
                            <label class="font-weight-normal" role="button">
                                <input class="user_permission"
                                       type="checkbox"
                                       name="permissions[]"
                                       value="{{ $perm->id }}"
                                       @if(in_array($perm->id, old('permissions', $userPerms))) checked @endif
                                       data-group="{{ $group }}"
                                       data-main="{{ $perm->is_main }}"
                                       data-full="{{ (int)empty($groupTitle) }}"
                                >
                                {{ $perm->display_name }}
                            </label>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    @else
        <tr class="text-danger">Не са открити права, които можете да предоставите на потребителя.</tr>
    @endif
</table>
