<?php

namespace App\Http\Requests;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\PdoiResponseSubject;
use App\Rules\SubjectValidDeliveryMethod;
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
        $rules = [];
        if( request()->input('full_edit') ) {
            $rules = [
                'eik' => ['required', 'digits_between:1,13'],
                'adm_level' => ['required', 'numeric', 'exists:rzs_section,adm_level'],
                'region' => ['required', 'numeric'],
                'municipality' => ['required', 'numeric'],
                'town' => ['required', 'numeric'],
                'parent_id' => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],

                'phone' => ['nullable', 'string', 'max:1000'],
                'fax' => ['nullable', 'string', 'max:1000'],
                'email' => ['nullable', 'email', 'max:255'],
                'active' => ['required', 'numeric', 'gt:0'],
            ];
        }

        $rules['full_edit'] = ['required', 'numeric'];
        $rules['redirect_only'] = ['nullable', 'numeric'];
        $rules['rzs_delivery_method'] = ['required', 'numeric', Rule::in(PdoiSubjectDeliveryMethodsEnum::values())];
        $rules['court'] = ['nullable', 'numeric', 'exists:pdoi_response_subject,id'];


        if( request()->input('rzs_delivery_method') ) {
            if( request()->input('full_edit') ) {
                if( (int)request()->input('rzs_delivery_method') == PdoiSubjectDeliveryMethodsEnum::EMAIL->value ) {
                    $rules['email'] = ['required', 'email', 'max:255'];
                }
                if( (int)request()->input('rzs_delivery_method') == PdoiSubjectDeliveryMethodsEnum::SEOS->value ) {
                    $rules['eik'] = [Rule::exists('egov_organisation', 'eik')->where('status', '=', 1)];
                }
            }
            $rules['rzs_delivery_method'] = new SubjectValidDeliveryMethod(request()->input('id'), request()->input('full_edit'), request()->input('eik'), request()->input('email'));
        }

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:pdoi_response_subject'];
            if( request()->input('full_edit') ) {
                $rules['eik'][] = Rule::unique('pdoi_response_subject')->ignore($this->input('id'));
            }
        } else {
            $rules['eik'][] = 'unique:pdoi_response_subject';
        }

        foreach (config('available_languages') as $lang) {
            foreach (PdoiResponseSubject::translationFieldsProperties() as $field => $properties) {
                if( request()->input('full_edit') || $field == 'court_text' ) {
                    $rules[$field.'_'.$lang['code']] = $properties['rules'];
                }
            }
        }


        return $rules;
    }

    public function messages()
    {
        return [
            'eik.exists' => trans('custom.rzs.egov_organisation_is_missing')
        ];
    }
}
