@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive">

                    <div class="mb-3">

                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-success mb-1">
                            <i class="fas fa-plus-circle"></i> {{__('custom.add')}} {{ l_trans($title_singular) }}
                        </a>

                    </div>

                    <table class="table table-sm table-hover table-bordered no-striped">
                        <thead class="fix-border">
                            <th>Права/Роли</th>
                            @foreach($roles as $role)
                                <th>{{$role->display_name}}</th>
                            @endforeach
                            <th>Действия</th>
                        </thead>
                        <tbody>

                        @foreach($perms as $group => $permissions)
                            @php($groupTitle = __('custom.'.$group))
                            @if(!empty($groupTitle))
                                <tr>
                                   <td colspan="4" class="border-5 border-primary border-top-0 border-left-0 border-right-0 pt-4"><i class="fa fa-layer-group text-primary mr-2"></i><strong>{{ __('custom.'.$group) }}</strong></td>
                                </tr>
                            @endif
                            @foreach($permissions as $perm)
                                <tr class="@if(empty($groupTitle)){{ 'full-access bg-primary' }}@endif">
                                    <td>
                                        @if($perm->display_name)
                                            {{ $perm->display_name }}
                                        @else
                                            {{trans('custom.permissions_list.'.$perm->name)}}
                                        @endif
                                    </td>

                                    @foreach($roles as $role)
                                        <td>
                                            <input type="checkbox" class="js-toggle-role-permission"
                                                   name="permissions[]"
                                                   data-role="{{$role->id}}"
                                                   data-permission="{{$perm->id}}"
                                                   data-url="{{route('admin.permissions.roles')}}"
                                                   data-toggle="toggle"
                                                   data-on="Да"
                                                   data-off="НЕ"
                                                   data-onstyle="olive"
                                                   data-size="sm" {{$perm->hasRole($role->id) ? 'checked' : null}}
                                                   data-roleName="{{ $role->name }}"
                                                   data-group="{{ $group }}"
                                                   data-main="{{ $perm->is_main }}"
                                                   data-full="{{ (int)empty($groupTitle) }}"
                                            >
                                        </td>
                                    @endforeach
                                    <td>
                                        @if ($perm->users()->count() && $perm->roles()->count() == 0 && !str_contains($perm->name, '.*'))
                                            <a href="{{route('admin.permissions.edit',$perm->id)}}"
                                               class="btn btn-sm btn-warning"
                                               data-toggle="tooltip"
                                               title="{{__('custom.edit')}}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-danger js-toggle-delete-resource-modal"
                                               data-target="#modal-delete-resource"
                                               data-resource-id="{{ $perm->id }}"
                                               data-resource-name="{{ $perm->title }}"
                                               data-resource-delete-url="{{route('admin.permissions.delete',$perm->id)}}"
                                               data-toggle="tooltip"
                                               title="{{__('custom.delete')}}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr class="@if(empty($groupTitle))@endif">
                            @endforeach
                        @endforeach
                        </tbody>
                        <tfoot class="fix-border">
                            <th>Права/Роли</th>
                            @foreach($roles as $role)
                                <th>{{$role->display_name}}</th>
                            @endforeach
                            <th>Действия</th>
                        </tfoot>
                    </table>
                </div>
            </div>

            @includeIf('modals.delete-resource', ['resource' => $title_singular])
        </div>
    </section>
@endsection
