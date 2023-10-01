<?php

namespace App\Filters\PdoiResponseSubject;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Filters\FilterContract;
use App\Filters\QueryFilter;


class DeliveryMethod extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        $value = (int)$value;
        if( in_array($value, PdoiSubjectDeliveryMethodsEnum::values()) ){
            $this->query->where('pdoi_response_subject.delivery_method', '=', $value);
        }
    }
}

