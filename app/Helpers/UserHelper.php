<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Users\UserRepository;

class UserHelper {

    /**
     * calculate daily record
     * 
     * var $date string
     * 
     * return array
     */
    public static function calculateDailyRecord($date){
        
        $redis = Redis::connection('user');
        
        # get all key redis with date parameter
        $keys = $redis->keys(config('custom.redis.prefix.hourly_record') . $date . '*');

        # sum all data male & user in redis
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($keys as $key) {
            $key = str_replace('user', '', $key);
            $maleCount += $redis->hGet($key, 'male') ?? 0;
            $femaleCount += $redis->hGet($key, 'female') ?? 0;
        }

        # find the average for all ages
        $repository = new UserRepository();
        $maleAverageAge = $repository->getAvg(
            where: ['gender' => 'male'],
            avg: 'age'
        );
        $femaleAverageAge = $repository->getAvg(
            where: ['gender' => 'female'],
            avg: 'age'
        );

        return [
            'male_count' => $maleCount,
            'female_count' => $femaleCount,
            'male_avg_age' => number_format($maleAverageAge, 2, '.', ''),
            'female_avg_age' => number_format($femaleAverageAge, 2, '.', ''),
        ];
    }
}