<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum PdoiApplicationStatusesEnum: int
{
    use Names, Options, Values;

    case RECEIVED = 1;
    case REGISTRATION_TO_SUBJECT = 2;
    case IN_PROCESS = 3;
    case APPROVED = 4;
    case PART_APPROVED = 5;
    case NOT_APPROVED = 6;
    case INFO_NOT_EXIST = 7;
    case NO_REVIEW = 8;
}
