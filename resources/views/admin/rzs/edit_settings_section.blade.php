<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.rzs.settings_section') }}</h5>
    <div class="col-12">
        <div class="form-group">
            <div class="col-12">
                <div class="form-group form-group-sm col-12 mb-3">
                    <label class="form-label me-3 fw-semibold">{{ __('validation.attributes.rzs_delivery_method') }}: <span class="required">*</span></label> <br>
                    @foreach(\App\Enums\PdoiSubjectDeliveryMethodsEnum::options() as $name => $val)
                        <label class="form-label fw-normal col-12" role="button">
                            <input type="radio" name="rzs_delivery_method" value="{{ $val }}" @if(old('rzs_delivery_method', $item->id ? $item->delivery_method : 0) == $val) checked @endif required> {{ __('custom.rzs.delivery_by.'.$name) }}
                        </label>
                    @endforeach
                    @error('rzs_delivery_method')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="col-12">
        <div class="form-group form-group-sm col-12 mb-3">
            <label class="form-label me-3 fw-semibold">{!! __('custom.rzs.court.label') !!}: <span class="required">*</span>
                <button type="button" class="btn btn-sm btn-primary ms-1 pick-subject"
                        data-title="{{ trans_choice('custom.pdoi_response_subjects',2) }}"
                        data-url="{{ route('modal.pdoi_subjects').'?admin=1&select=1' }}">
                    <i class="fas fa-list"></i>
                </button>
            </label>
            <select name="court" id="court" style="width:100%;" class="select2 @error('court') is-invalid @enderror">
                @if(isset($courtSubjects) && $courtSubjects->count())
                    <option value="" @if(old('court', $item->id ? $item->court_id : 0) == 0) selected @endif>Списък РЗС</option>
                    @foreach($courtSubjects as $row)
                        <option value="{{ $row->id }}" @if(old('court', $item->id ? $item->court_id : 0) == $row->id) selected @endif>{{ $row->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('court')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    @include('admin.partial.edit_field_translate', ['field' => 'court_text', 'required' => false])
</div>
