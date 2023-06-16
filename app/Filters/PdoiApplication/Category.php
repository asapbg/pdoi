<?php

namespace App\Filters\PdoiApplication;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class Category extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( $value > 0 ){
            $this->query->whereHas('categories', function($q) use($value){
                return $q->where('category.id', $value);
            });
        }
    }
}

