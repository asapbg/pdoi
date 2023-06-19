<?php

namespace App\Http\Requests;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
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
            'adm_level' => ['required', 'numeric', 'exists:rzs_section,adm_level'],
//            'date_from' => ['required', 'date'],
//            'date_to' => ['nullable', 'date'],
            'region' => ['required', 'numeric'],
            'municipality' => ['required', 'numeric'],
            'town' => ['required', 'numeric'],

            'phone' => ['nullable', 'string', 'max:1000'],
            'fax' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],

            'redirect_only' => ['nullable', 'numeric'],
            'rzs_delivery_method' => ['required', 'numeric', Rule::in(PdoiSubjectDeliveryMethodsEnum::values())],
            'court' => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],
            'active' => ['required', 'numeric', 'gt:0'],
        ];

        if( request()->input('rzs_delivery_method') ) {
            if( (int)request()->input('rzs_delivery_method') == PdoiSubjectDeliveryMethodsEnum::EMAIL->value ) {
                $rules['email'] = ['required', 'email', 'max:255'];
            }
        }

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:pdoi_response_subject'];
//            $rules['eik'][] = Rule::unique('pdoi_response_subject')->ignore($this->input('id'));
        } else {
//            $rules['eik'][] = 'unique:pdoi_response_subject';
        }

        foreach (config('available_languages') as $lang) {
            foreach (PdoiResponseSubject::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }


        return $rules;
    }
}
