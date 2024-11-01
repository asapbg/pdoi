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
    case NO_REVIEW = 8; //Просрочено
    case FORWARDED = 9; //Препратено по компетентност
    case RENEWED = 10; //Възобновено
    case NO_CONSIDER_REASON = 11; //Оставено без разглеждане

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

    public static function styleByValue($searchVal): string
    {
        $colors = [
            self::RECEIVED->value => 'received',
            self::REGISTRATION_TO_SUBJECT->value => 'reg-subject',
            self::IN_PROCESS->value => 'in-process',
            self::APPROVED->value => 'approved',
            self::PART_APPROVED->value => 'part-approved',
            self::NOT_APPROVED->value => 'not-approved',
            self::INFO_NOT_EXIST->value => 'no-info',
            self::FORWARDED->value => 'forwarded',
            self::RENEWED->value => 'renewed',
            self::NO_CONSIDER_REASON->value => 'no-consider',
            self::NO_REVIEW->value => 'no-review'
        ];
        return $colors[$searchVal];
    }

    public static function  notCompleted(): array
    {
        return [
            self::RECEIVED->value,
            self::REGISTRATION_TO_SUBJECT->value,
            self::IN_PROCESS->value,
            self::FORWARDED->value,
//            self::NO_REVIEW->value,
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
            self::FORWARDED,
            self::NO_CONSIDER_REASON
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

    public static function canEditFinalDecision($value): bool
    {
        return in_array($value, [
            self::APPROVED->value,
            self::PART_APPROVED->value,
            self::NOT_APPROVED->value,
            self::NO_CONSIDER_REASON->value,
            self::NO_REVIEW
        ]);
    }

    public static function canForward($value): bool
    {
        return in_array($value, [
            self::RECEIVED->value,
            self::IN_PROCESS->value,
            self::FORWARDED->value,
        ]);
    }
}
