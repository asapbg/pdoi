<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum ApplicationEventsEnum: int
{
    use Names, Options, Values;

    case SEND = 9; //Подаване на заявление
    case SEND_TO_SEOS = 7; //Изпратено към деловодна система
    case APPROVE_BY_SEOS = 8; //Потвърждение от деловодна система
    case ASK_FOR_INFO = 2; //Искане на допълнителна информация
    case GIVE_INFO = 3; //Предоставяне на допълнителна информация
    case FORWARD = 4; //Препращане на заявление
    case EXTEND_TERM = 5; //Удължаване на срока
    case FINAL_DECISION = 6; //Крайно решение
    case RENEW_PROCEDURE = 10; //Възобновяване на процедура
    case FORWARD_TO_SUB_SUBJECT = 11; //Препратено по компетентност към подчинен субект
    case FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT = 12; //Препратено по компетентност към подчинен субект (извън платформата)
    case FORWARD_TO_NOT_REGISTERED_SUBJECT = 13; //Препратено по компетентност извън платформата
    case MANUAL_REGISTER = 14; //Ръчно регистриране в процес на обработка

    public static function userEvents(): array
    {
        return [
            self::ASK_FOR_INFO->value,
            self::FORWARD->value,
            self::EXTEND_TERM->value,
            self::FINAL_DECISION->value,
        ];
    }

    public static function notAllowDecisionToExpiredApplication(): array
    {
        return [
            self::ASK_FOR_INFO->value,
//            self::GIVE_INFO->value,
//            self::FORWARD->value,
//            self::EXTEND_TERM->value,
//            self::FINAL_DECISION->value,
            self::RENEW_PROCEDURE->value,
//            self::FORWARD_TO_SUB_SUBJECT->value,
//            self::FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT->value,
//            self::FORWARD_TO_NOT_REGISTERED_SUBJECT->value,
        ];
    }

    public static function forwardGroupEvents(): array
    {
        return [
            self::FORWARD->value,
            self::FORWARD_TO_SUB_SUBJECT->value,
            self::FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT->value,
            self::FORWARD_TO_NOT_REGISTERED_SUBJECT->value,
        ];
    }

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

}
