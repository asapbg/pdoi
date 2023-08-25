<?php

namespace App\Http\Controllers;

use App\Enums\PdoiApplicationStatusesEnum;
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
        $extraChartData = [];
        $chartData = ['labels' => [], 'datasets' => []];
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
            $dbData = $q->first();
        } else {
            $dbData = $q->latest('id')->first();
        }

        $arrayData = json_decode($dbData['json_data'], true);

        if( $arrayData ) {
            $statuses = PdoiApplicationStatusesEnum::values();
            $colors = [
                PdoiApplicationStatusesEnum::RECEIVED->value => '#1D1289FF',
                PdoiApplicationStatusesEnum::REGISTRATION_TO_SUBJECT->value => '#890F0FFF',
                PdoiApplicationStatusesEnum::IN_PROCESS->value => '#128914FF',
                PdoiApplicationStatusesEnum::APPROVED->value => '#C2AA0BFF',
                PdoiApplicationStatusesEnum::PART_APPROVED->value => '#EA6749FF',
                PdoiApplicationStatusesEnum::NOT_APPROVED->value => '#64DE04FF',
                PdoiApplicationStatusesEnum::INFO_NOT_EXIST->value => '#890F0FFF',
                PdoiApplicationStatusesEnum::NO_REVIEW->value => '#AF2CF1FF',
                PdoiApplicationStatusesEnum::FORWARDED->value => '#2CF1ACFF',
                PdoiApplicationStatusesEnum::RENEWED->value => '#F19F34FF',
            ];

            $chartData['labels'] = array_column($arrayData, 'name');
            switch ($type)
            {
                case StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value:
                case StatisticTypeEnum::TYPE_APPLICATION_STATUS_TOTAL->value:
                    $extraChartData['scaleX']['max'] = 0;
                    foreach ($statuses as $status) {
                        $max = max(array_column($arrayData, 'cnt_'.$status));
                        if( $max > $extraChartData['scaleX']['max'] ) {
                            $extraChartData['scaleX']['max'] = $max;
                        }
                        $chartData['datasets'][] = array(
                            'label' => __('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($status)),
                            'data' => array_column($arrayData, 'cnt_'.$status),
                            'backgroundColor' => $colors[$status]
                        );
                    }

                    $extraChartData['scaleX']['max'] += 2;
                    break;
                default:
                    $extraChartData['scaleX']['max'] = 0;
                    foreach ($statuses as $status) {
                        $max = max(array_column($arrayData, 'cnt_'.$status));
                        if( $max > $extraChartData['scaleX']['max'] ) {
                            $extraChartData['scaleX']['max'] = $max;
                        }
                        $chartData['datasets'][] = array(
                            'label' => __('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($status)),
                            'data' => array_column($arrayData, 'cnt_'.$status),
                            'backgroundColor' => $colors[$status]
                        );
                    }

                    $extraChartData['scaleX']['max'] += 2;
            }
        }


        return $this->view('front.statistic.view', compact('titlePage', 'chartData', 'availablePeriods', 'type', 'extraChartData'));
    }
}
