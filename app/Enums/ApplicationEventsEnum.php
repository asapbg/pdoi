<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum ApplicationEventsEnum: int
{
    use Names, Options, Values;

    case SEND = 9; //Подаване на заявление
    case SEND_TO_RKS = 7; //Изпратено към деловодна система
    case APPROVE_BY_RKS = 8; //Потвърждение от деловодна система
    case ASK_FOR_INFO = 2; //Искане на допълнителна информация
    case GIVE_INFO = 3; //Предоставяне на допълнителна информация
    case FORWARD = 4; //Препращане на заявление
    case EXTEND_TERM = 5; //Удължаване на срока
    case FINAL_DECISION = 6; //Крайно решение

    public static function userEvents(): array
    {
        return [
            self::ASK_FOR_INFO->value,
            self::FORWARD->value,
            self::EXTEND_TERM->value,
            self::FINAL_DECISION->value,
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
