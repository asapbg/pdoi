@component('mail::message')
Здравейте, {{ $user->fullName() }}!<br>

@if(!empty($app_request->reason_refuse))
    {!! stripHtmlTags($app_request->reason_refuse, ['br']) !!}
@endif

@component('mail::button', ['url' => $url])
    Към заявлението
@endcomponent
* Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.
@endcomponent
