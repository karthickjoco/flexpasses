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
        $schedule->command('getProfiles')->everyTenMinutes();
        $schedule->command('getVechiles')->everyTenMinutes();
        $schedule->command('getOngoingTrips')->everyMinute();
        $schedule->command('getCompletedTrips')->everyFiveMinutes();
        $schedule->command('dailyReport')->daily();
        $schedule->command('monthlyReport')->daily();
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
