@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ct-general-tab" data-toggle="pill" href="#ct-general" role="tab" aria-controls="ct-general" aria-selected="true">{{ __('custom.general_info') }}</a>
                        </li>
                        @if($item->id)
                            <li class="nav-item">
                                <a class="nav-link" id="ct-files-tab" data-toggle="pill" href="#ct-files" role="tab" aria-controls="ct-files" aria-selected="false">{{ trans_choice('custom.files',2) }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabsContent">
                        <div class="tab-pane fade active show" id="ct-general" role="tabpanel" aria-labelledby="ct-general-tab">
                            @php($storeRoute = route($storeRouteName, ['item' => $item]))
                            <form action="{{ $storeRoute }}" method="post" name="form" id="form">
                                @csrf
                                @if($item->id)
                                    @method('PUT')
                                @endif
                                <input type="hidden" name="id" value="{{ $item->id ?? 0 }}">
                                @if($item->id && !empty($item->system_name))
                                    <input type="hidden" name="active" value="{{ $item->active }}">
                                    <input type="hidden" name="order_idx" value="0">
                                @endif

                                <div class="row mb-4">
                                    @include('admin.partial.edit_field_translate', ['field' => 'name', 'required' => true])
                                    @include('admin.partial.edit_field_translate', ['field' => 'short_content', 'required' => false])
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
                                    @if(!$item->id || empty($item->system_name))
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
                                                                <option value="{{ $row->id }}" @if(old('section', ($item->id ? $item->section_id ?? '' : '')) == $row->id) selected @endif>{{ $row->name }}</option>
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
                        @if($item->id)
                            <div class="tab-pane fade" id="ct-files" role="tabpanel" aria-labelledby="ct-files-tab">
                                <form action="{{ route('admin.upload.file', ['object_id' => $item->id, 'object_type' => \App\Models\File::CODE_OBJ_PAGE]) }}" method="post" name="form" id="form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Публично име <span class="required">*</span> </label>
                                        <input value="{{ old('description', '') }}" class="form-control form-control-sm @error('description') is-invalid @enderror" id="description" type="text" name="description">
                                        @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Изберете файл <span class="required">*</span> </label>
                                        <input class="form-control form-control-sm @error('file') is-invalid @enderror" id="file" type="file" name="file">
                                        @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button id="save" type="submit" class="btn btn-success">{{ __('custom.save') }}</button>
                                </form>
                                @if($item->files)
                                    <table class="table table-sm table-hover table-bordered mt-4">
                                        <tbody>
                                        <tr>
                                            <th>Име</th>
                                            <th></th>
                                        </tr>
                                        @foreach($item->files as $f)
                                            <tr>
                                                <td>{{ $f->description }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-secondary" type="button" target="_blank" href="{{ route('download.page.file', ['file' => $f->id]) }}">
                                                        <i class="fas fa-download me-1" role="button"
                                                           data-toggle="tooltip" title="{{ __('custom.download') }}"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-danger" type="button" href="{{ route('admin.delete.file', ['file' => $f->id]) }}">
                                                        <i class="fas fa-trash me-1" role="button"
                                                           data-toggle="tooltip" title="{{ __('custom.delete') }}"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
