<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum DeliveryMethodsEnum: int
{
    use Names, Options, Values;

    case EMAIL = 1;
    case SDES = 2;
}
