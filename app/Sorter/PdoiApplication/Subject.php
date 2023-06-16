<?php namespace App\Sorter\PdoiApplication;

use App\Sorter\SorterContract;
use App\Sorter\QuerySorter;

class Subject extends QuerySorter implements SorterContract{

    public function handle($direction): void
    {
        $direction = !is_null($direction) ? $direction : 'asc';
        //TODO fix me
        //we must change list select with join to use auto sort
    }
}


