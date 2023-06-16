<?php

namespace App\Filters\PdoiApplication;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;
use Carbon\Carbon;


class ToDate extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->where('pdoi_application.created_at', '<=', databaseDateTime(Carbon::parse($value)->endOfDay()));
        }
    }
}

