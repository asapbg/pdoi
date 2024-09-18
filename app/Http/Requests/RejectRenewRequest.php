<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRenewRequest extends FormRequest
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
            'id' => ['required', 'numeric', 'exists:pdoi_application_restore_request'],
            'answer' => ['required', 'string', 'min:3']
        ];
    }
}
