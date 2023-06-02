<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
    <div class="col-md-3 col-12 mt-4">
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

    <div class="col-12"></div>
    @include('admin.partial.edit_field_translate', ['field' => 'name'])
</div>
