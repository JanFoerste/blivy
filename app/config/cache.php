<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'driver' => env('CACHE_DRIVER', 'file'),

    'providers' => [

        'redis' => [
            'class' => '\Blivy\Cache\RedisCacheProvider',

            'server' => \Blivy\Support\Config::get('database', 'connections.redis'),
        ],

        'file' => [
            'class' => '\Blivy\Cache\FileCacheProvider'
        ],
    ],

    'key_prefix' => 'cache_'
];