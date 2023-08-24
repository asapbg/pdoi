<?php

namespace App\Http\Controllers;

use App\Enums\StatisticTypeEnum;
use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $titlePage = trans_choice('front.statistics', 2);
        return $this->view('front.statistic.index', compact('titlePage'));
    }

    public function show(Request $request, int $type, string $period = '')
    {
        $titlePage = __('front.statistic.type.'.StatisticTypeEnum::keyByValue($type));
        $q = Statistic::where('type', '=', $type);
        if( !empty($period) ) {
            $q->where('period', '=', $period);
            $data = $q->first();
        } else {
            $data = $q->latest('id')->first();
        }

        return $this->view('front.statistic.view', compact('titlePage', 'data'));
    }
}
