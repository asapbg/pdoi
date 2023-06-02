<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.pdoi_subjects.address_section') }}</h5>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="adm_level">{{ __('validation.attributes.area') }}<span class="required">*</span></label>
            <div class="col-12">
                <select id="region" name="region"  class="form-control form-control-sm @error('region'){{ 'is-invalid' }}@enderror">
                    @if(!$item->id)
                        <option value="-1">---</option>
                    @endif
                    @if(isset($regions) && $regions->count())
                        @foreach($regions as $row)
                            <option value="{{ $row->id }}" @if(old('region', ($item->id ? $item->region : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('region')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="municipality">{{ __('validation.attributes.municipality') }}<span class="required">*</span></label>
            <div class="col-12">
                <select id="municipality" name="municipality"  class="form-control form-control-sm @error('municipality'){{ 'is-invalid' }}@enderror">
                    @if(!$item->id)
                        <option value="-1">---</option>
                    @endif
                    @if(isset($municipalities) && $municipalities->count())
                        @foreach($municipalities as $row)
                            <option value="{{ $row->id }}" @if(old('municipality', ($item->id ? $item->municipality : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('municipality')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="municipality">{{ __('validation.attributes.settlement') }}<span class="required">*</span></label>
            <div class="col-12">
                <select id="town" name="town"  class="form-control form-control-sm @error('municipality'){{ 'is-invalid' }}@enderror">
                    @if(!$item->id)
                        <option value="-1">---</option>
                    @endif
                    @if(isset($towns) && $towns->count())
                        @foreach($towns as $row)
                            <option value="{{ $row->id }}" @if(old('town', ($item->id ? $item->town : 0)) == $row->id) selected @endif>{{ $row->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('town')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    @include('admin.partial.edit_field_translate', ['field' => 'address', 'required' => true])

    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="zip_code">{{ __('validation.attributes.zip_code') }}</label>
            <div class="col-12">
                <input type="number" id="zip_code" step="1" name="zip_code" class="form-control form-control-sm @error('zip_code'){{ 'is-invalid' }}@enderror" value="{{ old('zip_code', ($item->id ? $item->zip_code : '')) }}">
                @error('zip_code')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="phone">{{ __('validation.attributes.phone') }}</label>
            <div class="col-12">
                <input type="text" id="phone" name="phone" class="form-control form-control-sm @error('phone'){{ 'is-invalid' }}@enderror" value="{{ old('phone', ($item->id ? $item->phone : '')) }}">
                @error('phone')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="fax">{{ __('validation.attributes.fax') }}</label>
            <div class="col-12">
                <input type="text" id="fax" name="fax" class="form-control form-control-sm @error('fax'){{ 'is-invalid' }}@enderror" value="{{ old('fax', ($item->id ? $item->fax : '')) }}">
                @error('fax')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="col-sm-12 control-label" for="email">{{ __('validation.attributes.email') }}</label>
            <div class="col-12">
                <input type="text" id="email" name="email" class="form-control form-control-sm @error('email'){{ 'is-invalid' }}@enderror" value="{{ old('email', ($item->id ? $item->email : '')) }}">
                @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

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
    @include('admin.partial.edit_field_translate', ['field' => 'add_info'])
</div>
