<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUsersRequest extends FormRequest
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
        $must_change_password = $this->offsetGet('must_change_password');

        $rules = [
            'username'              => ['required', 'unique:users', 'string', 'max:255'],
            'first_name'            => ['required', 'string', 'max:255'],
            'last_name'             => ['required', 'string', 'max:255'],
            'email'                 => ['nullable', 'string', 'email', 'max:255'],
            'roles'                 => ['required' ,'array', 'min:1'],
        ];
        if(!$must_change_password) {
            $rules = array_merge($rules, self::passwordRequestValidationRules());
        }

        return $rules;
    }

    /**
     * User password validation rules
     *
     * @return array
     */
    public static function passwordRequestValidationRules()
    {
        return [
            'password'              => ['required', 'confirmed',
                Password::min(6)->numbers()->letters()->symbols()],
            'password_confirmation' => ['required','same:password']
        ];
    }
}
