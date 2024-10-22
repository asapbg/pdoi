<?php

namespace App\Filters\PdoiApplication;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Filters\FilterContract;
use App\Filters\QueryFilter;
use Carbon\Carbon;


class Expired extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( $value > 0 ){
            $status = PdoiApplicationStatusesEnum::notCompleted();
            $status[] = PdoiApplicationStatusesEnum::NO_REVIEW->value;
            $this->query->where(function ($q) use($status){
                return $q->where('pdoi_application.response_end_time', '<', Carbon::now()->startOfDay())
                    ->whereIn('pdoi_application.status', $status)
                    ->whereNotIn('pdoi_application.status', [PdoiApplicationStatusesEnum::FORWARDED->value]);
//                return $q->where('pdoi_application.response_end_time', '<', Carbon::now()->startOfDay())
//                    ->whereIn('pdoi_application.status', $status);
            });
        }
    }
}

