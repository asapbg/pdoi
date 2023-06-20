@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="false">{{ trans_choice('custom.applications',1) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">{{ __('custom.answer') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="true">{{ trans_choice('custom.activity_logs',1) }}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                            <div class="row">
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.reg_number') }}:  <span class="text-primary">{{ $item->application_uri }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.status') }}:  <span class="text-primary">{{ $item->statusName }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ trans_choice('custom.pdoi_response_subjects', 1)  }}:  <span class="text-primary">{{ $item->responseSubject->subject_name }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.date_apply') }}: <span class="text-primary">{{ displayDate($item->created_at) }}</span></div>
                                <div class="col-md-4 col-12 fw-bold">{{ __('custom.term') }}: <span class="text-primary">{{ displayDate($item->response_end_time) }}</span></div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.legal_form') }}:</label> <br>
                                    <label class="form-label me-3" role="button">
                                        <input type="radio" disabled name="legal_form" @if($item->applicant_type == \App\Models\User::USER_TYPE_PERSON) checked @endif>
                                        {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_PERSON] }}
                                    </label>
                                    <label class="form-label" role="button">
                                        <input type="radio" disabled name="legal_form" @if($item->applicant_type == \App\Models\User::USER_TYPE_COMPANY) checked @endif>
                                        {{ \App\Models\User::getUserLegalForms()[\App\Models\User::USER_TYPE_COMPANY] }}
                                    </label>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold" disabled>{{ trans_choice('custom.profile_type', 1)  }}: </label>
                                    <select class="form-control form-control-sm" disabled readonly="">
                                        <option>{{ $item->profileType ? $item->profileType->name : '' }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.name') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->names }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-4 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.email') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->email }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.phone') }}:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->phone }}" disabled>
                                </div>
                                <hr>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.country',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled readonly="">
                                        <option>{{ $item->country->name }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.area',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->area->ime }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.municipality',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->municipality->ime }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-3 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ trans_choice('custom.settlement',1) }}: </label>
                                    <select class="form-control form-control-sm" disabled>
                                        <option>{{ $item->settlement->ime }}</option>
                                    </select>
                                </div>
                                <div class="form-group form-group-sm col-md-2 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('validation.attributes.post_code') }}:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->post_code }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.address') }}: </label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->address }}" disabled>
                                </div>
                                <div class="form-group form-group-sm col-md-6 col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('custom.address') }} 2:</label>
                                    <input class="form-control form-control-sm" type="text" value="{{ $item->address_second }}" disabled>
                                </div>
                                <hr>
                                <div class="form-group form-group-sm col-12 mb-3">
                                    <label class="form-label fw-semibold">{{ __('front.application.request_field.description') }}</label>
                                    <div class="w-100 border border-1 rounded-1 p-3" disabled>{!! html_entity_decode($item->request) !!}</div>
                                </div>
                                <hr>
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.documents',1) }}</h5>
                                <table class="table table-light table-sm table-bordered mb-4">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('front.file_name') }}</th>
                                        <th>{{ __('front.description') }}</th>
                                        <th>{{ __('custom.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if($item->files->count())
                                            @foreach($item->files as $file)
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ $file->filename }}</td>
                                                    <td>{{ $file->description }}</td>
                                                    <td>
                                                        <a class="btn btn-sm btn-secondary" type="button" href="{{ route('admin.download.file', ['file' => $file->id]) }}">
                                                            <i class="fas fa-download me-1 download-file" data-file="$file->id" role="button"
                                                               data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.categories',1) }}</h5>
                                @can('update', $item)
                                    <form class="mb-3" action="{{ route('admin.application.category.add') }}" method="post">
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                        <div class="d-flex flex-row col-md-6 col-12">
                                            @csrf
                                            @php($itemCategories = $item->categories->count() ? $item->categories->pluck('id')->toArray() : [])
                                            <select class="form-control form-control-sm select2 select2-info" name="categories[]" multiple>
                                                @if(isset($categories) && $categories->count())
                                                    @foreach($categories as $row)
                                                        @if(!in_array($row->id, $itemCategories))
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button class="btn btn-sm btn-info ms-1" type="submit" id="add-category">{{ __('custom.add') }}</button>
                                        </div>
                                    </form>
                                @endcan
                                @if($item->categories->count())
                                    <div class="input-group input-group-sm mb-3">
                                        @foreach($item->categories as $row)
                                            <span class="badge badge-info pill rounded-1 text-bg-light ms-2 fw-normal" style="font-size: 12px;">{{ $row->name }}
                                                <i class="fas fa-times remove-category" data-application="{{ $item->id }}" data-category="{{ $row->id }}"
                                                   role="button" data-toggle="tooltip" title="{{ __('custom.remove') }}"></i>
                                            </span>
                                        @endforeach
                                        <span class="text-danger" id="remove-category-error"></span>
                                    </div>
                                @endif

                                @can('update', $item)
                                    <h5 class="bg-primary py-1 px-2 my-4">{{ __('custom.new_event') }}</h5>
                                    <form class=" mb-3" action="post">
                                        @csrf
                                        <div class="input-group col-md-6 col-12">
                                            <select class="form-select form-select-sm" id="next-event">
                                                <option value="">{{ __('custom.available_actions') }}</option>
                                                @if($item->)
                                                <option value="{{ route('admin.application.event.new', ['item' => $item->id, 'event' => \App\Enums\ApplicationEventsEnum::ASK_FOR_INFO->value]) }}">Искане на допълнителна информация</option>
                                                <option value="{{ route('admin.application.event.new', ['item' => $item->id, 'event' => \App\Enums\ApplicationEventsEnum::FORWARD->value]) }}">Препращане на заявление</option>
                                                <option value="{{ route('admin.application.event.new', ['item' => $item->id, 'event' => \App\Enums\ApplicationEventsEnum::EXTEND_TERM->value]) }}">Удължаване на срок</option>
                                                <option value="{{ route('admin.application.event.new', ['item' => $item->id, 'event' => \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value]) }}">Крайно решение</option>
                                            </select>
                                            <a href="#" id="apply_event" role="button" class="btn btn-sm btn-success disabled">{{ __('custom.apply') }}</a>
                                        </div>
                                    </form>
                                @endcan
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                            Отговор
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab">
                            История
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-md-offset-3">
                            <a href="{{ route('admin.application') }}" class="btn btn-primary">{{ __('custom.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function (){
            $('#next-event').on('change', function (){
                $('#apply_event').attr('href', $(this).val());
                if( $(this).val().length ) {
                    $('#apply_event').removeClass('disabled');
                } else {
                    $('#apply_event').addClass('disabled');
                }
            });

            if( $('.remove-category').length ) {
                $('.remove-category').on('click', function (){
                    let errorContainer = $('#remove-category-error');
                    errorContainer.html('');
                    $.ajax({
                        url  : '<?php echo route("admin.application.category.remove"); ?>',
                        type : 'POST',
                        data : { _token: '{{ csrf_token() }}', id: $(this).data('application'), category: $(this).data('category') },
                        success : function(data) {
                            if( typeof data.error != 'undefined' ) {
                                errorContainer.html(data.message);
                            } else {
                                location.reload();
                            }
                        },
                        error : function() {
                            errorContainer.html('System error');
                        }
                    });
                });
            }
        });
    </script>
@endpush
