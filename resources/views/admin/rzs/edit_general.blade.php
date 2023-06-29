<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
    <div class="col-md-3 col-12 mt-1">
        <div class="form-group">
            <label class="col-sm-12 control-label">{{ __('validation.attributes.eik') }}<span class="required">*</span></label>
            <div class="col-12">
                <input type="text" id="eik" step="1" name="eik" class="form-control form-control-sm @error('eik'){{ 'is-invalid' }}@enderror" value="{{ old('eik', ($item->id ? $item->eik : '')) }}">
                @error('eik')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mt-1">
        <div class="form-group">
            <label class="col-sm-12 control-label"">{{ __('validation.attributes.adm_level') }}<span class="required">*</span></label>
            <div class="col-12">
                <select id="adm_level" name="adm_level"  class="form-control form-control-sm select2 @error('adm_level'){{ 'is-invalid' }}@enderror">
                    @if(!$item->id)
                        <option value="">---</option>
                    @endif
                    @if(isset($rzsSections) && $rzsSections->count())
                        @foreach($rzsSections as $row)
                            <option value="{{ $row->id }}" @if(old('adm_level', ($item->id ? $item->adm_level : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('adm_level')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-9 col-12 mt-1">
        <div class="form-group">
            <label class="col-sm-12 control-label" >{{ __('validation.attributes.parent_id') }}</label>
            <div class="col-12 mb-2 d-flex">
                <select id="subjects" name="parent_id"  class="custom-select form-control form-control-sm select2 @error('parent_id'){{ 'is-invalid' }}@enderror">
                    @if(!$item->parent_id)
                        <option value="">---</option>
                    @endif
                    @if(isset($subjects) && $subjects->count())
                        @foreach($subjects as $row)
                            <option value="{{ $row->id }}" @if(old('parent_id', ($item->id ? $item->parent_id : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                        @endforeach
                    @endif
                </select>
                <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject"
                        data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                        data-url="{{ route('modal.pdoi_subjects').'?redirect_only=0&select=1&multiple=0&admin=1' }}">
                    <i class="fa fa-list"></i>
                </button>
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
    @include('admin.partial.edit_field_translate', ['field' => 'subject_name'])
</div>
