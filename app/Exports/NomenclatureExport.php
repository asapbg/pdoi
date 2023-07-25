<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NomenclatureExport implements FromView, ShouldAutoSize {
    public function __construct($data, $type, $title, $extraData)
    {
        $this->data = $data;
        $this->type = $type;
        $this->title = $title;
        $this->extraData = $extraData;
    }

    public function view(): View
    {
        return view('exports.nomenclature.'.$this->type, [
            'items' => $this->data,
            'title' => $this->title,
            'extraData' => $this->extraData,
        ]);
    }
}
