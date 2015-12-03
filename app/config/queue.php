<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'driver' => 'redis',

    'key_prefix' => 'queue_',
    'failed_prefix' => 'job_failed_',

    'connections' =>[
        'redis' => [
            'class' => '\Blivy\Queue\Drivers\RedisQueueDriver',
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 1
        ]
    ]
];