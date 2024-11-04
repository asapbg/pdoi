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
         $schedule->command('check:expired_application')->everyFourHours();
         $schedule->command('sync:iisda')->daily();
         $schedule->command('notification:email')->everyFifteenMinutes();
         $schedule->command('notify:unlocked_applications')->hourly();
        $schedule->command('notify:soon_expired_applications')->daily();
        $schedule->command('notification:email_internal')->everyThirtyMinutes();
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
