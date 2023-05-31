<?php namespace App\Sorter;

interface SorterContract {
    public function handle($value): void;
}
