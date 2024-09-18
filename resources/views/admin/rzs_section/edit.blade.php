@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    @php($storeRoute = route($storeRouteName, [$item]))
                    <form action="{{ $storeRoute }}" method="post" name="form" id="form">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">

                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
                            <div class="col-md-4 col-12 mt-1">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label"">{{ __('validation.attributes.adm_level') }}<span class="required">*</span></label>
                                    <div class="col-12">
                                        <select id="parent_id" name="parent_id"  class="form-control form-control-sm select2 @error('parent_id'){{ 'is-invalid' }}@enderror">
                                            <option value="">---</option>
                                            @if(isset($rzsSections) && $rzsSections->count())
                                                @foreach($rzsSections as $row)
                                                    <option value="{{ $row->id }}" @if(old('parent_id', ($item->id ? $item->parent_id : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('parent_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mt-1">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">
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

                            <div class="col-12"></div>
                            @include('admin.partial.edit_field_translate', ['field' => 'name'])
                        </div>



                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                <a href="{{ route($listRouteName) }}"
                                   class="btn btn-primary">{{ __('custom.back') }}</a>
                            </div>
                        </div>
                        <br/>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
