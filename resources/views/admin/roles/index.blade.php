@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <div class="mb-3">

                        @includeIf('partials.status', ['action' => 'App\Http\Controllers\Admin\RolesController@index'])

                        <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus-circle"></i> {{__('custom.add')}} {{$title_singular}}
                        </a>
                    </div>

                    <table class="table table-sm table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{__('validation.attributes.name')}}</th>
                            <th>{{__('validation.attributes.alias')}}</th>
                            <th>{{__('custom.active_f')}}</th>
                            <th>{{__('validation.attributes.created_at')}}</th>
                            <th>{{__('validation.attributes.updated_at')}}</th>
                            <th>{{__('custom.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($roles) && $roles->count() > 0)
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->display_name }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @includeIf('partials.toggle-boolean', ['object' => $role, 'model' => 'Role'])
                                    </td>
                                    <td>{{ $role->created_at }}</td>
                                    <td>{{ $role->updated_at }}</td>
                                    <td>
                                        <a href="{{route('admin.roles.edit',$role->id)}}"
                                           class="btn btn-sm btn-info"
                                           data-toggle="tooltip"
                                           title="{{__('custom.edit')}}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($role->users_count == 0 && !$role->trashed())
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-danger js-toggle-delete-resource-modal"
                                               data-target="#modal-delete-resource"
                                               data-resource-id="{{ $role->id }}"
                                               data-resource-name="{{ $role->display_name }}"
                                               data-resource-delete-url="{{route('admin.roles.delete',$role->id)}}"
                                               data-toggle="tooltip"
                                               title="{{__('custom.deletion')}}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="card-footer mt-2">
                    @if(isset($roles) && $roles->count() > 0)
                        {{ $roles->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </section>

    @includeIf('modals.delete-resource', ['resource' => $title_singular])

@endsection
