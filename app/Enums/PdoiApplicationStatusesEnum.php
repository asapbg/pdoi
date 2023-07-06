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
    case FORWARDED = 9; //Препратено по компетентност
    case RENEWED = 10; //Възобновено

//    case FORWARD_ТО_SUB_SUBJECT = 10; //Препратено по компетентност към подчинен субект
//    case FORWARD_ТО_NOT_REGISTERED_SUBJECT = 11; //Препратено по компетентност на субект, нерегистриран на платформата

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
            self::RECEIVED->value,
            self::REGISTRATION_TO_SUBJECT->value,
            self::IN_PROCESS->value,
            self::FORWARDED->value,
//            self::FORWARD_ТО_SUB_SUBJECT,
//            self::FORWARD_ТО_NOT_REGISTERED_SUBJECT
        ];
    }

    public static function finalStatuses(): array
    {
        return [
            self::APPROVED,
            self::PART_APPROVED,
            self::NOT_APPROVED,
            self::INFO_NOT_EXIST,
            self::FORWARDED
        ];
    }

    public static function isFinalStatus($value): bool
    {
        $check = false;
        foreach (self::finalStatuses() as $item){
            if( $item->value == (int)$value ) {
                $check = true;
            }
        }
        return $check;
    }

    public static function canRenew($value): bool
    {
        return in_array($value, [
            self::NOT_APPROVED->value
            , self::PART_APPROVED->value
        ]);
    }

    public static function canForward($value): bool
    {
        return in_array($value, [
            self::IN_PROCESS->value,
            self::FORWARDED->value,
        ]);
    }
}
