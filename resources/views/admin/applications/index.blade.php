@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form', ['filterClass' => 'mt-3'])

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('custom.reg_number') }}</th>
                            <th>{{ __('custom.date_apply') }}</th>
                            <th>{{ trans_choice('custom.pdoi_response_subjects', 1) }}</th>
                            <th>{{ __('custom.application.request_for_info') }}</th>
                            <th>{{ __('custom.status') }}</th>
                            <th>{{ __('custom.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->application_uri }}</td>
                                    <td>{{ displayDate($item->created_at) }}</td>
                                    <td>{{ $item->responseSubject->subject_name }}</td>
                                    <td>
                                        @php($itemContent = strip_tags(html_entity_decode($item->request)))
                                        {{ $itemContent }}@if(strlen($itemContent) > 500){{ '...' }}@endif
                                    </td>
                                    <td>{{ $item->statusName }}</td>
                                    <td class="text-center">
                                        @canany(['update', 'view'], $item)
                                            <a href="{{ route( 'admin.application.view' , [$item->id]) }}"
                                               class="btn btn-sm btn-info"
                                               data-toggle="tooltip"
                                               title="{{ __('custom.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="/application-full-history.html"
                                               class="btn btn-sm btn-warning"
                                               data-toggle="tooltip" title=""
                                               data-original-title="{{ __('custom.application.full_history') }}">
                                                <i class="fas fa-history"></i>
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

{{--            @includeIf('modals.delete-resource', ['resource' => $title_singular])--}}
        </div>
    </section>

@endsection


