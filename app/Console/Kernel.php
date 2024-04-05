<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\Users\UserFetchDataCron;
use App\Console\Commands\Users\UserDailyRecordCron;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
	// $schedule->command('cron:fetch-user-data')->hourly();
        // $schedule->command('cron:daily-record-job')->dailyAt('23:59');
        $schedule->job(new UserFetchDataCron)->hourly();
        $schedule->job(new UserDailyRecordCron)->dailyAt('23:59');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
