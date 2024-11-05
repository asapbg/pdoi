@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form')

            <div class="card">
                <div class="card-body table-responsive">
                    <div class="mb-3">
                        <a href="{{ route($editRouteName) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus-circle"></i> {{ $title_singular }}
                        </a>
                    </div>
                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Относно</th>
                            <th>Планирано на</th>
{{--                            <th>Получател</th>--}}
                            <th>Изпратено от</th>
{{--                            <th>Прочетено</th>--}}
                            <th>Тип</th>
                            <th>Изпратено</th>

                            <th>{{ __('custom.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->subject }}</td>
                                    <td>{{ displayDateTime($item->created_at) }}</td>
{{--                                    <td>{{ $item->notifiable?->fullName() }}</td>--}}
                                    <td>{{ $item->sender?->fullName() }}</td>
                                    <td>
                                        @if($item->by_email == 1)
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                        @endif
                                        @if($item->by_app == 1)
                                            <i class="fas fa-bell text-primary"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->send_at)
                                            {{ displayDateTime($item->send_at) }}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @can('manage.*')
                                            <a href="{{ route( 'admin.notifications.view' , $item->id) }}"
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


