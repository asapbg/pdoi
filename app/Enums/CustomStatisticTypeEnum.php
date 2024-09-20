<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum CustomStatisticTypeEnum: int
{
    use Names, Options, Values;

    case TYPE_BASE = 1; //Статистика с два елемента: label -> value

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

    public static function fileExamplesByValue($value): string
    {
        $files = [
            self::TYPE_BASE->value => 'test_standard_type.csv'
        ];

        return $files[$value];
    }
}
