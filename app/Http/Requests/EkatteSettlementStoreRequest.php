<?php

namespace App\Http\Requests;

use App\Models\EkatteSettlement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EkatteSettlementStoreRequest extends FormRequest
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
            'ekatte' => ['required', 'numeric'],
            'tvm' => ['nullable', 'string', 'min:1', 'max:50'],
            'oblast' => ['required', 'string', 'min:1', 'max:50'],
            'obstina' => ['required', 'string', 'min:1', 'max:50'],
            'kmetstvo' => ['nullable', 'string', 'max:50'],
            'kind' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'altitude' => ['nullable', 'string', 'max:50'],
            'document' => ['nullable', 'string', 'max:50'],
            'tsb' => ['nullable', 'string', 'max:50'],
            'abc' => ['nullable', 'string', 'max:50'],
            'active' => ['required', 'numeric', 'gt:0'],
        ];

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:ekatte_settlement'];
            $rules['obstina'][] = Rule::unique('ekatte_settlement')->ignore((int)request()->input('id'));
        } else {
            $rules['obstina'][] = 'unique:ekatte_settlement';
        }

        foreach (config('available_languages') as $lang) {
            foreach (EkatteSettlement::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
