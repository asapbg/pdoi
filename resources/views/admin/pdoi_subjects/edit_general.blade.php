<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="eik">{{ __('validation.attributes.eik') }}<span class="required">*</span></label>
            <div class="col-12">
                <input type="text" id="eik" name="eik" class="form-control form-control-sm @error('eik'){{ 'is-invalid' }}@enderror" value="{{ old('eik', (isset($item) ? $item->eik : '')) }}">
                @error('eik')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-9 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="adm_level">{{ __('validation.attributes.adm_level') }}</label>
            <div class="col-12">
                <select id="adm_level" name="adm_level"  class="form-control form-control-sm @error('adm_level'){{ 'is-invalid' }}@enderror">
                    @if(!$item->id)
                        <option value="-1">---</option>
                    @endif
                    @if($subjects->count())
                        @foreach($subjects as $row)
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

    @include('admin.pdoi_subjects.edit_field_translate', ['field' => 'subject_name', 'required' => true])

    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="date_from">{{ __('validation.attributes.date_from') }}<span class="required">*</span></label>
            <div class="col-12">
                <input type="text" id="date_from" readonly name="date_from" class="form-control form-control-sm datepicker @error('eik'){{ 'is-invalid' }}@enderror" value="{{ old('date_from', ($item->id ? $item->date_from : date('d-m-Y'))) }}">
                @error('date_from')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="date_to">{{ __('validation.attributes.date_to') }}</label>
            <div class="col-12">
                <input type="text" id="date_to" readonly name="date_to" class="form-control form-control-sm datepicker @error('date_to'){{ 'is-invalid' }}@enderror" value="{{ old('date_to', ($item->id ? $item->date_to : '')) }}">
                @error('date_to')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-12"></div>
    <div class="col-md-4 col-12 ps-md-3 pt-4">
        <div class="form-group">
            <label for="direct_addressing">
                <input class="pe-2" type="checkbox" name="direct_addressing" id="direct_addressing" value="1" @if(old('direct_addressing', ($item->id ? $item->direct_addressing : 0))) checked @endif>
                {{ __('validation.attributes.redirect_only') }}
                <i class="fas fa-info-circle text-info" data-toggle="tooltip" title="{{ __('custom.pdoi_subjects.redirect_only.tooltip') }}"></i>
            </label>
        </div>
    </div>
</div>
