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
        $availablePeriods = [];
        $periods = Statistic::where('type', '=', $type)->orderBy('id', 'desc')->get()->pluck('period');
        if( $periods ) {
            foreach ($periods as $p) {
                $availablePeriods[$p] = match ($type) {
                    StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value => substr($p, 0, 2) . '.' . substr($p, -4) . ' - ' . substr($p, 2, 2) . '.' . substr($p, -4),
                    default => substr($p, 0, 2) . '.' . substr($p, 2),
                };
            }
        }

        $titlePage = __('front.statistic.type.'.StatisticTypeEnum::keyByValue($type));
        $q = Statistic::where('type', '=', $type);
        if( !empty($period) ) {
            $q->where('period', '=', $period);
            $data = $q->first();
        } else {
            $data = $q->latest('id')->first();
        }

        return $this->view('front.statistic.view', compact('titlePage', 'data', 'availablePeriods', 'type'));
    }
}
