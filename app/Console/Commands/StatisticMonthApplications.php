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

class StatisticMonthApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:applications_monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика за подадени заявления по задължен субект, тип заявител и статус - месечна база';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = StatisticTypeEnum::TYPE_APPLICATION_MONTH->value;
        Log::info("Cron run statistic:applications_monthly");
        $statisticPeriod = date('mY', strtotime("-1 month"));
        if( Statistic::where('period', '=', $statisticPeriod)->where('type', '=', $type)->first() ) {
            exit;
        }

        $startPeriod = Carbon::now()->startOfMonth()->subMonths(1);
        $endPeriod = Carbon::now()->endOfMonth()->subMonths(1);
        $data = PdoiApplication::publicStatistic($type, $startPeriod, $endPeriod);

        Statistic::create([
            'type' => $type,
            'period' => $statisticPeriod,
            'json_data' => $data,
        ]);

    }
}

