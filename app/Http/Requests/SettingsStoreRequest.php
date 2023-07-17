<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsStoreRequest extends FormRequest
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
            'general' => array(
                'section' => ['required', 'string'],
                'session_time_limit' => ['required', 'numeric', 'min:5']
            )
        ];
        $section = request()->input('section');
        return $section && isset($rules[$section]) ? $rules[$section] : [];
    }
}
