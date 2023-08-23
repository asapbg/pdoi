@component('mail::message')

Здравейте, {{ "$user->names" }}

Беше създаден профил в системата на {{ env('APP_NAME') }}, трябва да изберете парола, моля използвайте бутона по-долу

@component('mail::button', ['url' => $url])
Към Tiger app
@endcomponent

@endcomponent
