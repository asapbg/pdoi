<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\FileClientMimeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminCreateApplicationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'created_at' => ['required', 'date'],
            'applicant_type' => ['required', 'numeric', Rule::in(array_keys(User::getUserLegalForms()))],
            'profile_type' => ['nullable', 'numeric', 'exists:profile_type,id'],
            'full_names' => ['required', 'string', 'max:255', new AlphaSpace()],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country_id' => ['required', 'numeric', 'exists:country,id'],
            'area_id' => ['nullable', 'numeric', 'exists:ekatte_area,id'],
            'municipality_id' => ['nullable', 'numeric', 'exists:ekatte_municipality,id'],
            'settlement_id' => ['nullable', 'numeric', 'exists:ekatte_settlement,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'email_publication' => ['nullable', 'numeric', 'in:1'],
            'names_publication' => ['nullable', 'numeric', 'in:1'],
            'address_publication' => ['nullable', 'numeric', 'in:1'],
            'phone_publication' => ['nullable', 'numeric', 'in:1'],
            'request' => ['required', 'string', 'min:3'],
            'response' => ['required', 'string', 'min:3'],
            'response_subject_id' => ['required', 'numeric', 'exists:pdoi_response_subject,id'],
            'files' => ['array', 'max:'.config('filesystems.max_file_uploads')],
            'files.*' => ['file', 'max:'.config('filesystems.max_upload_file_size'), new FileClientMimeType(File::ALLOWED_FILE_EXTENSIONS_MIMES_TYPE)],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'file_visible' => ['array'],
            'file_visible.*' => ['nullable', 'numeric'],
            'status' => ['required', 'numeric', ],
        ];
    }
}
