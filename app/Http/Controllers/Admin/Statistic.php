<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Statistic extends Controller
{

    const TYPE_BASE = 'base';
    const TYPE_APPLICATIONS = 'applications';
    const TYPE_RENEW = 'renew';
    const TYPE_FORWARDED = 'forward';
    const TYPE_TERMS = 'terms';

    private function isAuth(){
        if( !auth()->user() || !auth()->user()->canany(['manage.*', 'statistic.*']) ) {
            return abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function index(Request $request)
    {
        $this->isAuth();
        $this->setTitlePlural(trans_choice('custom.statistics', 2));
        $statistics = array(
            [
                'name' => __('custom.statistics.base'),
                'description' => null,
                'icon_class' => 'fas fa-layer-group text-info',
                'url' => route('admin.statistic.type', ['type' => self::TYPE_BASE])
            ],
            [
                'name' => __('custom.statistics.applications'),
                'description' => null,
                'icon_class' => 'far fa-file-alt text-warning',
                'url' => route('admin.statistic.type', ['type' => self::TYPE_APPLICATIONS])
            ],
        );
        $statisticLinks = array(
            [
                'name' => __('custom.statistics.forward'),
                'url' => route('admin.statistic.type', ['type' => self::TYPE_FORWARDED])
            ],
            [
                'name' => __('custom.statistics.renew'),
                'url' => route('admin.statistic.type', ['type' => self::TYPE_RENEW])
            ],
            [
                'name' => __('custom.statistics.terms'),
                'url' => route('admin.statistic.type', ['type' => self::TYPE_TERMS])
            ]
        );
        return $this->view('admin.statistic.index', compact('statistics', 'statisticLinks'));
    }

    public function statistic(Request $request, $type = self::TYPE_BASE)
    {
        $data = array();
        $this->isAuth();

        switch ($type)
        {
            case self::TYPE_APPLICATIONS:
                $data['filter'] = $this->filter($type);
                $requestFilter = $request->all();
                $requestFilter['groupBy'] = $requestFilter['groupBy'] ?? 'subject';
                $this->setTitles(trans_choice('custom.statistics', 1).' '.trans_choice('custom.applications', 2));
                $data['name_title'] = __('custom.statistics.applications.name_column.'.$requestFilter['groupBy']);
                $data['statistic'] = PdoiApplication::statisticGroupBy($requestFilter);
                $data['groupedBy'] = $requestFilter['groupBy'];
                break;
            case self::TYPE_RENEW:
                $data['filter'] = $this->filter($type);
                $requestFilter = $request->all();
                $this->setTitles(__('custom.statistics.renew'));
                $data['statistic'] = PdoiApplication::statisticRenewed($requestFilter);
                break;
            case self::TYPE_TERMS:
                $data['filter'] = $this->filter($type);
                $requestFilter = $request->all();
                $this->setTitles(__('custom.statistics.terms'));
                $data['statistic'] = PdoiApplication::statisticTerms($requestFilter);
                break;
            case self::TYPE_FORWARDED:
                $data['filter'] = $this->filter($type);
                $requestFilter = $request->all();
                $this->setTitles(__('custom.statistics.forward'));
                $data['statistic'] = PdoiApplication::statisticForwarded($requestFilter);
                break;
            default:
                $this->setTitles(__('custom.statistics.'.$type).' '.trans_choice('custom.statistics', 2));
                $data['user_type'] = [
                    'title' => __('custom.statistics.base.user_types_cnt'),
                    'statistic' => User::statisticCntByUserType()
                ];
                $data['subjects_with_admin'] = [
                    'title' => __('custom.statistics.base.subjects_with_admin'),
                    'statistic' => PdoiResponseSubject::statisticSubjectsWithAdmin()
                ];
        }

        return $this->view('admin.statistic.'.$type, compact('data'));
    }

    private function filter($type): array
    {
        return match ($type) {
            self::TYPE_APPLICATIONS => array(
                'groupBy' => [
                    'type' => 'select',
                    'options' => statisticApplicationGroupByOptions(true, '', __('custom.group_by')),
                    'value' => request()->input('groupBy') ?? 'subject',
                    'default' => '',
                    'col' => 'col-md-4'
                ],
                'formDate' => array(
                    'type' => 'datepicker',
                    'value' => request()->input('formDate') ?? Carbon::now()->startOfMonth()->format('d-m-Y'),
                    'placeholder' => __('custom.begin_date'),
                    'col' => 'col-md-2'
                ),
                'toDate' => array(
                    'type' => 'datepicker',
                    'value' => request()->input('toDate') ?? Carbon::now()->endOfMonth()->format('d-m-Y'),
                    'placeholder' => __('custom.end_date'),
                    'col' => 'col-md-2'
                ),
                'status' => array(
                    'type' => 'select',
                    'options' => optionsApplicationStatus(true, '', __('custom.status')),
                    'default' => '',
                    'value' => request()->input('status'),
                    'col' => 'col-md-4'
                ),
                'category' => array(
                    'type' => 'select',
                    'options' => optionsFromModel(Category::optionsList(), true,'', trans_choice('custom.categories',1)),
                    'default' => '',
                    'value' => request()->input('category'),
                    'col' => 'col-md-4'
                ),
                'subject' => array(
                    'type' => 'subjects',
                    'placeholder' => trans_choice('custom.pdoi_response_subjects', 1),
                    'options' => optionsFromModel(PdoiResponseSubject::simpleOptionsList(), true, '', trans_choice('custom.pdoi_response_subjects', 1)),
                    'value' => request()->input('subject'),
                    'default' => '',
                )
            ),
            self::TYPE_FORWARDED,
            self::TYPE_TERMS,
            self::TYPE_RENEW => array(
                'formDate' => array(
                    'type' => 'datepicker',
                    'value' => request()->input('formDate') ?? Carbon::now()->startOfMonth()->format('d-m-Y'),
                    'placeholder' => __('custom.begin_date'),
                    'col' => 'col-md-2'
                ),
                'toDate' => array(
                    'type' => 'datepicker',
                    'value' => request()->input('toDate') ?? Carbon::now()->endOfMonth()->format('d-m-Y'),
                    'placeholder' => __('custom.end_date'),
                    'col' => 'col-md-2'
                ),
                'subject' => array(
                    'type' => 'subjects',
                    'placeholder' => trans_choice('custom.pdoi_response_subjects', 1),
                    'options' => optionsFromModel(PdoiResponseSubject::simpleOptionsList(), true, '', trans_choice('custom.pdoi_response_subjects', 1)),
                    'value' => request()->input('subject'),
                    'default' => '',
                )
            ),
            default => [],
        };
    }
}
