<?php

namespace App\Http\Controllers;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\StatisticTypeEnum;
use App\Exports\StatisticExport;
use App\Models\CustomStatistic;
use App\Models\PdoiApplication;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class StatisticController extends Controller
{
    public function index()
    {
        $titlePage = trans_choice('front.statistics', 2);
        $customStatistics = CustomStatistic::IsPublished()->get();
        return $this->view('front.statistic.index', compact('titlePage', 'customStatistics'));
    }

    public function showCustom($id = 0)
    {
        $item = CustomStatistic::IsPublished()->find($id);

        if(!$item){
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->view('front.statistic.view_custon_statistic', compact('item'));
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
                PdoiApplicationStatusesEnum::RECEIVED->value => '#ECEAEAFF',
                PdoiApplicationStatusesEnum::REGISTRATION_TO_SUBJECT->value => '#A10037FF',
                PdoiApplicationStatusesEnum::IN_PROCESS->value => '#6C757DFF',
                PdoiApplicationStatusesEnum::APPROVED->value => '#6ABE7DFF',
                PdoiApplicationStatusesEnum::PART_APPROVED->value => '#0A8122FF',
                PdoiApplicationStatusesEnum::NOT_APPROVED->value => '#DC3545FF',
                PdoiApplicationStatusesEnum::INFO_NOT_EXIST->value => '#026AF1FF',
                PdoiApplicationStatusesEnum::NO_REVIEW->value => '#DEC1FDFF',
                PdoiApplicationStatusesEnum::FORWARDED->value => '#17A2B8FF',
                PdoiApplicationStatusesEnum::RENEWED->value => '#CFE138FF',
                PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value => '#FFC107FF',
            ];

            $chartData['labels'] = array_column($arrayData, 'name');
            $extraChartData['scaleY']['max'] = 0;
            foreach ($statuses as $status) {
                if(!sizeof(array_column($arrayData, 'cnt_'.$status))){
                    dd('cnt_'.$status, $arrayData);
                }
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
        $results = json_decode($data, true);

        $total = [];
        $r_key = 1;

        $response[$r_key]['INSTITUTION'] = "Институция";
        foreach(PdoiApplicationStatusesEnum::options() as $name => $key) {
            $response[$r_key][$name] = __('custom.application.status.'.$name);
        }
        $r_key++;
        foreach ($results as $result) {

            $response[$r_key]['INSTITUTION'] = $result['name'];

            foreach(PdoiApplicationStatusesEnum::options() as $name => $key) {

                $val = $result['cnt_'.$key] ?? 0;
                if (!isset($total[$key])) {
                    $total[$key] = 0;
                }
                $total[$key] += $val;

                $response[$r_key][$name] = $val;
            }

            $r_key++;
        }
        $r_key++;
        $response[$r_key][trans_choice('custom.institutions', 1)] = "Общо";
        foreach(PdoiApplicationStatusesEnum::options() as $name => $key) {
            $response[$r_key][$name] = $total[$key];
        }

        return response()->json($response);

    }
}
