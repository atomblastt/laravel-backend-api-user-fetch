<?php

return [
    'redis' => [
        'prefix' => [
            'hourly_record' => env('CUSTOM_CONFIG_REDIS_PREFIX_HOURLY_RECORD', ':hourly_record_'),
        ]
    ]
];
