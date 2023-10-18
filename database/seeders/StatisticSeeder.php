<?php

namespace Database\Seeders;

use App\Enums\StatisticTypeEnum;
use App\Models\PdoiApplication;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statistics = array(
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '012019',
                't1' => '2019-01-01 00:00:00',
                't2' => '2019-01-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '022019',
                't1' => '2019-02-01 00:00:00',
                't2' => '2019-02-28 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '032019',
                't1' => '2019-03-01 00:00:00',
                't2' => '2019-03-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '042019',
                't1' => '2019-04-01 00:00:00',
                't2' => '2019-04-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '052019',
                't1' => '2019-05-01 00:00:00',
                't2' => '2019-05-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '062019',
                't1' => '2019-06-01 00:00:00',
                't2' => '2019-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '072019',
                't1' => '2019-07-01 00:00:00',
                't2' => '2019-07-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '082019',
                't1' => '2019-08-01 00:00:00',
                't2' => '2019-08-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '092019',
                't1' => '2019-09-01 00:00:00',
                't2' => '2019-09-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '102019',
                't1' => '2019-10-01 00:00:00',
                't2' => '2019-10-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '112019',
                't1' => '2019-11-01 00:00:00',
                't2' => '2019-11-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '122019',
                't1' => '2019-12-01 00:00:00',
                't2' => '2019-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '012020',
                't1' => '2020-01-01 00:00:00',
                't2' => '2020-01-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '022020',
                't1' => '2020-02-01 00:00:00',
                't2' => '2020-02-29 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '032020',
                't1' => '2020-03-01 00:00:00',
                't2' => '2020-03-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '042020',
                't1' => '2020-04-01 00:00:00',
                't2' => '2020-04-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '052020',
                't1' => '2020-05-01 00:00:00',
                't2' => '2020-05-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '062020',
                't1' => '2020-06-01 00:00:00',
                't2' => '2020-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '072020',
                't1' => '2020-07-01 00:00:00',
                't2' => '2020-07-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '082020',
                't1' => '2020-08-01 00:00:00',
                't2' => '2020-08-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '092020',
                't1' => '2020-09-01 00:00:00',
                't2' => '2020-09-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '102020',
                't1' => '2020-10-01 00:00:00',
                't2' => '2020-10-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '112020',
                't1' => '2020-11-01 00:00:00',
                't2' => '2020-11-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '122020',
                't1' => '2020-12-01 00:00:00',
                't2' => '2020-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '012021',
                't1' => '2021-01-01 00:00:00',
                't2' => '2021-01-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '022021',
                't1' => '2021-02-01 00:00:00',
                't2' => '2021-02-28 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '032021',
                't1' => '2021-03-01 00:00:00',
                't2' => '2021-03-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '042021',
                't1' => '2021-04-01 00:00:00',
                't2' => '2021-04-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '052021',
                't1' => '2021-05-01 00:00:00',
                't2' => '2021-05-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '062021',
                't1' => '2021-06-01 00:00:00',
                't2' => '2021-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '072021',
                't1' => '2021-07-01 00:00:00',
                't2' => '2021-07-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '082021',
                't1' => '2021-08-01 00:00:00',
                't2' => '2021-08-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '092021',
                't1' => '2021-09-01 00:00:00',
                't2' => '2021-09-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '102021',
                't1' => '2021-10-01 00:00:00',
                't2' => '2021-10-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '112021',
                't1' => '2021-11-01 00:00:00',
                't2' => '2021-11-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '122021',
                't1' => '2021-12-01 00:00:00',
                't2' => '2021-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '012022',
                't1' => '2022-01-01 00:00:00',
                't2' => '2022-01-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '022022',
                't1' => '2022-02-01 00:00:00',
                't2' => '2022-02-28 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '032022',
                't1' => '2022-03-01 00:00:00',
                't2' => '2022-03-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '042022',
                't1' => '2022-04-01 00:00:00',
                't2' => '2022-04-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '052022',
                't1' => '2022-05-01 00:00:00',
                't2' => '2022-05-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '062022',
                't1' => '2022-06-01 00:00:00',
                't2' => '2022-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '072022',
                't1' => '2022-07-01 00:00:00',
                't2' => '2022-07-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '082022',
                't1' => '2022-08-01 00:00:00',
                't2' => '2022-08-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_MONTH->value,
                'period' => '092022',
                't1' => '2022-09-01 00:00:00',
                't2' => '2022-09-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '01062019',
                't1' => '2019-01-01 00:00:00',
                't2' => '2019-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '07122019',
                't1' => '2019-07-01 00:00:00',
                't2' => '2019-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '01062020',
                't1' => '2020-01-01 00:00:00',
                't2' => '2020-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '07122020',
                't1' => '2020-07-01 00:00:00',
                't2' => '2020-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '01062021',
                't1' => '2021-01-01 00:00:00',
                't2' => '2021-06-30 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '07122021',
                't1' => '2021-07-01 00:00:00',
                't2' => '2021-12-31 23:59:59'
            ],
            [
                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_SIX_MONTH->value,
                'period' => '01062022',
                't1' => '2022-01-01 00:00:00',
                't2' => '2022-06-30 23:59:59'
            ],
//            [
//                'type' => StatisticTypeEnum::TYPE_APPLICATION_STATUS_TOTAL->value,
//                'period' => '072023',
//                't1' => null,
//                't2' => Carbon::now()
//            ],
        );

        foreach ($statistics as $item) {
            $data = PdoiApplication::publicStatistic($item['type'], $item['t1'], $item['t2']);
            Statistic::create([
                'type' => $item['type'],
                'period' => $item['period'],
                'json_data' => $data,
            ]);
        }
    }
}
