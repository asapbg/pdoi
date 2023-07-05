<?php

namespace App\Http\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageStoreRequest extends FormRequest
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
            'active' => ['required', 'numeric', 'gt:0'],
            'section' => ['nullable', 'numeric', 'exists:menu_section,id'],
            'order_idx' => ['required', 'numeric', 'gte:0'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('page', 'slug')->ignore((int)request()->input('id'))],
        ];

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:page'];
        }

        foreach (config('available_languages') as $lang) {
            foreach (Page::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
