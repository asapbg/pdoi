<?php

namespace App\Http\Requests;

use App\Models\EkatteMunicipality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EkatteMunicipalityStoreRequest extends FormRequest
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
            'obstina' => ['required', 'string', 'min:1', 'max:50'],
            'ekatte' => ['required', 'numeric'],
            'category' => ['nullable', 'string', 'max:50'],
            'document' => ['nullable', 'string', 'max:50'],
            'abc' => ['nullable', 'string', 'max:50'],
            'active' => ['required', 'numeric', 'gt:0'],
        ];

        if( $this->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:ekatte_municipality'];
            $rules['obstina'][] = Rule::unique('ekatte_municipality')->ignore($this->input('id'));
        } else {
            $rules['obstina'][] = 'unique:ekatte_municipality';
        }

        foreach (config('available_languages') as $lang) {
            foreach (EkatteMunicipality::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
