<?php

namespace App\Http\Requests;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\File;
use App\Models\PdoiApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterEventRequest extends FormRequest
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
            'final_status' => ['nullable', 'numeric'],
            'application' => ['nullable', 'numeric', 'exists:pdoi_application,id'],
            'new_resp_subject_id' => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],
            'add_text' => ['nullable', 'string'],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'files' => ['array'],
            'files.*' => ['file', 'max:'.File::MAX_FILE_SIZE, 'mimes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS)],
        ];
    }
}
