<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
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
        $storeRules = [
            'names'                 => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:50'],
            'password'              => ['nullable', 'confirmed', Password::min(6)->numbers()],
            'password_confirmation' => ['nullable','same:password'],
            'email'                 => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore((int)request()->input('id'))
            ],
        ];

        return $storeRules;
    }
}
