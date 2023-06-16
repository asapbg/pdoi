<?php namespace App\Sorter\PdoiApplication;

use App\Sorter\SorterContract;
use App\Sorter\QuerySorter;

class ApplyDate extends QuerySorter implements SorterContract{

    public function handle($value): void
    {
        $direction = $value ?? 'asc';
        $this->query->orderBy('pdoi_application.created_at', $direction);
    }
}


