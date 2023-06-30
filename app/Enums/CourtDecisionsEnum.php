<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum CourtDecisionsEnum: int
{
    use Names, Options, Values;

    case CANCEL = 1; //Съдът отменя решението за отказ
    case CONFIRMS = 2; //Съдът потвърждава решението за отказ
    case CHANGE = 3; //Съдът изменя решението решението за отказ

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

    public static function isReopenAvailable($value): bool
    {
        $decisionWithReopenOption = [
            self::CHANGE->value, self::CANCEL->value
        ];
        return in_array((int)$value, $decisionWithReopenOption);
    }
}
