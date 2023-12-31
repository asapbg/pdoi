<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StatisticExport implements FromView, ShouldAutoSize {
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.statistics.'.$this->data['type'], [
            'data' => $this->data
        ]);
    }
}
