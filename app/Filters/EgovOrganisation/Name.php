<?php

namespace App\Filters\EgovOrganisation;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Name extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            $this->query->whereHas('translations', function ($query) use ($value) {
                $query->where('locale', app()->getLocale());
                $query->where('name', 'ilike', '%'.$value.'%');
            });
        }
    }
}

