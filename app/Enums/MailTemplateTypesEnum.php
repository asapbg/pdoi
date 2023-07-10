<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum MailTemplateTypesEnum: int
{
    use Names, Options, Values;

    case RZS_AUTO_FORWARD = 1;
    case RZS_MANUAL_FORWARD = 2;

    public static function isDeletable(int $value): bool
    {
        $editableTemplates = [];
        return in_array($value, $editableTemplates);
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
