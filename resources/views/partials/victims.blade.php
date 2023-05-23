@php
    $row_key = 0;
    $count = 0;
    if (is_array(old('new_victims'))) {
        $new_victims = old('new_victims');
    }
    $existing_victims_cnt = (isset($case)) ? $case->victims->count() : 0;
@endphp
<div class="form-group">
    <label class="col-xs-12 control-label" for="victim">
        {{ __('custom.victim_perpetrator_data') }}
    </label>
    @if($existing_victims_cnt > 0 || isset($new_victims))
        @if($existing_victims_cnt > 0)
            <div class="col-xs-12">
                @foreach($case->victims as $victim)
                    <div id="victim_{{ $row_key }}" class="row mb-2">
                        <div class="col-md-6 col-sm-5">
                            <input type="hidden" name="victims[{{ $row_key }}][id]" value="{{ $victim->id }}">
                            <input type="hidden" name="victims[{{ $row_key }}][delete]" class="delete" value="no">
                            <input type="text" name="victims[{{ $row_key }}][name]" class="form-control" placeholder="{{ __('custom.first_name') }}"
                                   value="{{ $victim->victim_name }}">
                            @error('victim_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <input type="text" name="victims[{{ $row_key }}][egn]" class="form-control" placeholder="{{ __('custom.egn') }}" maxlength="10"
                                   value="{{ $victim->victim_egn }}">
                            @error('victim_egn')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 col-sm-3 pl-0">
                            <span class="btn btn-danger" onclick="RemoveVictimRow({{ $row_key }})">
                                <i class="fa fa-trash-o"></i> Изтрий
                            </span>
                        </div>
                    </div>
                    @php
                        $row_key++;
                        $count++;
                    @endphp
                @endforeach
            </div>
        @endif
        @if(isset($new_victims))
            <div class="col-xs-12">
            @foreach($new_victims as $victim)
                @if(!empty($victim['name']) && !empty($victim['egn']))
                    <div id="victim_{{ $row_key }}" class="row mb-2">
                        <div class="col-md-6 col-sm-5">
                            <input type="text" name="new_victims[{{ $row_key }}][name]" class="form-control" placeholder="{{ __('custom.first_name') }}"
                                   value="{{ $victim['name'] }}">
                            @error('victim_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <input type="text" name="new_victims[{{ $row_key }}][egn]" class="form-control" placeholder="{{ __('custom.egn') }}" maxlength="10"
                                   value="{{ $victim['egn'] }}">
                            @error('victim_egn')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                            <div class="col-md-2 col-sm-3 pl-0">
                                <span class="btn btn-danger" onclick="RemoveVictimRow({{ $row_key }})">
                                    <i class="fa fa-trash-o"></i> Изтрий
                                </span>
                            </div>
                    </div>
                    @php
                        $row_key++;
                        $count++;
                    @endphp
                @endif
            @endforeach
            </div>
        @endif
    @endif
    @if($count == 0)
        @php
            $count++;
        @endphp
        <div class="col-xs-12">
            <div class="row">
                <div class="col-md-6 col-sm-5">
                    <input type="text" name="new_victims[0][name]" class="form-control" placeholder="{{ __('custom.first_name') }}">
                    @error('victim_name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 col-sm-4">
                    <input type="text" name="new_victims[0][egn]" class="form-control" placeholder="{{ __('custom.egn') }}" maxlength="10">
                    @error('victim_egn')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</div>
<div id="more_victims" class="col-xs-12">
    <input type="hidden" id="victims_count" value="{{ $count }}">
</div>
<div class="form-group">
    <div class="col-xs-12">
        <span class="btn btn-success" onclick="AddVictimRow()">
            <i class="fa fa-plus-circle"></i> Добави {{ trans_choice('custom.victims', 1) }}
        </span>
    </div>
</div>
<div id="new_victim" class="d-none">
    <div class="victims row mb-2">
        <div class="col-md-6 col-sm-5">
            <input type="text" name="" class="form-control name" placeholder="{{ __('custom.first_name') }}">
            @error('victim_name')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4 col-sm-4">
            <input type="text" name="" class="form-control egn" placeholder="{{ __('custom.egn') }}" maxlength="10">
            @error('victim_egn')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-2 col-xs-3 pl-0 remove">

        </div>
    </div>
</div>
