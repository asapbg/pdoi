<?php

namespace App\Filters\PdoiApplication;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class ApplicationUri extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->where('application_uri', '=' , $value);
        }
    }
}

