<?php

namespace App\Filters\RzsSection;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Active extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( in_array($value, [0,1]) ){
            $this->query->where('rzs_section.active', '=', $value);
        }
    }
}

