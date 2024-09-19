@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.notifications.store') }}" method="post" name="form" id="form">
                        @csrf
                        <div class="row mb-4">
                            <h5 class="bg-primary py-1 px-2 mb-4">Получатели</h5>
                            <div class="col-12">
                                <div class="form-group col-12 mb-3">
                                    <label class="form-label me-3 fw-semibold">
                                        <input type="checkbox" name="all" value="1" @if(old('all', 0) == 1) checked @endif id="all" data-clear="users">
                                        До всички
                                    </label>
                                    @error('all')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group col-12 mb-3">
                                    <label class="form-label me-3 fw-semibold">
                                        Вътрешни потребители
                                    </label>
                                    <select class="form-control form-control-sm select2 select_with_all_checkbox @error('users') is-invalid @enderror" name="users[]" id="users" multiple="multiple" data-clear="all">
{{--                                        <option value=""></option>--}}
                                        @if(isset($recipients) && sizeof($recipients))
                                            @foreach($recipients as $row)
                                                <option value="{{ $row->id }}" @if(in_array($row->id, old('users', []))) selected @endif>
                                                    {{ $row->fullName() }}
                                                    @if($row->roles->count())
                                                        ({{ implode(', ', $row->roles->pluck('display_name')->toArray()) }})
                                                    @endif
                                                    @if($row->responseSubject)
                                                        / {{ $row->responseSubject->subject_name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('users')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">Начин на изпращане</h5>
                            <div class="col-auto mb-3">
                                <div class="form-group col-12">
                                    <label class="form-label me-3 fw-semibold mb-0">
                                        <input type="checkbox" name="mail" value="1" @if(old('mail', 0) == 1) checked @endif>
                                        Ел. поща
                                    </label>
                                </div>
                                @error('mail')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-auto mb-3">
                                <div class="form-group col-12">
                                    <label class="form-label me-3 fw-semibold mb-0">
                                        <input type="checkbox" name="db" value="1" @if(old('db', 0) == 1) checked @endif>
                                        Вътрешно съобщение
                                    </label>
                                </div>
                                @error('db')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <h5 class="bg-primary py-1 px-2 mb-4">Съобщение</h5>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label class="form-label me-3 fw-semibold">
                                            Относно
                                        </label>
                                        <input type="text" class="form-control form-control-sm @error('subject') is-invalid @enderror" name="subject"  value="{{ old('subject', '') }}">
                                        @error('subject')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label class="form-label me-3 fw-semibold">
                                            Съдържание
                                        </label>
                                        @php($request = old('msg', ''))
                                        <textarea class="form-control summernote w-100 @error('msg') is-invalid @enderror" name="msg" >{{ $request }}</textarea>
                                        @error('msg')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="save" type="submit" class="btn btn-success">Изпрати</button>
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
