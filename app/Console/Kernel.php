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
        $schedule->command('getCompletedTrips')->everyTwoMinutes();
        $schedule->command('dailyReport')->dailyAt('05:00'); //UTC time respective to EST
        $schedule->command('monthlyReport')->dailyAt('05:00'); //UTC time respective to EST
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
