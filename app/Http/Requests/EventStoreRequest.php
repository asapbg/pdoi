<?php

namespace App\Http\Requests;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\CustomRole;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventStoreRequest extends FormRequest
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
        $rules = [];

        if( auth()->user()->hasRole(CustomRole::SUPER_USER_ROLE) ) {
            $rules['app_event'] = ['required', 'numeric', 'gt:0'];
            $rules['app_status'] = ['required', 'numeric', Rule::in(PdoiApplicationStatusesEnum::values())];
            $rules['extend_terms_reason_id'] = ['nullable', 'numeric', 'exists:extend_terms_reason,id'];
            $rules['days'] = ['nullable', 'numeric'];
            $rules['old_resp_subject'] = ['required', 'numeric', 'in:0,1'];
            $rules['new_resp_subject'] = ['required', 'numeric', 'in:0,1'];
            $rules['event_status'] = ['required', 'numeric', 'in:1,2'];
            $rules['add_text'] = ['required', 'numeric', 'in:0,1'];
            $rules['files'] = ['required', 'numeric', 'in:0,1'];
            $rules['event_delete'] = ['required', 'numeric', 'in:0,1'];
            $rules['mail_to_admin'] = ['required', 'numeric', 'in:0,1'];
            $rules['mail_to_app'] = ['required', 'numeric', 'in:0,1'];
            $rules['mail_to_new_admin'] = ['required', 'numeric', 'in:0,1'];
        }

        if( request()->isMethod('put') ) {
            $rules['id'] = ['required', 'numeric', 'exists:event'];
        }

        foreach (config('available_languages') as $lang) {
            foreach (Event::translationFieldsProperties() as $field => $properties) {
                $rules[$field.'_'.$lang['code']] = $properties['rules'];
            }
        }

        return $rules;
    }
}
