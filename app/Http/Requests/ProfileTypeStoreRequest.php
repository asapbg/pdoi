<?php

namespace App\Http\Requests;

use App\Models\ProfileType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileTypeStoreRequest extends FormRequest
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
        $rules = [
            'active' => ['required', 'numeric', 'gt:0'],
            'user_legal_form' => ['required', 'numeric', Rule::in(array_keys(User::getUserLegalForms()))],
        ];

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:country'];
        }

        foreach (config('available_languages') as $lang) {
            foreach (ProfileType::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
