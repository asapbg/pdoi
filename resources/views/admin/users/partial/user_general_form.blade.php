<h5 class="bg-primary py-1 px-2 mb-4">{{ __('custom.general_info') }}</h5>
<div class="form-group">
    <label class="col-sm-12 control-label" for="user_type">
        {{ __('validation.attributes.user_type') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        @php($usersTypes = isset($item) ? optionsUserTypes() : optionsUserTypes(true))
        <select id="user_type" name="user_type"  class="form-control form-control-sm">
            @foreach($usersTypes as $val => $name)
                <option value="{{ $val }}" @if(old('user_type', (isset($item) ? $item->user_type : '')) == $val) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        @error('user_type')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="col-sm-12 control-label" for="status">
        {{ __('validation.attributes.status') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        <select id="status" name="status"  class="form-control form-control-sm">
            @php($usersStatuses = isset($item) ? optionsUserStatuses() : optionsUserStatuses(true))
            @foreach($usersStatuses as $val => $name)
                <option value="{{ $val }}" @if(old('status', (isset($item) ? $item->status : '')) == $val) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        @error('status')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="col-sm-12 control-label" for="username">
        {{ __('validation.attributes.username') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        <input type="text" id="username" name="username" class="form-control form-control-sm" value="{{ old('username', (isset($item) ? $item->username : '')) }}">
        @error('username')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="col-sm-12 control-label" for="names">
        {{ __('validation.attributes.names') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        <input type="text" id="names" name="names" class="form-control form-control-sm"
               value="{{ old('names', (isset($item) ? $item->names : '')) }}">
        @error('names')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="col-sm-12 control-label" for="email">
        {{ __('validation.attributes.email') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        <input type="email" id="email" name="email" class="form-control form-control-sm"
               value="{{ old('email', (isset($item) ? $item->email : '')) }}">
        @error('email')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="col-sm-12 control-label" for="phone">
        {{ __('validation.attributes.phone') }}
    </label>
    <div class="col-12">
        <input type="text" id="phone" name="phone" class="form-control form-control-sm"
               value="{{ old('phone', (isset($item) ? $item->phone : '')) }}">
        @error('phone')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

{{--@if(!isset($item) || $item->user_type == \App\Models\User::USER_TYPE_INTERNAL)--}}
    <div class="form-group @if(old('user_type', (isset($item) ? $item->user_type : '')) != \App\Models\User::USER_TYPE_INTERNAL) d-none @endif">
        <label class="col-sm-12 control-label" for="email">
            {{ __('validation.attributes.administrative_unit') }}
        </label>
        <div class="col-12">
            <select id="administrative_unit" name="administrative_unit"  class="form-control form-control-sm select2">
                <option value="" @if(old('administrative_unit', isset($item) ? $item->administrative_unit : '') == '') selected @endif>---</option>
                @if(isset($rzsSubjectOptions) && $rzsSubjectOptions->count())
                    @foreach($rzsSubjectOptions as $rzs)
                        <option value="{{ $rzs->id }}" @if(old('administrative_unit', (isset($item) ? $item->administrative_unit : '')) == $rzs->id) selected @endif>{{ $rzs->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('administrative_unit')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
{{--@endif--}}

<div class="form-group">
    <label class="col-sm-12 control-label" for="lang">
        {{ __('custom.users.work_language') }}<span class="required">*</span>
    </label>
    <div class="col-12">
        <select id="lang" name="lang"  class="form-control form-control-sm">
            @foreach(optionsLanguages() as $val => $name)
                <option value="{{ $val }}" @if(old('lang', config('app.locale')) == $val) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        @error('lang')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

@if(!isset($item))
    <div class="form-group d-none">
        <span class="col-sm-12 control-label">&nbsp;</span>
        <div class="icheck-primary col-12">
            <input class="form-check-input" type="checkbox" name="must_change_password"
                   id="must_change_password" {{ old('must_change_password') ? 'checked' : '' }}>
            <label class="form-check-label" for="must_change_password">
                {{ __('validation.attributes.must_change_password') }}
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-12 control-label" for="password">
            {{ __('validation.attributes.password') }}<span class="required">*</span>
        </label>
        <div class="col-12">
            <input type="password" name="password" class="form-control form-control-sm passwords"
                   autocomplete="new-password">
            <i>{{ __('auth.password_format') }}</i>
            @error('password')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-12 control-label" for="password_confirmation">
            {{ __('validation.attributes.password_confirm') }}
        </label>
        <div class="col-12">
            <input type="password" name="password_confirmation" class="form-control form-control-sm passwords"
                   autocomplete="new-password">
            @error('password_confirmation')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif

{{--@if(!isset($item) || $item->user_type == \App\Models\User::USER_TYPE_INTERNAL)--}}
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function (){
                let internalUserType = parseInt(<?php echo \App\Models\User::USER_TYPE_INTERNAL; ?>);
                $('#user_type').on('change', function (){
                    if( parseInt($('#user_type').val()) === internalUserType ) {
                        $('#administrative_unit').closest('.form-group').removeClass('d-none');
                    } else {
                        $('#administrative_unit').closest('.form-group').addClass('d-none');
                    }
                });
            });
        </script>
    @endpush
{{--@endif--}}
