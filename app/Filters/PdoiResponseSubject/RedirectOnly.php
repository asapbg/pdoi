<?php

namespace App\Filters\PdoiResponseSubject;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class RedirectOnly extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( in_array($value, [0,1]) ){
            $this->query->where('pdoi_response_subject.redirect_only', '=', $value);
        }
    }
}

