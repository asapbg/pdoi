<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Foundation\Http\FormRequest;

class PageFileUploadRequest extends FormRequest
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
            'description' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:'.config('filesystems.max_upload_file_size'), 'mimes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS)]
        ];
    }
}
