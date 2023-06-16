<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum PdoiApplicationStatusesEnum: int
{
    use Names, Options, Values;

    case RECEIVED = 1; //Прието на платформата
    case REGISTRATION_TO_SUBJECT = 2; //Очаква регистрация при задължен субект
    case IN_PROCESS = 3; //Регистрирано/в процес на обработка
    case APPROVED = 4; //Одобрено
    case PART_APPROVED = 5; //Частично одобрено
    case NOT_APPROVED = 6; //Неодобрено
    case INFO_NOT_EXIST = 7; //Информацията не съществува
    case NO_REVIEW = 8; //Оставено без разглеждане

    // Return enum name by value
    public static function keyByValue($searchVal): string
    {
        $keyName = '';
        foreach (self::options() as $key => $val) {
            if( $val == $searchVal) {
                $keyName = $key;
            }
        }
        return $keyName;
    }

    public static function  notCompleted(): array
    {
        return [
            self::RECEIVED,
            self::REGISTRATION_TO_SUBJECT,
            self::IN_PROCESS,
        ];
    }
}
