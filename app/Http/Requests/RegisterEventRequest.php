<?php

namespace App\Http\Requests;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\Event;
use App\Models\File;
use App\Models\PdoiApplication;
use App\Rules\FileClientMimeType;
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
        $app = PdoiApplication::find(request()->input('application'));
        $event = request()->input('event') ? Event::find((int)request()->input('event')) : null;
        $edit_final_decision_reason_is_required = request()->user()->can('canEditFinalDecision', $app) && (int)request()->input('change_decision_reasons_select') == 0 ? 'required' : 'nullable';
        $final_status_is_required = $event && $event->app_event == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value ? 'required' : 'nullable';

        //        $final_status_is_required = ;
        $rules = [
            'event' => ['nullable', 'numeric'],
            'final_status' => [$final_status_is_required, 'numeric'],
            'application' => ['nullable', 'numeric', 'exists:pdoi_application,id'],
            'new_resp_subject_id' => ['nullable', 'numeric', 'exists:pdoi_response_subject,id'],
            'refuse_reason' => ['nullable', 'required_if:final_status,'.PdoiApplicationStatusesEnum::NOT_APPROVED->value, 'numeric', 'exists:reason_refusal,id'],
            'no_consider_reason' => ['nullable', 'required_if:final_status,'.PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value, 'numeric', 'gte:0'],
            'add_text' => ['nullable', 'required_if:no_consider_reason,0', 'string'],
            'change_decision_reasons_select' => ['nullable', 'numeric'],
            'edit_final_decision_reason' => [$edit_final_decision_reason_is_required, 'string'],
            'file_description' => ['array'],
            'file_description.*' => ['nullable', 'string', 'max:255'],
            'files' => ['array', 'max:'.config('filesystems.max_file_uploads')],
            'files.*' => ['file', 'max:'.config('filesystems.max_upload_file_size'), new FileClientMimeType(File::ALLOWED_FILE_EXTENSIONS_MIMES_TYPE)], //'mimetypes:'.implode(',', File::ALLOWED_FILE_EXTENSIONS_MIMES_TYPE)
            'file_visible' => ['array'],
            'file_visible.*' => ['nullable', 'numeric'],
        ];

        if(
            $event
            && $event->app_event == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value
            && request()->input('final_status') != PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value){
            $rules['add_text'] = ['nullable', 'required_without:files', 'string'];
            $rules['files'] = ['array', 'required_without:add_text', 'max:'.config('filesystems.max_file_uploads')];
        }

        return $rules;
    }
}
