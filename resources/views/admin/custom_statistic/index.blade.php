@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form')

            <div class="card">
                <div class="card-body table-responsive">

                    <div class="mb-3">

{{--                        @includeIf('partials.status', ['action' => 'App\Http\Controllers\Admin\PageController@index'])--}}

                        <a href="{{ route($editRouteName, 0) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus-circle"></i> {{ __('custom.add') }} {{ $title_singular }}
                        </a>
                    </div>

                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{__('validation.attributes.name')}}</th>
                            <th>{{ __('custom.date_public') }}</th>
                            <th>{{ __('custom.date_unpublic') }}</th>
                            <th>{{__('custom.author')}}</th>
                            <th>{{__('custom.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ displayDate($item->publish_from) }}</td>
                                    <td>{{ !empty($item->publish_to) ? displayDate($item->publish_to) : 'Безсрочно' }}</td>
                                    <td>{{ $item->author ? $item->author->fullName() : '---' }}</td>
                                    <td class="text-center">
                                        @can('update', $item)
                                            <a href="{{ route( $editRouteName , $item->id) }}"
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


