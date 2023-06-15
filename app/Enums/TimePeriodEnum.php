<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;
use Carbon\Carbon;

enum TimePeriodEnum: string
{
    use Names, Options, Values;

    case TODAY = 'today'; //Днес
    case YESTERDAY = 'yesterday'; //Вчера
    case CURRENT_MONTH = 'current_month'; //Този месец
    case PREV_WEEK = 'prev_week'; //Миналата седмица
    case PREV_MONTH = 'prev_month'; //Минаия месец
    case CURRENT_YEAR = 'current_year'; //Тази година
    case PREV_YEAR = 'prev_year'; //Миналата година
    case LAST_5_YEAR = 'last_5_year'; //Последните 5 години


    // Return period parameters
    public static function parameters($name): array
    {
        return match ($name) {
            self::YESTERDAY => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            self::CURRENT_MONTH => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            self::PREV_WEEK => [Carbon::now()->startOfWeek()->subWeek()->startOfDay(), Carbon::now()->endOfWeek()->subWeek()->endOfDay()],
            self::PREV_MONTH => [Carbon::now()->startOfMonth()->subMonth()->startOfDay(), Carbon::now()->endOfMonth()->subMonth()->endOfDay()],
            self::CURRENT_YEAR => [Carbon::now()->startOfYear()->startOfDay(), Carbon::now()->endOfYear()->endOfDay()],
            self::PREV_YEAR => [Carbon::now()->startOfYear()->subYear()->startOfDay(), Carbon::now()->endOfYear()->subYear()->endOfDay()],
            self::LAST_5_YEAR => [Carbon::now()->startOfYear()->subYears(5)->startOfDay(), Carbon::now()->endOfYear()->subYears(5)->endOfDay()],
            default => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()], //today
        };
    }
}
