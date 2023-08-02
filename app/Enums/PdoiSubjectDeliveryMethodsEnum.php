<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum PdoiSubjectDeliveryMethodsEnum: int
{
    use Names, Options, Values;

    case EMAIL = 1;
    case SDES = 2; //Secure electronic delivery system => ССЕВ
    case SEOS = 3; //деловодство
}
