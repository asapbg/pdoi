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

class StatisticTotalApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:applications_total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика за подадените чрез платформата за достъп до обществена информация заявления - общо';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = StatisticTypeEnum::TYPE_APPLICATION_STATUS_TOTAL->value;
        Log::info("Cron run statistic:applications_total");
        $statisticPeriod = date('mY', strtotime("-1 month"));
        $startPeriod = null;
        $endPeriod = Carbon::now()->endOfMonth()->subMonths(1);

        $lastReport = Statistic::where('type', '=', $type)->first();
        if( !$lastReport ) {
            exit;
        }

        if( $lastReport->period == $statisticPeriod ) {
            exit;
        }

        $data = PdoiApplication::publicStatistic($type, $startPeriod, $endPeriod);

        $lastReport->period = $statisticPeriod;
        $lastReport->json_data = $data;
        $lastReport->save();
    }
}

