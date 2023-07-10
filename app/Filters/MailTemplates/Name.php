<?php

namespace App\Filters\MailTemplates;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Name extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->where('name', 'ilike', '%'.$value.'%');
        }
    }
}

