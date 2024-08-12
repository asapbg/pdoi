<?php

namespace App\Http\Requests;

use App\Models\ReasonRefusal;
use Illuminate\Foundation\Http\FormRequest;

class NoConsiderationReasonStoreRequest extends FormRequest
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
        ];

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:no_consideration_reason'];
        }

        foreach (config('available_languages') as $lang) {
            foreach (ReasonRefusal::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
