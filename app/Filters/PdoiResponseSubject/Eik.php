<?php

namespace App\Filters\PdoiResponseSubject;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Eik extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->where('eik', $value);
        }
    }
}

