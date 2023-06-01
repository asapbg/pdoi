<?php

namespace App\Http\Requests;

use App\Models\PdoiResponseSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PdoiResponseSubjectStoreRequest extends FormRequest
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
            'eik' => ['required', 'string', 'max:13'],
            'adm_level' => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],
            'date_from' => ['required', 'date'],
            'date_to' => ['nullable', 'date'],
            'region' => ['required', 'numeric'],
            'municipality' => ['required', 'numeric'],
            'town' => ['required', 'numeric'],

            'phone' => ['nullable', 'string', 'max:1000'],
            'fax' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'string', 'max:255'],


            'redirect_only' => ['nullable', 'numeric'],
            'active' => ['required', 'numeric', 'gt:0'],
            'manual' => ['required', 'numeric', 'gt:0'],
        ];

        if( $this->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:pdoi_response_subject'];
            $rules['eik'][] = 'unique:pdoi_response_subject';
        } else {
            $rules['eik'][] = Rule::unique('pdoi_response_subject')->ignore($this->input('eik'));
        }

        foreach (config('available_languages') as $lang) {
            foreach (PdoiResponseSubject::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }


        return $rules;
    }
}
