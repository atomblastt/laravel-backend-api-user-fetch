<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Users\UserRepository;

class UserService
{
    /**
     * constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(
        protected UserRepository $repository = new UserRepository(),
    ) {
    }

    /**
     * Get All
     *
     * @return Array
     */
    public function getAll($columns = '*')
    {
        return $this->repository->getAll($columns);
    }

    /**
     * Create or Update
     *
     * @return Model
     */
    public function updateOrCreate($params, $data)
    {
        $check = $this->repository->findBy(
            key: $params,
            connectionName: 'pgsql::write'
        );
        if ($check) {
            return $this->repository->updateBy($params, $data);
        }
        return $this->repository->create(array_merge($params, $data));
    }

    /**
     * Process data user
     */
    public function fetchAndProcessUserDataFromApi()
    {
        try {
            # set up redis connection to user
            $redis = Redis::connection('user');
            
            $today = now()->format('Y-m-d:H');
            
            Log::channel('daily_fetch_user_data')->info('=== CRON '.$today.' RUN ===');
            Log::channel('daily_fetch_user_data')->info($today);

            # define male & female users for redis
            $maleUsers = 0;
            $femaleUsers = 0;

            # fetch data from API using HTTP GET request.
            $apiUrl = config('api.random-user.url');
            $response = Http::get($apiUrl);

            # Check if the response is successful (HTTP status code 2xx)
            if ($response->successful()) {
                $users = $response->json()['results'];

                # processing data from cronjobs
                foreach ($users as $user) {
                    $uuid = $user['login']['uuid'];
                    
                    # create new or update data if uuid exist in table
                    $this->updateOrCreate(
                        params: ['uuid' => $uuid],
                        data: [
                            'gender' => $user['gender'],
                            'name' => $user['name'],
                            'location' => $user['location'],
                            'age' => $user['dob']['age']
                        ]
                    );

                    # count total user male & female before set to redis
                    if ($user['gender'] === 'male') {
                        $maleUsers++;
                    } elseif ($user['gender'] === 'female') {
                        $femaleUsers++;
                    }
                }

                # set data count male & female to Redis
                $redis->hSet(config('custom.redis.prefix.hourly_record') . $today, 'male', $maleUsers);
                $redis->hSet(config('custom.redis.prefix.hourly_record') . $today, 'female', $femaleUsers);

                Log::channel('daily_fetch_user_data')->info('Male in '.$today.' = '. $maleUsers);
                Log::channel('daily_fetch_user_data')->info('Female in '.$today.' = '. $femaleUsers);
                Log::channel('daily_fetch_user_data')->info('=== CRON '.$today.' CLOSE ===');

                return true;
            } else {
                # handle HTTP errors
                $statusCode = $response->status();

                # log the error message
                Log::channel('daily_fetch_user_data')->error("HTTP error encountered: Status code $statusCode");

                return false;
            }
        } catch (\Exception $e) {
            Log::channel('daily_fetch_user_data')->error($e->getMessage());
            throw $e;
        }
    }


    /**
     * Soft Delete By Id
     *
     * @param int $id
     *
     * @return Model|null
     */
    public function softDeleteById(int $id): ?Model
    {
        return $this->repository->softDeleteById($id);
    }
}