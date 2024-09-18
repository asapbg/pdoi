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
                        <input type="hidden" name="type" value="{{ $item->id ? $item->type : 0 }}">

                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2 my-4">{{ __('custom.general_info') }}</h5>
                            <div class="col-md-4 col-12 mt-1">
                                <div class="form-group">
                                    <label for="name">{{ __('custom.name') }}</label>
                                    <input name="name" value="{{ old('name', $item->id ? $item->name : '') }}" type="text" class="form-control"
                                           id="name">
                                    @error('name')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <h5 class="bg-primary py-1 px-2 my-4">{{ __('custom.content') }}</h5>
                            <div class="col-12 mt-1">
                                <div class="form-group">
{{--                                    <label for="content">{{ __('custom.content') }}</label>--}}
                                    <div style="margin-bottom: 2px">
                                        <label style="font-style: italic;">{{ trans_choice('custom.attributes', 2) }}</label>
                                        @if($placeholders && count($placeholders) > 0)
                                            @foreach($placeholders as $k => $placeholder)
                                                <button type="button" id="{{$k}}" class="js-add-placeholder btn-info btn-sm mb-1"
                                                        data-placeholder="{{ $k }}">
                                                    {{ __('custom.mail_templates.placeholders.'.$placeholder['translation_key']) }}
                                                </button>
                                            @endforeach
                                        @endif
                                    </div>

                                    <textarea name="content"  type="text" class="summernote"
                                              id="content">{{ old('content', $item->id ? $item->content : '') }}</textarea>
                                    @error('content')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
@push('scripts')
    <script>
        $(document).ready(function () {
            $('.js-add-placeholder').on('click', function(e) {
                let placeholder = $(this).data('placeholder');
                $('.summernote').summernote('editor.insertText', ':' + placeholder);
            })
        });
    </script>
@endpush
