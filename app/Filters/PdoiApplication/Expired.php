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
            $this->query->where(function ($q){
                return $q->where('pdoi_application.response_end_time', '<', Carbon::now())
                    ->whereIn('pdoi_application.status', PdoiApplicationStatusesEnum::notCompleted());
            });
        }
    }
}

