<?php

namespace App\Filters\PdoiResponseSubject;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Manual extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->where('pdoi_response_subject.adm_register', '=', $value);
        }
    }
}

