<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('check:expired_application')->everyThirtyMinutes();
         $schedule->command('sync:iisda')->daily();
         $schedule->command('notification:email')->everyFifteenMinutes();

         //statistics
        $schedule->command('statistic:applications_monthly')->daily();
        $schedule->command('statistic:applications_per_six_months')->daily();
        $schedule->command('statistic:applications_total')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
