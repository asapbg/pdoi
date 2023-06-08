<?php

namespace App\Http\Requests;

use App\Enums\DeliveryMethodsEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileStoreRequest extends FormRequest
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
            'names' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')
                ->ignore((int)auth()->user()->id)],
            'email' => ['required', 'string', 'max:255', Rule::unique('users', 'email')
                ->ignore((int)auth()->user()->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'person_identity' => ['nullable', 'required_without:company_identity', 'string', 'max:20'],
            'company_identity' => ['nullable', 'required_without:person_identity', 'string', 'max:20'],
            'country' => ['required', 'numeric', 'exists:country,id'],
            'area' => ['required', 'numeric', 'exists:ekatte_area,id'],
            'municipality' => ['required', 'numeric', 'exists:ekatte_municipality,id'],
            'settlement' => ['required', 'numeric', 'exists:ekatte_settlement,id'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'address_second' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'numeric', Rule::in(DeliveryMethodsEnum::values())],
        ];

        if( request()->input('legal_form') ) {
            $personIdentity = request()->input('legal_form') == User::USER_TYPE_PERSON ? 'person_identity' : 'company_identity';
            $rules[$personIdentity] = ['required', 'string', 'max:20'];
        }

        return $rules;

    }
}
