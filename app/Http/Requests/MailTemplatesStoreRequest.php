<?php

namespace App\Http\Requests;

use App\Enums\MailTemplateTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MailTemplatesStoreRequest extends FormRequest
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
            'id' => ['required', 'numeric', 'exists:mail_template,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('mail_template')->ignore((int)request()->input('id'))],
            'type' => ['required', 'numeric', 'in:'.implode(',', MailTemplateTypesEnum::values())],
            'content' => ['required', 'string', 'min:3']
        ];
    }
}
