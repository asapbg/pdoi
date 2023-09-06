<?php

namespace App\Filters\PdoiApplication;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;
use App\Models\User;


class Text extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->whereRaw('pdoi_application.request_ts_bg @@ plainto_tsquery(\'bulgarian\', ?)', [$value]);
        }
    }
}
