@component('mail::message')
{{ $message }}

{{ $from_user }}
* Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.
@endcomponent
