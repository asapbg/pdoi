Здравейте {{ $application->applicant->names }},{{ PHP_EOL }}
Информация за статуса на подадено от вас заявление в {{ __('custom.full_app_name') }}.{{ PHP_EOL }}
Рег. №: {{ $application->application_uri }}.{{ PHP_EOL }}
Задължен субект: {{ $application->responseSubject->subject_name }}.{{ PHP_EOL }}
Статус: {{ __('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($application->status)) }}.{{ PHP_EOL }}
Дата на промяна: {{ displayDate($application->status_date) }}.{{ PHP_EOL }}
Срок за отговор: {{ displayDate($application->response_end_time) }}.{{ PHP_EOL }}
*Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.

