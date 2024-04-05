<?php

namespace App\Console\Commands\Users;

use Illuminate\Console\Command;
use App\Services\Users\UserService;

class UserFetchDataCron extends Command
{
    protected $signature = 'cron:fetch-user-data-cron';

    protected $description = 'Fetch user data from external API and store into database';

    public function handle()
    {
        $service = new UserService();
        $process = $service->fetchAndProcessUserDataFromApi();
        if ($process) {
            $this->info('Fetch user data successfuly');
        } else {
            $this->error('Fetch user data failed');
        }
    }
}
