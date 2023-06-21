@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @php($storeRoute = route($storeRouteName, ['item' => $item]))
                    <form action="{{ $storeRoute }}" method="post" name="form" id="form">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">

                        <div class="row mb-4">
                            @include('admin.partial.edit_field_translate', ['field' => 'name', 'required' => true])

                            @if(auth()->user()->hasRole(\App\Models\CustomRole::SUPER_USER_ROLE))
                                <div class="col-md-2 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="active">app_event</label>
                                        <div class="col-12">
                                            <input name="app_event" value="{{ old('active', ($item->id ? $item->app_event : '')) }}" class="form-control form-control-sm @error('app_event'){{ 'is-invalid' }}@enderror" type="number" step="1">
                                            @error('app_event')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="app_status">app_status</label>
                                        <div class="col-12">
                                            <select name="app_status"  class="form-control form-control-sm @error('app_status'){{ 'is-invalid' }}@enderror">
                                                @foreach(optionsApplicationStatus(true) as $op)
                                                    <option value="{{ $op['value'] }}" @if(old('app_status', ($item->id ? $item->app_status : '')) == $op['value']) selected @endif>{{ $op['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('app_status')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="extend_terms_reason_id">extend_terms_reason_id</label>
                                        <div class="col-12">
                                            <select name="extend_terms_reason_id"  class="form-control form-control-sm @error('extend_terms_reason_id'){{ 'is-invalid' }}@enderror">
                                                @foreach($extendReasons as $op)
                                                    <option value="{{ $op->id }}" @if(old('extend_terms_reason_id', ($item->id ? $item->extend_terms_reason_id : '')) == $op->name) selected @endif>{{ $op->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('extend_terms_reason_id')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="days">days</label>
                                        <div class="col-12">
                                            <input name="days" value="{{ old('days', ($item->id ? $item->days : '')) }}" class="form-control form-control-sm @error('days'){{ 'is-invalid' }}@enderror" type="number" step="1">
                                            @error('days')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="new_resp_subject">event_status</label>
                                        <div class="col-12">
                                            <select name="event_status"  class="form-control form-control-sm @error('event_status'){{ 'is-invalid' }}@enderror">
                                                @foreach(['' => '', \App\Models\Event::EVENT_STATUS_COMPLETED => 'Изпълнено', \App\Models\Event::EVENT_STATUS_NOT_COMPLETED => 'Неизпълнено'] as $key => $value)
                                                    <option value="{{ $key }}" @if(old('event_status', ($item->id ? $item->event_status : '')) == $key) selected @endif>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('event_status')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="new_resp_subject">date_type</label>
                                        <div class="col-12">
                                            <select name="date_type"  class="form-control form-control-sm @error('date_type'){{ 'is-invalid' }}@enderror">
                                                @foreach(['' => '', \App\Models\Event::DATE_TYPE_EVENT => 'Дата на събитието', \App\Models\Event::DATE_TYPE_SUBJECT_REGISTRATION => 'Дата на регистрация при ЗС'] as $key => $value)
                                                    <option value="{{ $key }}" @if(old('date_type', ($item->id ? $item->date_type : '')) == $key) selected @endif>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('date_type')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @foreach(['old_resp_subject', 'new_resp_subject', 'add_text', 'files', 'event_delete', 'mail_to_admin', 'mail_to_app', 'mail_to_new_admin'] as $field)
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label" for="{{ $field }}">{{ $field }}</label>
                                            <div class="col-12">
                                                <select name="{{ $field }}"  class="form-control form-control-sm @error($field){{ 'is-invalid' }}@enderror">
                                                    @foreach(['', 0, 1] as $op)
                                                        <option value="{{ $op }}" @if(old($field, ($item->id ? $item->{$field} : '')) == $op) selected @endif>
                                                            {{ $op != '' ? ( $op ? __('custom.yes') : __('custom.no') ) : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error($field)
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route($listRouteName) }}"
                                   class="btn btn-primary">{{ __('custom.cancel') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
