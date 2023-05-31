<?php namespace App\Sorter;

abstract class QuerySorter {
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }
}
