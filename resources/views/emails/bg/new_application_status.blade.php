@component('mail::message')

Здравейте {{ $application->applicant->names }},

Информация за статуса на подадено от вас заявление в {{ __('custom.full_app_name') }}.

@component('mail::panel')
    Рег. №: {{ $application->application_uri }}<br>
    Задължен субект: {{ $application->responseSubject->subject_name }}<br>
    Статус: {{ __('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($application->status)) }}<br>
    Дата на промяна: {{ displayDate($application->status_date) }}<br>
    Срок за отговор: {{ displayDate($application->response_end_time) }}
@endcomponent

@component('mail::subcopy')
    *Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.
@endcomponent

@endcomponent
