<?php

namespace App\Http\Requests;

use App\Enums\DeliveryMethodsEnum;
use App\Models\Country;
use App\Models\File;
use App\Models\PdoiApplication;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\EgnRule;
use App\Rules\FileClientMimeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PdoiApplicationApplyRequest extends FormRequest
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
    {dd(request()->all());
        $rules = [
            'legal_form' => ['required', 'numeric', Rule::in(array_keys(User::getUserLegalForms()))],
            'names' => ['required', 'string', 'max:255', new AlphaSpace()],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')
                ->ignore((int)auth()->user()->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'numeric', 'exists:country,id'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'address_second' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'numeric', Rule::in(DeliveryMethodsEnum::values())],
            'request' => ['required', 'string', 'min:3'],
            'email_publication' => ['nullable', 'numeric', 'in:1'],
            'names_publication' => ['nullable', 'numeric', 'in:1'],
            'address_publication' => ['nullable', 'numeric', 'in:1'],
            'phone_publication' => ['nullable', 'numeric', 'in:1'],
            'subjects' => ['required', 'array', 'max:10'],
            'person_identity' => ['nullable', 'string', 'max:20', new EgnRule()],
            'company_identity' => ['nullable', 'string', 'max:20'],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'files' => ['array', 'max:'.config('filesystems.max_file_uploads')],
            'files.*' => ['file', 'max:'.config('filesystems.max_upload_file_size'), new FileClientMimeType(File::ALLOWED_FILE_EXTENSIONS_MIMES_TYPE)],
        ];

        if( request()->input('delivery_method') && (int)request()->input('delivery_method') === DeliveryMethodsEnum::SDES->value ) {
            if ( request()->input('legal_form') ) {
                $personIdentity = request()->input('legal_form') == User::USER_TYPE_PERSON ? 'person_identity' : 'company_identity';
                $rules[$personIdentity] = ['required', 'string', new EgnRule()];
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
