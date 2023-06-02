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
                            @include('admin.partial.edit_field_translate', ['field' => 'ime', 'required' => true])

                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="obstina">{{ __('validation.attributes.obstina_code') }}<span class="required">*</span></label>
                                    <div class="col-12">
                                        <input type="text" id="obstina" name="obstina" class="form-control form-control-sm @error('obstina'){{ 'is-invalid' }}@enderror" value="{{ old('obstina', (isset($item) ? $item->obstina : '')) }}">
                                        @error('obstina')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="ekatte">{{ __('validation.attributes.ekatte') }}<span class="required">*</span></label>
                                    <div class="col-12">
                                        <input type="number" id="ekatte" step="1" name="ekatte" class="form-control form-control-sm @error('ekatte'){{ 'is-invalid' }}@enderror" value="{{ old('ekatte', ($item->id ? $item->ekatte : '')) }}">
                                        @error('ekatte')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="category">{{ __('validation.attributes.category') }}</label>
                                    <div class="col-12">
                                        <input type="text" id="category" name="category" class="form-control form-control-sm @error('category'){{ 'is-invalid' }}@enderror" value="{{ old('category', (isset($item) ? $item->category : '')) }}">
                                        @error('category')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="document">{{ __('validation.attributes.ekkate_document') }}</label>
                                    <div class="col-12">
                                        <input type="text" id="document" name="document" class="form-control form-control-sm @error('document'){{ 'is-invalid' }}@enderror" value="{{ old('document', (isset($item) ? $item->document : '')) }}">
                                        @error('document')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label" for="abc">{{ __('validation.attributes.abc') }}</label>
                                    <div class="col-12">
                                        <input type="text" id="abc" name="abc" class="form-control form-control-sm @error('abc'){{ 'is-invalid' }}@enderror" value="{{ old('abc', (isset($item) ? $item->abc : '')) }}">
                                        @error('abc')
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
