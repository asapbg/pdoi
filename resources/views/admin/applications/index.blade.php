@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            @include('admin.partial.filter_form', ['filterClass' => 'mt-3'])

            <div class="card">
                <div class="card-body table-responsive">
                    <div class="text-right">
                        <a href="{{ route('admin.application.create') }}" class="btn btn-sm btn-success d-inline-block">
                            <i class="fas fa-plus-circle"></i> {{ __('custom.application.add_external') }}
                        </a>
                    </div>
                    <div id="legend" class="mb-3">
                        <div class="d-inline-block app-f-small"><span class="badge badge-info ms-2 lh-1">&nbsp;</span> {{ __('custom.forwarded') }}</div>
                        <div class="d-inline-block app-f-small"><span class="badge badge-success ms-2 lh-1">&nbsp;</span> {{ __('custom.in_process') }}</div>
                        <div class="d-inline-block app-f-small"><span class="badge badge-warning ms-2 lh-1">&nbsp;</span> {{ __('custom.expired_term') }}</div>
                        <div class="d-inline-block app-f-small"><span class="badge badge-danger ms-2 lh-1">&nbsp;</span> {{ __('custom.not_approved') }}</div>
                    </div>
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
                                @include('admin.applications.row_list')
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="card-footer mt-2">
                    @if(isset($items) && $items->count() > 0)
                        {{ $items->withQueryString()->links() }}
                    @endif
                </div>
            </div>

{{--            @includeIf('modals.delete-resource', ['resource' => $title_singular])--}}
        </div>
    </section>

@endsection


