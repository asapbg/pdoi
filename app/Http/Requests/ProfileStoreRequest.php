<?php

namespace App\Http\Requests;

use App\Enums\DeliveryMethodsEnum;
use App\Models\Country;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\EgnRule;
use App\Rules\SsevProfileRule;
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
        $identityType = request()->input('legal_form');
        $identityNumber = request()->input('legal_form') == User::USER_TYPE_PERSON ? !empty(request()->input('person_identity')) ?? request()->input('person_identity') : (!empty(request()->input('company_identity')) ? request()->input('company_identity') : '') ;

        $rules = [
            'names' => ['required', 'string', 'max:255', new AlphaSpace()],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')
                ->ignore((int)auth()->user()->id)],
            'email' => ['required', 'string', 'max:255', Rule::unique('users', 'email')
                ->ignore((int)auth()->user()->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'legal_form' => ['nullable', 'numeric', Rule::in(array_keys(User::getUserLegalForms()))],
            'person_identity' => ['nullable', 'string', new EgnRule()],
            'company_identity' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'numeric', 'exists:country,id'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'address_second' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'numeric', Rule::in(DeliveryMethodsEnum::values()), new SsevProfileRule(auth()->user(), $identityType, $identityNumber)],
            'profile_type' => ['numeric'],
        ];

        if( request()->input('profile_type') && (int)request()->input('profile_type') == 0 ) {
            $rules['profile_type'][] = 'exists:profile_type,id';
        }

        if( request()->input('person_identity') && !empty(request()->input('person_identity')) ) {
            $rules['person_identity'][] = Rule::unique('users', 'person_identity')->ignore(auth()->user()->id);
        }
        if( request()->input('company_identity') && !empty(request()->input('company_identity')) ) {
            $rules['company_identity'][] = Rule::unique('users', 'company_identity')->ignore(auth()->user()->id);
        }

        if( request()->input('delivery_method') && (int)request()->input('delivery_method') === DeliveryMethodsEnum::SDES->value ) {
            if ( request()->input('legal_form') ) {
                $personIdentity = request()->input('legal_form') == User::USER_TYPE_PERSON ? 'person_identity' : 'company_identity';
                $rules[$personIdentity] = ['required', 'string', 'max:20', Rule::unique('users', $personIdentity)->ignore(auth()->user()->id), new EgnRule()];
            }
        }

        if(request()->input('country')) {
            $defaultCountry = Country::isDefault()->first();
            if(request()->input('country') == $defaultCountry->id){
                $rules['area'] = ['required', 'numeric', 'exists:ekatte_area,id'];
                $rules['municipality'] = ['required', 'numeric', 'exists:ekatte_municipality,id'];
                $rules['settlement'] = ['required', 'numeric', 'exists:ekatte_settlement,id'];
            } else{
                $rules['area'] = ['nullable'];
                $rules['municipality'] = ['nullable'];
                $rules['settlement'] = ['nullable'];
            }
        }

        return $rules;

    }
}
