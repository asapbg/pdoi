@component('mail::message')

Здравейте,

Беше регистрирано ново заявление в системата на {{ __('custom.full_app_name') }}.

@component('mail::panel')
    Рег. №: {{ $application->application_uri }}<br>
    Заявител: {{ $application->applicant->names }}<br>
    Дата на регситрация: {{ displayDate($application->registration_date) }}<br>
    Срок: {{ displayDate($application->response_end_time) }}
@endcomponent

@component('mail::subcopy')
    *Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.
@endcomponent

@endcomponent
