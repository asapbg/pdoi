<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Foundation\Http\FormRequest;

class RegisterEventForwardRequest extends FormRequest
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
        $rules = [
            'event' => ['nullable', 'numeric'],
            'in_platform' => ['required', 'numeric'],
            'application' => ['nullable', 'numeric', 'exists:pdoi_application,id'],
            'old_subject' => ['required', 'numeric', 'exists:pdoi_response_subject,id'],
            'subject_user_request' => ['required', 'string', 'min:3'],
            'add_text' => ['nullable', 'string', 'min:3'],
            'to_name' => ['nullable', 'string', 'min:2'],
            'current_subject_user_request' => ['nullable', 'string', 'min:3'],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'files' => ['array'],
            'files.*' => ['file', 'max:'.config('filesystems.max_upload_file_size'), 'mimes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS)],
        ];

        $inPlatform = (int)request()->input('in_platform');
        if( $inPlatform ) {
            $rules['new_resp_subject_id'] = ['required', 'numeric', 'exists:pdoi_response_subject,id'];
        } else {
            $rules['new_resp_subject_eik'] = ['required', 'string', 'max:13'];
            $rules['new_resp_subject_name'] = ['required', 'string', 'max:255'];
            $rules['subject_is_child'] = ['nullable', 'numeric'];
        }

        return $rules;
    }
}
