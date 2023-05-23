<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $storeRules = new StoreUsersRequest();
        $this->offsetSet('must_change_password', false);

        return array_merge($storeRules->rules($this), [
            'username'              => ['required', 'string', 'max:255'],
            'password'              => ['nullable', 'confirmed', Password::min(6)->numbers()],
            'password_confirmation' => ['nullable','same:password']
        ]);
    }
}
