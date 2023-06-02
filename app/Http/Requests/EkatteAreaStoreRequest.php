<?php

namespace App\Http\Requests;

use App\Models\EkatteArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EkatteAreaStoreRequest extends FormRequest
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
            'oblast' => ['required', 'string', 'min:1', 'max:50'],
            'region' => ['required', 'string', 'min:1', 'max:50'],
            'document' => ['nullable', 'string', 'max:50'],
            'abc' => ['nullable', 'string', 'max:50'],
            'ekatte' => ['required', 'numeric'],
            'active' => ['required', 'numeric', 'gt:0'],
        ];

        if( $this->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:ekatte_area'];
            $rules['oblast'][] = Rule::unique('ekatte_area')->ignore($this->input('id'));
        } else {
            $rules['oblast'][] = 'unique:ekatte_area';
        }

        foreach (config('available_languages') as $lang) {
            foreach (EkatteArea::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
