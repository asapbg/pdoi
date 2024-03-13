<?php

namespace App\Http\Controllers;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\StatisticTypeEnum;
use App\Exports\StatisticExport;
use App\Models\PdoiApplication;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatisticController extends Controller
{
    public function index()
    {
        $titlePage = trans_choice('front.statistics', 2);
        return $this->view('front.statistic.index', compact('titlePage'));
    }

    public function show(Request $request, int $type)
    {
        $export = $request->filled('export');
        $from = $request->filled('from') ? $request->input('from') : Carbon::now()->subMonth();
        $to = $request->filled('to') ? $request->input('to') : Carbon::now();
        $filter = $this->filters($request);

        $extraChartData = [];
        $chartData = ['labels' => [], 'datasets' => []];

        $titlePage = __('front.statistic.type.'.StatisticTypeEnum::keyByValue($type));
        $titlePeriod =__('custom.statistics.for_period', ['period' => displayDate($from).' - '.displayDate($to)]);
        $data = PdoiApplication::publicStatistic($type, $from, $to);
        $arrayData = json_decode($data, true);

        if( $export ) {
            $exportData['type'] = 'applications_by_subjects';
            $exportData['data'] = [
                'title' => $titlePage,
                'period' => $titlePeriod,
                'statistic' => $arrayData
            ];
            return Excel::download(new StatisticExport($exportData), 'statistic_'.$type.'_'.Carbon::now()->format('Y_m_d_H_i_s').'.xlsx');
        }


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
            $extraChartData['scaleY']['max'] = 0;
            foreach ($statuses as $status) {
                $max = max(array_column($arrayData, 'cnt_'.$status));
                if( $max > $extraChartData['scaleY']['max'] ) {
                    $extraChartData['scaleY']['max'] = $max;
                }
                $chartData['datasets'][] = array(
                    'label' => __('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($status)),
                    'data' => array_column($arrayData, 'cnt_'.$status),
                    'borderColor' => $colors[$status],
                    'borderWidth' => 1,
                    'backgroundColor' => $colors[$status]
                );
            }

            $extraChartData['scaleY']['max'] += 2;
        }

        $this->setTitleSingular($titlePage);
        return $this->view('front.statistic.view', compact('titlePage', 'chartData', 'type',
            'extraChartData', 'titlePeriod', 'filter'));
    }

    private function filters($request)
    {
        return array(
            'from' => array(
                'type' => 'datepicker',
                'value' => $request->input('from') ?? displayDate(Carbon::now()->subMonth()),
                'placeholder' => __('custom.begin_date'),
                'col' => 'col-md-3'
            ),
            'to' => array(
                'type' => 'datepicker',
                'value' => $request->input('to') ?? displayDate(Carbon::now()),
                'placeholder' => __('custom.end_date'),
                'col' => 'col-md-3'
            ),
            'export' => true
        );
    }

    /**
     * @param Request $request
     * @param int $type
     * @return JsonResponse
     */
    public function apiStats(Request $request, int $type)
    {
        $from = $request->filled('from') ? $request->input('from') : Carbon::now()->subMonth();
        $to = $request->filled('to') ? $request->input('to') : Carbon::now();

        $titlePage = __('front.statistic.type.'.StatisticTypeEnum::keyByValue($type));
        $titlePeriod =__('custom.statistics.for_period', ['period' => displayDate($from).' - '.displayDate($to)]);
        $data = PdoiApplication::publicStatistic($type, $from, $to);
        $arrayData = json_decode($data, true);

        $total = [];
        $r_key = 1;
        $response[$r_key][$titlePage] = $titlePeriod;
        $r_key++;
        foreach ($arrayData as $datum) {

            $response[$r_key][trans_choice('custom.institutions', 1)] = $datum['name'];

            foreach(PdoiApplicationStatusesEnum::options() as $name => $key) {

                $val = $datum['cnt_'.$key] ?? 0;
                if (!isset($total[$key])) {
                    $total[$key] = 0;
                }
                $total[$key] += $val;

                $response[$r_key][__('custom.application.status.'.$name)] = $val;
            }

            $r_key++;
        }
        $r_key++;
        $response[$r_key][trans_choice('custom.institutions', 1)] = "Общо";
        foreach(PdoiApplicationStatusesEnum::options() as $name => $key) {
            $response[$r_key][__('custom.application.status.'.$name)] = $total[$key];
        }

        return response()->json($response);

    }
}
