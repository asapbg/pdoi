<?php

namespace App\Traits;

use App\Filters\FilterBuilder;
use App\Sorter\SorterBuilder;

trait FilterSort
{
    /**
     * @param $query
     * @param $sorter string | null
     * @param $direction string | null
     * @return mixed
     */
    public function scopeSortedBy($query, ?string $sorter, ?string $direction)
    {
        $a = new \ReflectionClass(get_class());
        $namespace = "App\Sorter\\".$a->getShortName();
        $sorter = new SorterBuilder($query, $sorter, $direction, $namespace);

        return $sorter->apply();
    }

    /**
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeFilterBy($query, $search)
    {
        $a = new \ReflectionClass(get_class());
        $namespace = "App\Filters\\".$a->getShortName();
        $filter = new FilterBuilder($query, $search, $namespace);

        return $filter->apply();
    }
}
