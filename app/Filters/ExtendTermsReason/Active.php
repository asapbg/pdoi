<?php

namespace App\Filters\ExtendTermsReason;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Active extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( in_array($value, [0,1]) ){
            $this->query->where('extend_terms_reason.active', '=', $value);
        }
    }
}

