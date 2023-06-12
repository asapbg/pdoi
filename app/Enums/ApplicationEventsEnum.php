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

}
