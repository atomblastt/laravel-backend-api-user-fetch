<?php

namespace App\Services\Users;

use Exception;
use Carbon\Carbon;
use App\Helpers\UserHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserDailyRecordRepository;

class UserDailyRecordService
{
    /**
     * constructor.
     *
     * @param UserDailyRecordRepository $repository
     */
    public function __construct(
        protected UserDailyRecordRepository $repository = new UserDailyRecordRepository(),
        protected UserRepository $userRepository = new UserRepository(),
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
     * Process user daily record from job
     */
    public function processUserDailyRecord()
    {
        try{
            $today = now()->format('Y-m-d');

            $dailyRecord = UserHelper::calculateDailyRecord($today);
            $data = [
                'date' => $today,
                'male_count' => $dailyRecord['male_count'],
                'female_count' => $dailyRecord['female_count'],
                'male_avg_age' => $dailyRecord['male_avg_age'],
                'female_avg_age' => $dailyRecord['female_avg_age'],
            ];

            $this->updateOrCreate(
                params: ['date' => $today],
                data: $data
            );
        } catch (\Exception $e) {
            Log::channel('user_daily_record')->info($e->getMessage());
        }
    }

    /**
     * decrement daily record
     */
    public function decrementDailyRecord($id)
    {
        try {
            $sessionLog = Str::random(10);
            Log::channel('listener_decrement_daily_record')->info('['.$sessionLog.'] === DECREMENT DAILY RECORD START ===');
            
            # get user data from trash data
            $userData = $this->userRepository->findByKey(
                key:['id' => $id],
                withTrashed: true,
            );
            $data = $userData->toArray();
            $data['created_at'] = $userData->created_at->format('Y-m-d H:i:s');

            # DECREMENT DATA IN REDIS
            $redis = Redis::connection('user');
            $redis->multi();
            $redisKey = config('custom.redis.prefix.hourly_record') . Carbon::parse($data['created_at'])->format('Y-m-d:H');
            $gender = $data['gender'];
            $redis->hIncrBy($redisKey, $gender, -1);
            $results = $redis->exec();
            
            # check transaction redis
            if ($results === false) {
                throw new Exception('Redis transaction failed');
            }
            
            # DECREMENT DATA IN DB
            $checkDailyRecord = $this->repository->findByKey(
                key: ['date' => Carbon::parse($data['created_at'])->format('Y-m-d')]
            );
            if ($checkDailyRecord) {
                Log::channel('listener_decrement_daily_record')->info('inner check daily record');

                $dataDailyRecord = UserHelper::calculateDailyRecord(Carbon::parse($data['created_at'])->format('Y-m-d'));
                Log::channel('listener_decrement_daily_record')->info(json_encode($dataDailyRecord));

                $this->repository->update(
                    id: $checkDailyRecord['id'],
                    attributes: $dataDailyRecord,
                );
            }
            
            Log::channel('listener_decrement_daily_record')->info('['.$sessionLog.']=== DECREMENT DAILY RECORD END ===');
            Log::channel('listener_decrement_daily_record')->info(' ');
        } catch (Exception $e) {
            # rollback transaction redis
            $redis->discard();
            Log::channel('listener_decrement_daily_record')->info($e->getMessage());
        }
    }
}