@component('mail::message')
Здравейте, {{ $user->fullName() }}!<br>
Изтичащ срок за обработка на заявление {{ $app->application_uri }}<br>
@if(!empty($app->response_end_time))
    Краен срок: {{ \Carbon\Carbon::parse($app->response_end_time)->format('d.m.Y') }}
@endif

@component('mail::button', ['url' => $url])
    Към заявлението
@endcomponent
* Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.
@endcomponent
