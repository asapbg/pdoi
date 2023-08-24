@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form')

            <div class="card">
                <div class="card-body table-responsive">

                    <div class="mb-3">

                        @includeIf('partials.status', ['action' => 'App\Http\Controllers\Admin\PdoiResponseSubjectController@index'])

                        <a href="{{ route($editRouteName) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus-circle"></i> {{ __('custom.add') }} {{ $title_singular }}
                        </a>
                        <a href="{{ route('admin.rzs.import') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-import"></i> {{ __('custom.rzs.import_subjects') }}
                        </a>
                    </div>

                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{__('validation.attributes.eik')}}</th>
                            <th>{{__('validation.attributes.name')}}</th>
                            <th>{{__('validation.attributes.adm_level')}}</th>
                            <th>{{__('validation.attributes.address')}}</th>
                            <th>{{__('custom.active_m')}}</th>
                            <th>{{__('custom.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->eik }}</td>
                                    <td>{{ $item->subject_name }}</td>
                                    <td>{{ $item->section ? $item->section->name : '' }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td>
                                        @if(isset($toggleBooleanModel))
                                            @includeIf('partials.toggle-boolean', ['object' => $item, 'model' => $toggleBooleanModel])
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @canany(['update', 'updateSettings'], $item)
                                            <a href="{{ route( $editRouteName , [$item->id]) }}"
                                               class="btn btn-sm btn-info"
                                               data-toggle="tooltip"
                                               title="{{ __('custom.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $item)
                                                <a href="javascript:;"
                                                   class="btn btn-sm btn-danger js-toggle-delete-resource-modal hidden"
                                                   data-target="#modal-delete-resource"
                                                   data-resource-id="{{ $item->id }}"
                                                   data-resource-name="{{ "$item->name" }}"
                                                   data-resource-delete-url="{{ route($deleteRouteName,[$item->id]) }}"
                                                   data-toggle="tooltip"
                                                   title="{{ __('custom.deletion') }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="card-footer mt-2">
                    @if(isset($items) && $items->count() > 0)
                        {{ $items->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>

            @includeIf('modals.delete-resource', ['resource' => $title_singular])
        </div>
    </section>

@endsection


