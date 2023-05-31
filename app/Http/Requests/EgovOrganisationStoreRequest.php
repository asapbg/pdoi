<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EgovOrganisationStoreRequest extends FormRequest
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
            'eik' => ['sometimes', 'string', 'max:15'],
            'web_site' => ['sometimes', 'url', 'max:500'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'fax' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'string', 'max:100'],
            'active' => ['required', 'numeric', 'gt:0'],
            'manual' => ['required', 'numeric', 'gt:0'],

            'name' => ['required', 'string', 'max:500'],
            'postal_address' => ['sometimes', 'string', 'max:500'],
            'contact' => ['sometimes', 'string', 'max:500'],

        ];

        if( $this->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:egov_organization'];
            $rules['eik'][] = 'unique:egov_organization';
        } else {
            $rules['eik'][] = Rule::unique('egov_organization')->ignore($this->input('eik'));
        }
    }
}
