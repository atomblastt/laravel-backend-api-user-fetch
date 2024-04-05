<?php

namespace App\Console\Commands\Users;

use Illuminate\Console\Command;
use App\Services\Users\UserService;
use App\Jobs\Users\UserDailyRecordJob;

class UserDailyRecordCron extends Command
{
    protected $signature = 'cron:daily-record-job';

    protected $description = 'Trigger job daily record';

    public function handle()
    {
        // UserDailyRecordJob::dispatch();
        UserDailyRecordJob::dispatch()->onQueue('queue_user_daily_record');
        $this->info('Job already to dispatch');
    }
}
