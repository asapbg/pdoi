<?php

namespace App\Console\Commands;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\StatisticTypeEnum;
use App\Models\PdoiApplication;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StatisticSixMonthApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:applications_per_six_months';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика за подадените чрез платформата за достъп до обществена информация заявления - по полугодия';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value;
        Log::info("Cron run statistic:applications_per_six_months");
        $currentMonth = (int)date('m');
        if( $currentMonth < 7 ) {
            //need to create report for second six moths of previous year
            $statisticPeriod = Carbon::now()->subYears(1)->startOfYear()->addMonths(6)->format('m')
                .Carbon::now()->subYears(1)->endOfYear()->format('m')
                .Carbon::now()->subYears(1)->format('Y');
            $startPeriod = Carbon::now()->subYears(1)->startOfYear()->addMonths(6);
            $endPeriod = Carbon::now()->subYears(1)->endOfYear();
        } else {
            //need to create report for first six moths of this year
            $statisticPeriod = Carbon::now()->startOfYear()->format('m')
                .Carbon::now()->startOfYear()->addMonths(6)->format('m')
                .Carbon::now()->format('Y');
            $startPeriod = Carbon::now()->startOfYear();
            $endPeriod = Carbon::now()->startOfYear()->addMonths(6);
        }

        if( Statistic::where('period', '=', $statisticPeriod)->where('type', '=', $type)->first() ) {
            exit;
        }

        $data = PdoiApplication::publicStatistic($type, $startPeriod, $endPeriod);

        Statistic::create([
            'type' => $type,
            'period' => $statisticPeriod,
            'json_data' => $data,
        ]);
    }
}

