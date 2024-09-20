<?php

namespace App\Http\Requests;

use App\Models\CustomStatistic;
use App\Rules\CustomStatisticCsvStructure;
use App\Rules\FileClientMimeType;
use Illuminate\Foundation\Http\FormRequest;

class CustomStatisticStoreRequest extends FormRequest
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
        $fileIsRequired = request()->input('id') ? 'nullable' : 'required';
        $rules = [
            'type' => ['required', 'numeric'],
            'publish_from' => ['required', 'date'],
            'publish_to' => ['nullable', 'date', 'after_or_equal::publish_to'],
            'file' => [$fileIsRequired, 'file','max:'.config('filesystems.max_upload_file_size'),  'mimes:'.implode(',', CustomStatistic::ALLOWED_FILE_EXTENSIONS)],
        ];

        if(request()->input('publish_to')){
            $rules['publish_from'][] = 'before_or_equal:publish_to';
        }

        foreach (config('available_languages') as $lang) {
            foreach (CustomStatistic::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
