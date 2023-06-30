<?php

namespace App\Http\Requests;

use App\Enums\CourtDecisionsEnum;
use App\Models\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRenewRequest extends FormRequest
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
            'reopen' => ['required', 'numeric', 'in:0,1'],
            'application' => ['required', 'numeric', 'exists:pdoi_application,id'],
            'decision' => ['required', 'numeric', Rule::in(CourtDecisionsEnum::values())],
            'file_decision' => ['required', 'file',  'max:'.config('filesystems.max_upload_file_size'), 'mimes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS)],
            'add_text' => ['nullable', 'string'],
        ];
    }
}
