<?php

namespace App\Http\Requests;

use App\Models\RzsSection;
use Illuminate\Foundation\Http\FormRequest;

class RzsSectionStoreRequest extends FormRequest
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


        foreach (config('available_languages') as $lang) {
            foreach (RzsSection::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }


        return $rules;
    }
}
