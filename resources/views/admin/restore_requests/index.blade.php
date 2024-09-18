@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form')

            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ trans_choice('custom.applications', 1) }}</th>
                            <th>{{ trans_choice('custom.rzs_items', 1) }}</th>
                            <th>{{ __('custom.status') }}</th>
                            <th>{{ __('custom.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->application->getModelName() }}</td>
                                    <td>{{ $item->application->responseSubject->subject_name }}</td>
                                    <td>
                                        {{ $item->statusName }}<br><span class="font-italic fs-12 text-primary">({{ displayDate($item->status_datetime).($item->statusUser ? ' - '.$item->statusUser->fullName() : '') }})</span>
                                    </td>
                                    <td class="text-center">
                                        @can('update', $item)
                                            <a href="{{ route( $editRouteName , [$item->id]) }}"
                                               class="btn btn-sm btn-info"
                                               data-toggle="tooltip"
                                               title="{{ __('custom.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                            @cannot('update', $item)
                                                <a href="{{ route( 'admin.restore_requests.view' , [$item->id]) }}"
                                                   class="btn btn-sm btn-info"
                                                   data-toggle="tooltip"
                                                   title="{{ __('custom.view') }}">
                                                    <i class="fa fa-eye"></i>
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


