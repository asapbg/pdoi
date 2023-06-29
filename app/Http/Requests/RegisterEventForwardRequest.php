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
        return [
            'event' => ['nullable', 'numeric'],
            'application' => ['nullable', 'numeric', 'exists:pdoi_application,id'],
            'new_resp_subject_id' => ['required', 'array', 'min:1'],
            'new_resp_subject_id.*' => ['numeric', 'exists:pdoi_response_subject,id'],
            'subject-notes' => ['required', 'array', 'min:1'],
            'subject-notes.*' => ['required', 'string', 'min:3'],
            'add_text' => ['nullable', 'string'],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'files' => ['array'],
            'files.*' => ['file', 'max:'.config('filesystems.max_upload_file_size'), 'mimes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS)],
        ];
    }
}
