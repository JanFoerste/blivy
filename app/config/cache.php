<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'driver' => env('CACHE_DRIVER', 'file'),

    'redis_server' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0
    ],

    'key_prefix' => 'cache_'
];