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
                            @include('admin.partial.edit_field_translate', ['field' => 'content', 'required' => false])
                            <div class="col-12 mb-md-3"></div>
                            @include('admin.partial.edit_field_translate', ['field' => 'meta_title', 'required' => false])
                            @include('admin.partial.edit_field_translate', ['field' => 'meta_description', 'required' => false])
                            @include('admin.partial.edit_field_translate', ['field' => 'meta_keyword', 'required' => false])
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="slug">
                                        {{ __('validation.attributes.slug') }}
                                    </label>
                                    <div class="col-12">
                                        <input name="slug" value="{{ old('slug', $item->id ? $item->slug : '') }}" class="form-control form-control-sm @error('slug'){{ 'is-invalid' }}@enderror">
                                        @error('slug')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12"></div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="section">
                                        {{ __('validation.attributes.section') }}
                                    </label>
                                    <div class="col-12">
                                        <select id="section" name="section"  class="form-control form-control-sm @error('section'){{ 'is-invalid' }}@enderror">
                                            <option value="" @if(old('section', $item->id ? $item->parent_id ?? '' : '') == '') selected @endif>---</option>
                                            @if(isset($sections) && sizeof($sections))
                                                @foreach($sections as $row)
                                                    <option value="{{ $row->id }}" @if(old('section', ($item->id ? $item->parent_id ?? '' : '')) == $row->id) selected @endif>{{ $row->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('section')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="active">
                                        {{ __('validation.attributes.status') }}
                                    </label>
                                    <div class="col-12">
                                        <select id="active" name="active"  class="form-control form-control-sm @error('active'){{ 'is-invalid' }}@enderror">
                                            @foreach(optionsStatuses() as $val => $name)
                                                <option value="{{ $val }}" @if(old('active', ($item->id ? $item->active : 1)) == $val) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('active')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="order_idx">
                                        {{ __('validation.attributes.order_idx') }}
                                    </label>
                                    <div class="col-12">
                                        <input typeof="number" step="1" name="order_idx" value="{{ old('order_idx', $item->id ? $item->order_idx : 0) }}" class="form-control form-control-sm @error('order_idx'){{ 'is-invalid' }}@enderror">
                                        @error('order_idx')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

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
