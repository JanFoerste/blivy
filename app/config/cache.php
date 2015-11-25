<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'driver' => env('CACHE_DRIVER', 'file'),

    'providers' => [

        'redis' => [
            'class' => '\Blivy\Cache\RedisCacheProvider',

            'server' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => 0
            ],
        ],

        'file' => [
            'class' => '\Blivy\Cache\FileCacheProvider'
        ],
    ],

    'key_prefix' => 'cache_'
];