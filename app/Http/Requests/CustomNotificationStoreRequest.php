<?php

namespace App\Http\Requests;

use App\Rules\MinHtmlLengthRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomNotificationStoreRequest extends FormRequest
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
            'mail' => ['required_without:db'],
            'db' => ['required_without:mail'],
            'users' => ['required_without:all', 'array'],
            'users.*' => ['numeric', 'exists:users,id'],
            'msg' => ['required', 'string', new MinHtmlLengthRule()],
            'subject' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'mail.required_without' => 'Изберете поне един \'Начин на изпращане\'',
            'db.required_without' => 'Изберете поне един \'Начин на изпращане\'',
            'users.required_without' => 'Изберете поне един получател',
        ];
    }
}
