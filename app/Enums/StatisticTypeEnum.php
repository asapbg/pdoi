<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum StatisticTypeEnum: int
{
    use Names, Options, Values;

    case TYPE_APPLICATION_MONTH = 1; //Статистика за подадени заявления по задължен субект, тип заявител и статус - месечна база
    case TYPE_APPLICATION_STATUS_SIX_MONTH = 2; //Брой заявления по задължен субект и статус за период
    case TYPE_APPLICATION_STATUS_TOTAL = 3; //Брой заявления по задължен субект и статус (Общо)

    // Return enum name by value
    public static function keyByValue($searchVal): string
    {
        $keyName = '';
        foreach (self::options() as $key => $val) {
            if ($val == $searchVal) {
                $keyName = $key;
            }
        }
        return $keyName;
    }
}
