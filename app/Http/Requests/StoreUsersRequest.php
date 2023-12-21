<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'username'              => ['required', 'unique:users', 'string', 'max:50'],
            'names'                 => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255'],
            'roles'                 => ['required' ,'array', 'min:1'],
            'permissions'           => ['nullable' ,'array'],
            'status'                => ['required' ,'numeric', 'gt:0'],
            'phone'                 => ['nullable' ,'string', 'max:50'],
            'administrative_unit'   => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],
            'lang'                  => ['required' ,'string'],
        ];

//        if( request()->isMethod('post') ) {
            $rules['user_type'] = ['required' ,'numeric', 'gt:0'];
//        }

        if( request()->isMethod('post') ) {
            $rules['email'][] = 'unique:users,email';
        } else{
            $rules['email'][] = Rule::unique('users', 'email')->ignore((int)request()->input('id'));
        }

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
